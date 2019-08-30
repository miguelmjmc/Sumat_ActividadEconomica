<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxpayerBankAccountType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', null, array('attr' => array('class' => 'bank-account')))
            ->add(
                'bankAccountType',
                null,
                array(
                    'choice_label' => 'name',
                    'placeholder' => 'Select',
                    'attr' => array('class' => 'selectpicker'),
                )
            )
            ->add('name', null, array('label' => 'Account holder'))
            ->add('dni')
            ->add('comment');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\TaxpayerBankAccount',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_taxpayerbankaccount';
    }
}
