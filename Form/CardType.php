<?php

namespace AppVentus\MangopayBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CardType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardNumber', TextType::class, array(
                    'constraints' => array(new NotBlank(['groups' => ['card']])),
                'label' => 'appventus_mangopay.card_number.label',
                'attr' => array(
                    'data-id' => 'appventus_mangopay_card_number',
                    'placeholder' => 'appventus_mangopay.card_number.placeholder',
                ),
                'mapped' => false
            ))
            ->add('cardHolder', TextType::class, array(
                    'constraints' => array(new NotBlank(['groups' => ['card']])),
                'label' => 'appventus_mangopay.card_holder.label',
                'attr' => array(
                    'data-id' => 'appventus_mangopay_card_holder',
                    'placeholder' => 'appventus_mangopay.card_holder.placeholder',
                ),
                'mapped' => false
            ))
            ->add('ccv', IntegerType::class, array(
                    'constraints' => array(new NotBlank(['groups' => ['card']])),
                'label' => 'appventus_mangopay.card_ccv.label',
                'attr' => array(
                    'data-id' => 'appventus_mangopay_ccv',
                    'placeholder' => 'appventus_mangopay.ccv.placeholder',
                ),
                'mapped' => false
            ))
            ->add('cardExpiryMonth', ChoiceType::class, array(
                    'constraints' => array(new NotBlank(['groups' => ['card']])),
                'label' => 'appventus_mangopay.card_expiry_month.label',
                'choices' => ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"],
                'choices_as_values' => true,
                'attr' => array(
                    'data-id' => 'appventus_mangopay_card_expiry_month',
                    'placeholder' => 'appventus_mangopay.card_expiry_month.placeholder',
                ),
                'mapped' => false
            ));

            $years = array();
            $range = range(date('y'), date('y')+15);
            foreach ($range as $year) {
                $year = str_pad($year, 2, "0", STR_PAD_LEFT);
                $years[$year] = $year;
                $year = (int) $year + 1;
            }

            $builder->add('cardExpiryYear', ChoiceType::class, array(
                'constraints' => array(new NotBlank(['groups' => ['card']])),
                'choices' => $years,
                'choices_as_values' => true,
                'attr' => array(
                    'data-id' => 'appventus_mangopay_card_expiry_year',
                    'placeholder' => 'appventus_mangopay.card_expiry_year.placeholder',
                ),
                'mapped' => false
            ))

            //
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'appventus_mangopaybundle_card_type';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appventus_mangopaybundle_card_type_form';
    }
}
