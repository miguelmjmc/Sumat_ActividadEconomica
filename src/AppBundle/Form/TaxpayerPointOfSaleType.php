<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxpayerPointOfSaleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serial')
            ->add('brand')
            ->add(
                'taxpayerBankAccount',
                null,
                array(
                    'choice_label' => 'number',
                    'placeholder' => 'Select',
                    'attr' => array('class' => 'selectpicker'),
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('ba')
                            ->where('ba.taxpayer = :taxpayer')
                            ->setParameter(':taxpayer', $options['taxpayer']);
                    },
                )
            )
            ->add('comment');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\TaxpayerPointOfSale',
                'taxpayer' => null,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_taxpayerpointofsale';
    }
}
