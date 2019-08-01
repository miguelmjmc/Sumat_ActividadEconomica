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
            );

        $builder->get('taxpayer')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($em) {
                /** @var  TaxReturn $data */
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

                $data->getTaxReturnEconomicActivity()->forAll(
                    function ($key, TaxReturnEconomicActivity $taxReturnEconomicActivity) use ($data) {
                        $taxReturnEconomicActivity->setTaxReturn($data);
                    }
                );
            }
        );
    }

    /**
     * @return \DateTime
     */
    public function getDate(Taxpayer $taxpayer = null)
    {
        if (null === $taxpayer) {
            return null;
        }

        /** @var TaxReturnRepository $er */
        $er = $this->entityManager->getRepository(TaxReturn::class);

        /** @var TaxReturn $lastTaxReturn */
        $lastTaxReturn = $er->findOneBy(array('taxpayer' => $taxpayer), array('date' => 'DESC'));

        $now = new \DateTime('now');

        if ($lastTaxReturn instanceof TaxReturn) {
            $date = (new \DateTime($lastTaxReturn->getDate()->format('Y/m/d')))->modify('+1 month');

            if (((int)$date->format('Y') <= (int)$now->format('Y')) && ((int)$date->format('m') < (int)$now->format('m'))) {
                return $date;
            }

            return null;
        }

        if (((int)$taxpayer->getStartDateTaxReturn()->format('Y') <= (int)$now->format('Y')) && ((int)$taxpayer->getStartDateTaxReturn()->format('m') < (int)$now->format('m'))) {
            return $taxpayer->getStartDateTaxReturn();
        }

        return null;
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
