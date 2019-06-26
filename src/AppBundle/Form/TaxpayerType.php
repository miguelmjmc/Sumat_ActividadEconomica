<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxpayerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $class = 'selectpicker';

        if ('GET' === $builder->getMethod() || 'DELETE' === $builder->getMethod()) {
            $class .= ' hidden';
        }

        $builder
            ->add('rif', null, array('attr' => array('class' => 'rif')))
            ->add('name', null, array('label' => 'Business name'))
            ->add('address', TextareaType::class)
            ->add('email')
            ->add('phone', null, array('attr' => array('class' => 'phone')))
            ->add('economicActivity', null, array('choice_label' => 'fullName', 'attr' => array('class' => $class)));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Taxpayer',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_taxpayer';
    }
}
