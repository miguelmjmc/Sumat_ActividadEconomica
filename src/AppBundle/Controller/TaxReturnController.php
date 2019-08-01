<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Settings;
use AppBundle\Entity\TaxReturn;
use AppBundle\Form\TaxReturnType;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/taxReturn")
 */
class TaxReturnController extends Controller
{
    /**
     * @Route("/", name="tax_return")
     */
    public function indexAction()
    {
        return $this->render('tax_return.html.twig');
    }

    /**
     * @Route("/list/taxpayer", name="tax_return_list")
     */
    public function taxReturnListAction()
    {
        $taxReturns = $this->getDoctrine()->getRepository(TaxReturn::class)->findAll();

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
                $taxReturn->getTaxpayer()->getRif(),
                $taxReturn->getTaxpayer()->getName(),
                $taxReturn->getDate()->format('Y/m'),
                '',
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param TaxReturn $taxReturn
     * @param string $id
     *
     * @return Response
     *
     * @Route("/modal/taxReturn/{id}", name="tax_return_modal", defaults={"id": "null"})
     */
    public function taxReturnModalAction(Request $request, TaxReturn $taxReturn = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(TaxReturnType::class, $taxReturn, $parameters);

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
            'suffix' => 'declaración',
            'grammaticalGender' => 'f',
            'action' => $this->generateUrl('tax_return_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal_lg.html.twig', $parameters);
    }

    /**
     * @param TaxReturn $taxReturn
     *
     * @return Response
     *
     * @Route("/pdf/taxReturn/{id}/invoice", name="tax_return_invoice")
     */
    public function taxReturnInvoiceAction(TaxReturn $taxReturn)
    {
        $settings = $this->getDoctrine()->getManager()->find(Settings::class, 1);

        $html = $this->renderView(
            'invoice.html.twig',
            array(
                'taxReturn' => $taxReturn,
                'settings' => $settings,
                'path' => $this->get('kernel')->getProjectDir(),
            )
        );

        return new PdfResponse(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            'Factura_'.$taxReturn->getInvoiceId().'.pdf',
            'application/pdf',
            'inline'
        );
    }
}
