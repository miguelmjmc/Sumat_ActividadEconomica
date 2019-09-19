<?php

namespace AppBundle\Form;

use AppBundle\Entity\Taxpayer;
use AppBundle\Form\Events\UploadFileSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
        /** @var Taxpayer $data */
        $data = $builder->getData();

        $taxReturn = $data instanceof Taxpayer ? $data->getTaxReturn()->count() : 0;

        $economicActivityClass = 'selectpicker';
        $startTaxReturnClass = 'datepicker-year-month';

        if ('GET' === $builder->getMethod() || 'DELETE' === $builder->getMethod()) {
            $economicActivityClass .= ' hidden';
        }

        if ('POST' === $builder->getMethod() || 'PUT' === $builder->getMethod()) {
            $startTaxReturnClass .= ' white';
        }

        $builder
            ->add('rif', null, array('attr' => array('class' => 'rif')))
            ->add('name', null, array('label' => 'Business name'))
            ->add('address', TextareaType::class)
            ->add('email')
            ->add('phone', null, array('attr' => array('class' => 'phone')))
            ->add(
                'startTaxReturn',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'format' => 'yyyy/MM',
                    'html5' => false,
                    'attr' => array('class' => $taxReturn === 0 ? $startTaxReturnClass : '', 'readonly' => true),
                    'disabled' => $taxReturn === 0 ? false : true,
                )
            )
            ->add(
                'paymentMethod',
                null,
                array('choice_label' => 'name', 'label' => 'Payment methods', 'attr' => array('class' => 'selectpicker'))
            )
            ->add(
                'economicActivity',
                null,
                array('choice_label' => 'fullName', 'attr' => array('class' => $economicActivityClass))
            );

        $builder->addEventSubscriber(new UploadFileSubscriber());
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
