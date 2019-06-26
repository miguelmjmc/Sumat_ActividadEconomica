<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taxpayer;
use AppBundle\Entity\TaxReturn;
use AppBundle\Form\TaxReturnType;
use AppBundle\Repository\TaxReturnRepository;
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
        /** @var TaxReturnRepository $er */
        $er = $this->getDoctrine()->getRepository(TaxReturn::class);

        $taxReturns = $er->findAllDeclared();

        $data = array('data' => array());

        /** @var TaxReturn $taxReturn */
        foreach ($taxReturns as $taxReturn) {

            $parameters = array(
                'suffix' => 'declaración',
                'grammaticalGender' => 'f',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('tax_return_modal', array('id' => $taxReturn->getId())),
                'managePath' => '#',
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

        if ('POST' === $request->getMethod()) {
            $data = $form->getData();

            if ($data instanceof TaxReturn) {
                if ($data->getTaxpayer() instanceof Taxpayer) {
                    $form = $this->createForm(TaxReturnType::class, $data, $parameters);

                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        /** @var TaxReturn $data */
                        $data = $form->getData();
                        $data->setDeclaredAt(new \DateTime());

                        $em = $this->getDoctrine()->getManager();
                        $em->flush();

                        return new Response('success');
                    }
                }
            }

        } else {
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                if ('DELETE' === $request->getMethod()) {
                    $em->remove($form->getData());
                }

                $em->flush();

                return new Response('success');
            }
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
}
