<?php

namespace Troopers\MangopayBundle\Form;

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
            ->add('cardNumber', TextType::class, [
                    'constraints' => [new NotBlank(['groups' => ['card']])],
                'label'           => 'troopers_mangopay.card_number.label',
                'attr'            => [
                    'data-id'     => 'troopers_mangopay_card_number',
                    'placeholder' => 'troopers_mangopay.card_number.placeholder',
                ],
                'mapped' => false,
            ])
            ->add('cardHolder', TextType::class, [
                    'constraints' => [new NotBlank(['groups' => ['card']])],
                'label'           => 'troopers_mangopay.card_holder.label',
                'attr'            => [
                    'data-id'     => 'troopers_mangopay_card_holder',
                    'placeholder' => 'troopers_mangopay.card_holder.placeholder',
                ],
                'mapped' => false,
            ])
            ->add('ccv', IntegerType::class, [
                    'constraints' => [new NotBlank(['groups' => ['card']])],
                'label'           => 'troopers_mangopay.card_ccv.label',
                'attr'            => [
                    'data-id'     => 'troopers_mangopay_ccv',
                    'placeholder' => 'troopers_mangopay.ccv.placeholder',
                ],
                'mapped' => false,
            ])
            ->add('cardExpiryMonth', ChoiceType::class, [
                    'constraints'   => [new NotBlank(['groups' => ['card']])],
                'label'             => 'troopers_mangopay.card_expiry_month.label',
                'choices'           => ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
                'choices_as_values' => true,
                'attr'              => [
                    'data-id'     => 'troopers_mangopay_card_expiry_month',
                    'placeholder' => 'troopers_mangopay.card_expiry_month.placeholder',
                ],
                'mapped' => false,
            ]);

        $years = [];
        $range = range(date('y'), date('y') + 15);
        foreach ($range as $year) {
            $year = str_pad($year, 2, '0', STR_PAD_LEFT);
            $years[$year] = $year;
            $year = (int) $year + 1;
        }

        $builder->add('cardExpiryYear', ChoiceType::class, [
                'constraints'       => [new NotBlank(['groups' => ['card']])],
                'choices'           => $years,
                'choices_as_values' => true,
                'attr'              => [
                    'data-id'     => 'troopers_mangopay_card_expiry_year',
                    'placeholder' => 'troopers_mangopay.card_expiry_year.placeholder',
                ],
                'mapped' => false,
            ])

            //
;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'troopers_mangopaybundle_card_type';
    }
}
