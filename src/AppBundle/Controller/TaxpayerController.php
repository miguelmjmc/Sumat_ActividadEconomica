<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taxpayer;
use AppBundle\Entity\TaxpayerBankAccount;
use AppBundle\Entity\TaxpayerBioPayment;
use AppBundle\Entity\TaxpayerPointOfSale;
use AppBundle\Entity\TaxReturn;
use AppBundle\Form\TaxpayerBankAccountType;
use AppBundle\Form\TaxpayerBioPaymentType;
use AppBundle\Form\TaxpayerPointOfSaleType;
use AppBundle\Form\TaxpayerType;
use AppBundle\Repository\TaxpayerBioPaymentRepository;
use AppBundle\Repository\TaxpayerPointOfSaleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/taxpayer")
 */
class TaxpayerController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="taxpayer")
     */
    public function indexAction()
    {
        return $this->render('taxpayer.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/list/taxpayer", name="taxpayer_list")
     */
    public function taxpayerListAction(Request $request)
    {
        switch ($request->query->get('filter', null)) {
            case 'solvent':
                $taxpayers = array();

                /** @var Taxpayer $taxpayer */
                foreach ($this->getDoctrine()->getRepository(Taxpayer::class)->findAll() as $taxpayer) {
                    if ($taxpayer->isSolvent()) {
                        $taxpayers[] = $taxpayer;
                    }
                }
                break;

            case 'gracePeriod':
                $taxpayers = array();

                /** @var Taxpayer $taxpayer */
                foreach ($this->getDoctrine()->getRepository(Taxpayer::class)->findAll() as $taxpayer) {
                    if ($taxpayer->isGracePeriod()) {
                        $taxpayers[] = $taxpayer;
                    }
                }
                break;

            case 'insolvent':
                $taxpayers = array();

                /** @var Taxpayer $taxpayer */
                foreach ($this->getDoctrine()->getRepository(Taxpayer::class)->findAll() as $taxpayer) {
                    if ($taxpayer->isInsolvent()) {
                        $taxpayers[] = $taxpayer;
                    }
                }
                break;

            default:
                $taxpayers = $this->getDoctrine()->getRepository(Taxpayer::class)->findAll();
                break;
        }

        $data = array('data' => array());

        /** @var Taxpayer $taxpayer */
        foreach ($taxpayers as $taxpayer) {

            $parameters = array(
                'suffix' => 'contribuyente',
                'actions' => array('manage'),
                'path' => $this->generateUrl('taxpayer_modal', array('id' => $taxpayer->getId())),
                'managePath' => $this->generateUrl('taxpayer_manage', array('id' => $taxpayer->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $taxpayer->getLastUpdate()->format('Y/m/d H:i:s'),
                $taxpayer->getRif(),
                $taxpayer->getName(),
                $taxpayer->getStatus(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param Taxpayer $taxpayer
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/taxpayer/{id}", name="taxpayer_modal", defaults={"id": "null"})
     */
    public function taxpayerModalAction(Request $request, Taxpayer $taxpayer = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(TaxpayerType::class, $taxpayer, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== count($taxpayer->getTaxReturn())) {
                    return new Response('error');
                }

                $em->remove($form->getData());
            }

            if ('POST' === $request->getMethod()) {
                $em->persist($form->getData());
            }

            $em->flush();

            return new Response('success');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'Contribuyente',
            'action' => $this->generateUrl('taxpayer_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }

    /**
     * @param Taxpayer $taxpayer
     *
     * @return Response
     *
     * @Route("/manage/{id}", name="taxpayer_manage")
     */
    public function taxpayerManageAction(Taxpayer $taxpayer)
    {
        $form = $this->createForm(TaxpayerType::class, $taxpayer, array('disabled' => true, 'method' => 'GET'));

        return $this->render(
            'taxpayer_manage.html.twig',
            array('taxpayer' => $taxpayer, 'form' => $form->createView())
        );
    }

    /**
     * @param Taxpayer $taxpayer
     * @return Response
     *
     * @Route("/list/taxpayer/{id}/taxReturn", name="taxpayer_tax_return_list")
     */
    public function taxReturnListAction(Taxpayer $taxpayer)
    {
        $taxReturns = $this->getDoctrine()->getRepository(TaxReturn::class)->findBy(array('taxpayer' => $taxpayer));

        $data = array('data' => array());

        /** @var TaxReturn $taxReturn */
        foreach ($taxReturns as $taxReturn) {

            $parameters = array(
                'suffix' => 'declaración',
                'grammaticalGender' => 'f',
                'actions' => array('show', 'edit', 'delete', 'pdf'),
                'path' => $this->generateUrl('tax_return_modal', array('id' => $taxReturn->getId())),
                'pdfTitle' => 'Ver factura',
                'pdfPath' => $this->generateUrl('tax_return_invoice', array('id' => $taxReturn->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $taxReturn->getLastUpdate()->format('Y/m/d H:i:s'),
                $taxReturn->getDate()->format('Y/m'),
                $taxReturn->getDeclaredAmountFormatted(),
                $taxReturn->getTotalAmountFormatted(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Taxpayer $taxpayer
     * @return Response
     *
     * @Route("/list/taxpayer/{id}/bankAccount", name="taxpayer_bank_account_list")
     */
    public function taxpayerBankAccountListAction(Taxpayer $taxpayer)
    {
        $taxpayerBankAccounts = $this->getDoctrine()->getRepository(TaxpayerBankAccount::class)->findBy(
            array('taxpayer' => $taxpayer)
        );

        $data = array('data' => array());

        /** @var TaxpayerBankAccount $taxpayerBankAccount */
        foreach ($taxpayerBankAccounts as $taxpayerBankAccount) {

            $parameters = array(
                'suffix' => 'cuenta bancaria',
                'grammaticalGender' => 'f',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl(
                    'taxpayer_bank_account_modal',
                    array('id' => $taxpayer->getId(), 'bankAccountId' => $taxpayerBankAccount->getId())
                ),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $taxpayerBankAccount->getNumber(),
                $taxpayerBankAccount->getBankAccountType()->getName(),
                $taxpayerBankAccount->getName(),
                $taxpayerBankAccount->getDni(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Taxpayer $taxpayer
     * @return Response
     *
     * @Route("/list/taxpayer/{id}/pointOfSale", name="taxpayer_point_of_sale_list")
     */
    public function taxpayerPointOfSaleListAction(Taxpayer $taxpayer)
    {
        /** @var TaxpayerPointOfSaleRepository $er */
        $er = $this->getDoctrine()->getRepository(TaxpayerPointOfSale::class);

        $taxpayerPointOfSales = $er->findByTaxpayer($taxpayer);

        $data = array('data' => array());

        /** @var TaxpayerPointOfSale $taxpayerPointOfSale */
        foreach ($taxpayerPointOfSales as $taxpayerPointOfSale) {

            $parameters = array(
                'suffix' => 'punto de venta',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl(
                    'taxpayer_point_of_sale_modal',
                    array('id' => $taxpayer->getId(), 'pointOfSaleId' => $taxpayerPointOfSale->getId())
                ),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $taxpayerPointOfSale->getSerial(),
                $taxpayerPointOfSale->getBrand(),
                $taxpayerPointOfSale->getTaxpayerBankAccount()->getNumber(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Taxpayer $taxpayer
     * @return Response
     *
     * @Route("/list/taxpayer/{id}/bioPayment", name="taxpayer_bio_payment_list")
     */
    public function taxpayerBioPaymentListAction(Taxpayer $taxpayer)
    {
        /** @var TaxpayerBioPaymentRepository $er */
        $er = $this->getDoctrine()->getRepository(TaxpayerBioPayment::class);

        $taxpayerBioPayments = $er->findByTaxpayer($taxpayer);

        $data = array('data' => array());

        /** @var TaxpayerBioPayment $taxpayerBioPayment */
        foreach ($taxpayerBioPayments as $taxpayerBioPayment) {

            $parameters = array(
                'suffix' => 'biopago',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl(
                    'taxpayer_bio_payment_modal',
                    array('id' => $taxpayer->getId(), 'bioPaymentId' => $taxpayerBioPayment->getId())
                ),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $taxpayerBioPayment->getSerial(),
                $taxpayerBioPayment->getBrand(),
                $taxpayerBioPayment->getTaxpayerBankAccount()->getNumber(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param Taxpayer $taxpayer
     * @param TaxpayerBankAccount $taxpayerBankAccount
     * @param int $id
     * @param int $bankAccountId
     *
     * @return Response
     *
     * @ParamConverter("taxpayer", options={"id" = "id"})
     * @ParamConverter("taxpayerBankAccount", options={"id" = "bankAccountId"})
     *
     * @Route("/modal/taxpayer/{id}/bankAccount/{bankAccountId}", name="taxpayer_bank_account_modal", defaults={"bankAccountId": "null"})
     */
    public function taxpayerBankAccountModalAction(Request $request, Taxpayer $taxpayer, TaxpayerBankAccount $taxpayerBankAccount = null, $id, $bankAccountId = null)
    {
        if ($taxpayerBankAccount instanceof TaxpayerBankAccount) {
            if ($taxpayerBankAccount->getTaxpayer()->getId() !== (int)$id) {
                return new Response('Bad Request ', '400');
            }
        }

        if (!$taxpayerBankAccount) {
            $taxpayerBankAccount = (new TaxpayerBankAccount())->setTaxpayer($taxpayer);
        }

        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(TaxpayerBankAccountType::class, $taxpayerBankAccount, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== count($taxpayerBankAccount->getTaxpayerPointOfSale()) || 0 !== count(
                        $taxpayerBankAccount->getTaxpayerBioPayment()
                    )) {
                    return new Response('error');
                }

                $em->remove($form->getData());
            }

            if ('POST' === $request->getMethod()) {
                $em->persist($form->getData());
            }

            $em->flush();

            return new Response('success');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'Cuenta Bancaria',
            'action' => $this->generateUrl(
                'taxpayer_bank_account_modal',
                array('id' => $id, 'bankAccountId' => $bankAccountId)
            ),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }

    /**
     * @param Request $request
     * @param TaxpayerPointOfSale $taxpayerPointOfSale
     * @param int $id
     * @param int $pointOfSaleId
     *
     * @return Response
     *
     * @ParamConverter("taxpayerPointOfSale", options={"id" = "pointOfSaleId"})
     *
     * @Route("/modal/taxpayer/{id}/pointOfSale/{pointOfSaleId}", name="taxpayer_point_of_sale_modal", defaults={"pointOfSaleId": "null"})
     */
    public function taxpayerPointOfSaleModalAction(Request $request, TaxpayerPointOfSale $taxpayerPointOfSale = null, $id, $pointOfSaleId = null)
    {
        if ($taxpayerPointOfSale instanceof TaxpayerPointOfSale) {
            if ($taxpayerPointOfSale->getTaxpayerBankAccount()->getTaxpayer()->getId() !== (int)$id) {
                return new Response('Bad Request', '400');
            }
        }

        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $parameters['taxpayer'] = $id;

        $form = $this->createForm(TaxpayerPointOfSaleType::class, $taxpayerPointOfSale, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                $em->remove($form->getData());
            }

            if ('POST' === $request->getMethod()) {
                $em->persist($form->getData());
            }

            $em->flush();

            return new Response('success');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'Punto de Venta',
            'action' => $this->generateUrl(
                'taxpayer_point_of_sale_modal',
                array('id' => $id, 'pointOfSaleId' => $pointOfSaleId)
            ),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }

    /**
     * @param Request $request
     * @param TaxpayerBioPayment $taxpayerBioPayment
     * @param int $id
     * @param int $bioPaymentId
     *
     * @return Response
     *
     * @ParamConverter("taxpayerBioPayment", options={"id" = "bioPaymentId"})
     *
     * @Route("/modal/taxpayer/{id}/bioPayment/{bioPaymentId}", name="taxpayer_bio_payment_modal", defaults={"bioPaymentId": "null"})
     */
    public function taxpayerBioPaymentModalAction(Request $request, TaxpayerBioPayment $taxpayerBioPayment = null, $id, $bioPaymentId = null)
    {
        if ($taxpayerBioPayment instanceof TaxpayerBioPayment) {
            if ($taxpayerBioPayment->getTaxpayerBankAccount()->getTaxpayer()->getId() !== (int)$id) {
                return new Response('Bad Request', '400');
            }
        }

        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $parameters['taxpayer'] = $id;

        $form = $this->createForm(TaxpayerBioPaymentType::class, $taxpayerBioPayment, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                $em->remove($form->getData());
            }

            if ('POST' === $request->getMethod()) {
                $em->persist($form->getData());
            }

            $em->flush();

            return new Response('success');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'Biopago',
            'action' => $this->generateUrl(
                'taxpayer_bio_payment_modal',
                array('id' => $id, 'bioPaymentId' => $bioPaymentId)
            ),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
