<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taxpayer;
use AppBundle\Form\TaxpayerType;
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
     * @Route("/", name="taxpayer")
     */
    public function indexAction()
    {
        return $this->render('taxpayer.html.twig');
    }

    /**
     * @Route("/list/taxpayer", name="taxpayer_list")
     */
    public function taxpayerListAction()
    {
        $taxpayers = $this->getDoctrine()->getRepository(Taxpayer::class)->findAll();

        $data = array('data' => array());

        /** @var Taxpayer $taxpayer */
        foreach ($taxpayers as $taxpayer) {

            $parameters = array(
                'suffix' => 'contribuyente',
                'actions' => array('show', 'edit', 'delete', 'manage'),
                'path' => $this->generateUrl('taxpayer_modal', array('id' => $taxpayer->getId())),
                'managePath' => '#',
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $taxpayer->getLastUpdate()->format('Y/m/d H:i:s'),
                $taxpayer->getRif(),
                $taxpayer->getName(),
                '',
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
            'suffix' => 'contribuyente',
            'action' => $this->generateUrl('taxpayer_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
