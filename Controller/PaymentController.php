<?php

namespace AppVentus\MangopayBundle\Controller;

use AppVentus\MangopayBundle\AppVentusMangopayEvents;
use AppVentus\MangopayBundle\Entity\Order;
use AppVentus\MangopayBundle\Event\OrderEvent;
use AppVentus\MangopayBundle\Event\PreAuthorisationEvent;
use AppVentus\MangopayBundle\OrderEvents;
use MangoPay\CardRegistration;
use MangoPay\PayIn;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Manage payment
 * @Route("/payment")
 */
class PaymentController extends Controller
{

    /**
     * Create a payment
     *
     * @Route("/new/{order}", name="appventus_mangopaybundle_payment_new", defaults={"order" = null, "type" = "card"})
     **/
    public function newAction(Request $request, $order)
    {
        $orderRepository = $this->getDoctrine()->getManager()
            ->getRepository($this->container->getParameter('appventus_mangopay.order.class'));
        $order = $orderRepository->findOneById($order);
        if (!$order instanceof Order) {
            throw $this->createNotFoundException('Order not found');
        }
        //create card form
        $form = $this->createForm('appventus_mangopaybundle_card_type');
        $form->handleRequest($request);

        if ($form->isValid()) {
            //find or create a mango user
            $mangoUser = $this->container->get('appventus_mangopay.user_helper')
                ->findOrCreateMangoUser($this->getUser());
            //create a cardRegistration
            $callback = $this->container->get('appventus_mangopay.payment_helper')
                ->prepareCardRegistrationCallback($mangoUser, $order);
            //return js callback
            return new JsonResponse($callback);
        }

        return $this->render(
            'AppVentusMangopayBundle::cardPayment.html.twig',
            array(
                'form' => $form->createView(),
                'order' => $order,
            )
        );
    }

    /**
     * @param Request     $request     The request
     * @param Reservation $reservation The reservation
     * @param integer     $cardId      The cardId
     *
     * This method is called by paymentAction callback, with the authorized cardId as argument.
     * It creates a PreAuthorisation with reservation price, and store its id in the Reservation.
     * When the owner will accept the reservation, we will be able to fetch the PreAuthorisation and create the PayIn
     *
     * @Route("/finalize/{orderId}/{cardId}", name="appventus_mangopaybundle_payment_finalize")
     * @return JsonResponse return json
     */
    public function paymentFinalizeAction(Request $request, $orderId, $cardId)
    {

        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository($this->container->getParameter('appventus_mangopay.order.class'));
        $order = $orderRepository->findOneById($orderId);

        $data = $request->get('data');
        $errorCode = $request->get('errorCode');

        $paymentHelper = $this->container->get('appventus_mangopay.payment_helper');
        $updatedCardRegister = $paymentHelper->updateCardRegistration($cardId, $data, $errorCode);

        // Handle error
        if ((property_exists($updatedCardRegister, 'ResultCode')
                && $updatedCardRegister->ResultCode !== "000000")
                || $updatedCardRegister->Status == 'ERROR')
        {

            $errorMessage = $this->get('translator')->trans('mangopay.error.' . $updatedCardRegister->ResultCode);

            return new JsonResponse(array(
                'success' => false,
                'message' => $errorMessage
            ));
        }

        // Create a PayIn
        $preAuth = $paymentHelper->createPreAuthorisation($updatedCardRegister, $this->getUser(), $order);

        // Handle error
        if ((property_exists($preAuth, 'Code') && $preAuth->Code !== 200) || $preAuth->Status == 'FAILED') {

            $errorMessage = $this->get('translator')->trans('mangopay.error.' . $preAuth->ResultCode);

            return new JsonResponse(array(
                'success' => false,
                'message' => $errorMessage
            ));
        }
        // Handle secure mode
        if (property_exists($preAuth, 'SecureModeNeeded') && $preAuth->SecureModeNeeded == 1) {
            return new JsonResponse(array(
                'success' => true,
                'redirect' => $preAuth->SecureModeRedirectURL
            ));
        }

        // store payin transaction
        $event = new PreAuthorisationEvent($order, $preAuth);
        $this->get('event_dispatcher')->dispatch(AppVentusMangopayEvents::UPDATE_CARD_PREAUTHORISATION, $event);

        $event = new OrderEvent($order);
        $this->get('event_dispatcher')->dispatch(OrderEvents::ORDER_CREATED, $event);

        $order->setStatus(Order::STATUS_PENDING);

        //Persist pending order
        $em->persist($order);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('appventus_mangopay.alert.pre_authorisation.success')
        );

        return new JsonResponse(array(
            'success' => true
        ));

    }

    /**
     * @param Request     $request     The request
     * @param Reservation $reservation The reservation
     *
     * This method is called by paymentFinalizeActionif 3dsecure is required. 3DSecure is needed when 250€ are reached
     *
     * @Route("/finalize-secure/{orderId}", name="appventus_mangopaybundle_payment_finalize_secure")
     * @return RedirectResponse
     */
    public function paymentFinalizeSecureAction(Request $request, $orderId)
    {

        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository($this->container->getParameter('appventus_mangopay.order.class'));
        $order = $orderRepository->findOneById($orderId);
        $mangopayApi = $this->container->get('appventus_mangopay.mango_api');

        $preAuthId = $request->get('preAuthorizationId');

        $preAuth = $mangopayApi->CardPreAuthorizations->Get($preAuthId);

        if ((property_exists($preAuth, 'Code') && $preAuth->Code !== 200) || $preAuth->Status != 'SUCCEEDED') {

            if (property_exists($preAuth, 'Code')) {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    $this->get('translator')->trans('mangopay.error.' . $preAuth->Code)
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
        $this->get('event_dispatcher')->dispatch(AppVentusMangopayEvents::UPDATE_CARD_PREAUTHORISATION, $event);

        $event = new OrderEvent($order);
        $this->get('event_dispatcher')->dispatch(OrderEvents::ORDER_CREATED, $event);

        $order->setStatus(Order::STATUS_PENDING);

        $em->persist($order);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('appventus_mangopay.alert.pre_authorisation.success')
        );

        return $this->redirect($this->get('appventus_mangopay.payment_helper')->generateSuccessUrl());
    }

    /**
     * @param Request $request The request
     *
     * This method shows the congratulations
     *
     * @Route("/success", name="appventus_mangopaybundle_payment_success")
     * @return Response
     */
    public function successAction(Request $request)
    {
        return $this->render(
            'AppVentusMangopayBundle::success.html.twig'
        );
    }
}
