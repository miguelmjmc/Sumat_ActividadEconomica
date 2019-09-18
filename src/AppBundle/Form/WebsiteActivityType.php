<?php

namespace AppBundle\Form;

use AppBundle\Form\Events\UploadFileSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('startDate', DateType::class,
                array(
                    'widget' => 'single_text',
                    'format' => 'yyyy/MM/dd',
                    'html5' => false,
                    'attr' => array('class' => 'datepicker', 'readonly' => true),
                ))
            ->add('endDate', DateType::class,
                array(
                    'widget' => 'single_text',
                    'format' => 'yyyy/MM/dd',
                    'html5' => false,
                    'attr' => array('class' => 'datepicker', 'readonly' => true),
                ));

        $builder->addEventSubscriber(new UploadFileSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\WebsiteActivity',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_websiteactivity';
    }
}
