<?php

namespace AppBundle\Form\Events;

use AppBundle\Entity\Settings;
use AppBundle\Entity\Taxpayer;
use AppBundle\Entity\TaxReturn;
use AppBundle\Entity\TaxReturnEconomicActivity;
use AppBundle\Form\TaxReturnEconomicActivityType;
use AppBundle\Repository\TaxReturnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaxReturnSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::SUBMIT => 'onSubmit',
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        /** @var Settings $settings */
        $settings = $this->entityManager->getRepository(Settings::class)->find(1);

        $taxUnit = 0;

        if ($settings instanceof Settings) {
            $taxUnit = $settings->getTaxUnit();
        }

        $form = $event->getForm();

        $form
            ->add(
                'taxpayer',
                null,
                array(
                    'choice_label' => 'fullName',
                    'placeholder' => 'Select',
                    'attr' => array('class' => 'selectpicker'),
                    'disabled' => $form->getConfig()->getMethod() === 'POST' ? false : true,
                )
            )
            ->add('period', null, array('mapped' => false, 'disabled' => true))
            ->add('taxUnit', null, array('disabled' => true, 'data' => $taxUnit))
            ->add(
                'taxReturnEconomicActivity',
                CollectionType::class,
                array(
                    'entry_type' => TaxReturnEconomicActivityType::class,
                    'entry_options' => array('label' => false),
                )
            );
    }

    public function onSubmit(FormEvent $event)
    {
        /** @var  TaxReturn $data */
        $data = $event->getData();
        $form = $event->getForm();

        if ($data->getTaxpayer() instanceof Taxpayer && null === $data->getId()) {
            /** @var Settings $settings */
            $settings = $this->entityManager->getRepository(Settings::class)->find(1);

            $taxUnit = 0;

            if ($settings instanceof Settings) {
                $taxUnit = $settings->getTaxUnit();
            }

            /** @var TaxReturnRepository $er */
            $er = $this->entityManager->getRepository(TaxReturn::class);

            $taxReturnCollection = $er->findBy(array('taxpayer' => $data->getTaxpayer(), 'declaredAt' => null));

            foreach ($taxReturnCollection as $taxReturn) {
                $this->entityManager->remove($taxReturn);
            }

            $this->entityManager->flush();

            $taxReturn = (new TaxReturn())
                ->setTaxpayer($data->getTaxpayer())
                ->setTaxUnit($taxUnit)
                ->setDate(new \DateTime());


            foreach ($data->getTaxpayer()->getEconomicActivity() as $key => $economicActivity) {
                $taxReturnEconomicActivity = (new TaxReturnEconomicActivity())
                    ->setEconomicActivity($economicActivity)
                    ->setAliquot($economicActivity->getAliquot())
                    ->setDeclaredAmount(0)
                    ->setMinimumTaxable($economicActivity->getMinimumTaxable())
                    ->setTaxReturn($taxReturn);

                $taxReturn->addTaxReturnEconomicActivity($taxReturnEconomicActivity);
            }

            $this->entityManager->persist($taxReturn);
            $this->entityManager->flush();

            $event->setData($taxReturn);
        }
    }
}
