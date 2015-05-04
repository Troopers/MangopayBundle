<?php

namespace AppVentus\MangopayBundle\Form;

use Symfony\Component\Form\AbstractType;
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
            ->add('cardNumber', 'text', array(
                'constraints' => array(new NotBlank()),
                'label' => 'appventus_mangopay.card_number.label',
                'attr' => array(
                    'data-id' => 'appventus_mangopay_card_number',
                    'placeholder' => 'appventus_mangopay.card_number.placeholder',
                ),
                'mapped' => false
            ))
            ->add('cardHolder', 'text', array(
                'constraints' => array(new NotBlank()),
                'label' => 'appventus_mangopay.card_holder.label',
                'attr' => array(
                    'data-id' => 'appventus_mangopay_card_holder',
                    'placeholder' => 'appventus_mangopay.card_holder.placeholder',
                ),
                'mapped' => false
            ))
            ->add('ccv', 'integer', array(
                'constraints' => array(new NotBlank()),
                'label' => 'appventus_mangopay.card_ccv.label',
                'attr' => array(
                    'data-id' => 'appventus_mangopay_ccv',
                    'placeholder' => 'appventus_mangopay.ccv.placeholder',
                ),
                'mapped' => false
            ))
            ->add('cardExpiryMonth', 'choice', array(
                'constraints' => array(new NotBlank()),
                'label' => 'appventus_mangopay.card_expiry_month.label',
                'choices' => array(
                    "01" => "01",
                    "02" => "02",
                    "03" => "03",
                    "04" => "04",
                    "05" => "05",
                    "06" => "06",
                    "07" => "07",
                    "08" => "08",
                    "09" => "09",
                    "10" => "10",
                    "11" => "11",
                    "12" => "12",
                ),
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

            $builder->add('cardExpiryYear', 'choice', array(
                'constraints' => array(new NotBlank()),
                'choices' => $years,
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
    public function getName()
    {
        return 'appventus_mangopaybundle_card_type';
    }
}
