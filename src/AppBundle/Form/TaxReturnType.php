<?php

namespace AppBundle\Form;

use AppBundle\Entity\EconomicActivity;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Taxpayer;
use AppBundle\Entity\TaxReturn;
use AppBundle\Entity\TaxReturnEconomicActivity;
use AppBundle\Repository\TaxReturnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxReturnType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->entityManager;

        /** @var TaxReturn $data */
        $data = $builder->getData();

        /** @var Settings $settings */
        $settings = $this->entityManager->getRepository(Settings::class)->find(1);

        $taxUnit = 0;

        if ($data instanceof TaxReturn && $data->getTaxUnit()) {
            $taxUnit = $data->getTaxUnit();
        } elseif ($settings instanceof Settings) {
            $taxUnit = $settings->getTaxUnit();
        }

        $builder
            ->add(
                'taxpayer',
                null,
                array(
                    'choice_label' => 'fullName',
                    'placeholder' => 'Select',
                    'attr' => array('class' => 'selectpicker'),
                    'disabled' => $builder->getMethod() === 'POST' ? false : true,
                )
            )
            ->add(
                'period',
                null,
                array(
                    'mapped' => false,
                    'disabled' => true,
                    'data' => $data instanceof TaxReturn && $data->getDate() instanceof \DateTime ? $data->getDate()->format('Y/m') : null,
                )
            )
            ->add('taxUnit', null, array('disabled' => true, 'data' => $taxUnit))
            ->add(
                'taxReturnEconomicActivity',
                CollectionType::class,
                array(
                    'entry_type' => TaxReturnEconomicActivityType::class,
                    'entry_options' => array('label' => false),
                )
            )
            ->add(
                'paymentMethod',
                null,
                array(
                    'choice_label' => 'name',
                    'placeholder' => 'Select',
                    'disabled' => $builder->getMethod() === 'PUT' ? false : true,
                    'attr' => array('class' => 'selectpicker'),
                )
            )
            ->add(
                'paymentMethodComment',
                TextareaType::class,
                array('disabled' => $builder->getMethod() === 'PUT' ? false : true)
            );

        $builder->get('taxpayer')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($em) {
                $data = $event->getData();

                /** @var Taxpayer $taxpayer */
                $taxpayer = $em->find(Taxpayer::class, $data);

                $taxReturnEconomicActivities = array();

                if ($taxpayer instanceof Taxpayer) {
                    /** @var EconomicActivity $economicActivity */
                    foreach ($taxpayer->getEconomicActivity() as $key => $economicActivity) {
                        $taxReturnEconomicActivity = (new TaxReturnEconomicActivity())
                            ->setEconomicActivity($economicActivity)
                            ->setAliquot($economicActivity->getAliquot())
                            ->setDeclaredAmount(0)
                            ->setMinimumTaxable($economicActivity->getMinimumTaxable());

                        $taxReturnEconomicActivities[] = $taxReturnEconomicActivity;
                    }
                }

                $event->getForm()->getParent()->add(
                    'period',
                    null,
                    array(
                        'mapped' => false,
                        'disabled' => true,
                        'data' => $this->getDate($taxpayer) instanceof \DateTime ? $this->getDate($taxpayer)->format('Y/m') : '',
                    )
                );

                $event->getForm()->getParent()->add(
                    'paymentMethod',
                    null,
                    array(
                        'choice_label' => 'name',
                        'placeholder' => 'Select',
                        'disabled' => $taxpayer instanceof Taxpayer ? false : true,
                        'attr' => array('class' => 'selectpicker'),
                    )
                );

                $event->getForm()->getParent()->add(
                    'paymentMethodComment',
                    TextareaType::class,
                    array('disabled' => $taxpayer instanceof Taxpayer ? false : true)
                );

                $event->getForm()->getParent()->add(
                    'taxReturnEconomicActivity',
                    CollectionType::class,
                    array(
                        'entry_type' => TaxReturnEconomicActivityType::class,
                        'entry_options' => array('label' => false),
                        'data' => $taxReturnEconomicActivities,
                    )
                );
            }
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                /** @var TaxReturn $data */
                $data = $event->getData();
                $form = $event->getForm();

                if (!$data->getDate()) {
                    $data->setDate($this->getDate($data->getTaxpayer()));
                }

                if (!$data->getTaxUnit()) {
                    $data->setTaxUnit($form->get('taxUnit')->getData());
                }

                if (!$data->getTaxFine() && $data->getDate()) {
                    $data->setTaxFine($this->getTaxFine($data->getDate()));
                }

                $data->getTaxReturnEconomicActivity()->forAll(
                    function ($key, TaxReturnEconomicActivity $taxReturnEconomicActivity) use ($data) {
                        $taxReturnEconomicActivity->setTaxReturn($data);
                    }
                );
            }
        );
    }

    /**
     * @param  Taxpayer $taxpayer
     *
     * @return \DateTime
     */
    public function getDate(Taxpayer $taxpayer = null)
    {
        if (!$taxpayer) {
            return null;
        }

        /** @var TaxReturnRepository $er */
        $er = $this->entityManager->getRepository(TaxReturn::class);

        /** @var TaxReturn $lastTaxReturn */
        $lastTaxReturn = $er->findOneBy(array('taxpayer' => $taxpayer), array('date' => 'DESC'));

        if ($lastTaxReturn instanceof TaxReturn) {
            $date = clone $lastTaxReturn->getDate();
            $date->modify('first day of next month midnight');

            if ($date < new \DateTime('first day of this month midnight')) {
                return $date;
            }

            return null;
        }

        if ($taxpayer->getStartDateTaxReturn() < new \DateTime('first day of this month midnight')) {
            return $taxpayer->getStartDateTaxReturn();
        }

        return null;
    }

    /**
     * @param \Datetime $date
     * @return float
     */
    public function getTaxFine(\DateTime $date = null)
    {
        if (!$date) {
            return 0;
        }

        /** @var Settings $settings */
        $settings = $this->entityManager->getRepository(Settings::class)->find(1);

        $deadline = clone $date;
        $deadline->modify('next month + 15 day midnight');

        if ($deadline > new \DateTime('now')) {
            return 0;
        }

        $deadline->modify('first day of next month midnight');

        if ($deadline > new \DateTime('now')) {
            return $settings->getTaxFine1();
        }

        return $settings->getTaxFine2();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\TaxReturn',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_taxreturn';
    }
}
