<?php

namespace Troopers\MangopayBundle\Controller;

use MangoPay\CardRegistration;
use MangoPay\PayIn;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Troopers\MangopayBundle\Entity\Order;
use Troopers\MangopayBundle\Event\OrderEvent;
use Troopers\MangopayBundle\Event\PreAuthorisationEvent;
use Troopers\MangopayBundle\Form\CardType;
use Troopers\MangopayBundle\OrderEvents;
use Troopers\MangopayBundle\TroopersMangopayEvents;

/**
 * Manage payment.
 *
 * @Route("/payment")
 */
class PaymentController extends Controller
{
    /**
     * Create a payment.
     *
     * @Route("/new/{order}", name="troopers_mangopaybundle_payment_new", defaults={"order" = null, "type" = "card"})
     **/
    public function newAction(Request $request, $order)
    {
        $orderRepository = $this->getDoctrine()->getManager()
            ->getRepository($this->container->getParameter('troopers_mangopay.order.class'));
        $order = $orderRepository->findOneById($order);
        if (!$order instanceof Order) {
            throw $this->createNotFoundException('Order not found');
        }
        //create card form
        $form = $this->createForm(CardType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //find or create a mango user
            $mangoUser = $this->container->get('troopers_mangopay.user_helper')
                ->findOrCreateMangoUser($this->getUser());
            //create a cardRegistration
            $callback = $this->container->get('troopers_mangopay.payment_helper')
                ->prepareCardRegistrationCallback($mangoUser, $order);
            //return js callback
            return new JsonResponse($callback);
        }

        return $this->render(
            'TroopersMangopayBundle::cardPayment.html.twig',
            [
                'form'  => $form->createView(),
                'order' => $order,
            ]
        );
    }

    /**
     * @param Request     $request     The request
     * @param Reservation $reservation The reservation
     * @param int         $cardId      The cardId
     *
     * This method is called by paymentAction callback, with the authorized cardId as argument.
     * It creates a PreAuthorisation with reservation price, and store its id in the Reservation.
     * When the owner will accept the reservation, we will be able to fetch the PreAuthorisation and create the PayIn
     *
     * @Route("/finalize/{orderId}/{cardId}", name="troopers_mangopaybundle_payment_finalize")
     *
     * @return JsonResponse return json
     */
    public function paymentFinalizeAction(Request $request, $orderId, $cardId)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository($this->container->getParameter('troopers_mangopay.order.class'));
        $order = $orderRepository->findOneById($orderId);

        $data = $request->get('data');
        $errorCode = $request->get('errorCode');

        $paymentHelper = $this->container->get('troopers_mangopay.payment_helper');
        $updatedCardRegister = $paymentHelper->updateCardRegistration($cardId, $data, $errorCode);

        // Handle error
        if ((property_exists($updatedCardRegister, 'ResultCode')
                && $updatedCardRegister->ResultCode !== '000000')
            || $updatedCardRegister->Status == 'ERROR') {
            $errorMessage = $this->get('translator')->trans('mangopay.error.'.$updatedCardRegister->ResultCode);

            return new JsonResponse([
                'success' => false,
                'message' => $errorMessage,
            ]);
        }

        // Create a PayIn
        $preAuth = $paymentHelper->createPreAuthorisation($updatedCardRegister, $this->getUser(), $order);

        // Handle error
        if ((property_exists($preAuth, 'Code') && $preAuth->Code !== 200) || $preAuth->Status == 'FAILED') {
            $errorMessage = $this->get('translator')->trans('mangopay.error.'.$preAuth->ResultCode);

            return new JsonResponse([
                'success' => false,
                'message' => $errorMessage,
            ]);
        }
        // Handle secure mode
        if (property_exists($preAuth, 'SecureModeNeeded') && $preAuth->SecureModeNeeded == 1) {
            return new JsonResponse([
                'success'  => true,
                'redirect' => $preAuth->SecureModeRedirectURL,
            ]);
        }

        // store payin transaction
        $event = new PreAuthorisationEvent($order, $preAuth);
        $this->get('event_dispatcher')->dispatch(TroopersMangopayEvents::UPDATE_CARD_PREAUTHORISATION, $event);

        $event = new OrderEvent($order);
        $this->get('event_dispatcher')->dispatch(OrderEvents::ORDER_CREATED, $event);

        $order->setStatus(Order::STATUS_PENDING);

        //Persist pending order
        $em->persist($order);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('troopers_mangopay.alert.pre_authorisation.success')
        );

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * @param Request     $request     The request
     * @param Reservation $reservation The reservation
     *
     * This method is called by paymentFinalizeActionif 3dsecure is required. 3DSecure is needed when 250€ are reached
     *
     * @Route("/finalize-secure/{orderId}", name="troopers_mangopaybundle_payment_finalize_secure")
     *
     * @return RedirectResponse
     */
    public function paymentFinalizeSecureAction(Request $request, $orderId)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository($this->container->getParameter('troopers_mangopay.order.class'));
        $order = $orderRepository->findOneById($orderId);
        $mangopayApi = $this->container->get('troopers_mangopay.mango_api');

        $preAuthId = $request->get('preAuthorizationId');

        $preAuth = $mangopayApi->CardPreAuthorizations->Get($preAuthId);

        if ((property_exists($preAuth, 'Code') && $preAuth->Code !== 200) || $preAuth->Status != 'SUCCEEDED') {
            if (property_exists($preAuth, 'Code')) {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    $this->get('translator')->trans('mangopay.error.'.$preAuth->Code)
                );
            } else {
                $this->get('session')->getFlashBag()->add('error', $preAuth->ResultMessage);
            }

            if (!$request->headers->get('referer')) {
                return $this->redirect('/');
            }

            return $this->redirect($request->headers->get('referer'));
        }

        $event = new PreAuthorisationEvent($order, $preAuth);
        $this->get('event_dispatcher')->dispatch(TroopersMangopayEvents::UPDATE_CARD_PREAUTHORISATION, $event);

        $event = new OrderEvent($order);
        $this->get('event_dispatcher')->dispatch(OrderEvents::ORDER_CREATED, $event);

        $order->setStatus(Order::STATUS_PENDING);

        $em->persist($order);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('troopers_mangopay.alert.pre_authorisation.success')
        );

        return $this->redirect($this->get('troopers_mangopay.payment_helper')->generateSuccessUrl($orderId));
    }

    /**
     * @param Request $request The request
     * @param int     $orderId
     *
     * This method shows the congratulations
     *
     * @Route("/success/{orderId}", name="troopers_mangopaybundle_payment_success")
     *
     * @return Response
     */
    public function successAction(Request $request, $orderId)
    {
        return $this->render(
            'TroopersMangopayBundle::success.html.twig',
            ['orderId' => $orderId]
        );
    }
}
