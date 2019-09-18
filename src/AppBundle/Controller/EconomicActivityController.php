<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EconomicActivity;
use AppBundle\Form\EconomicActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/system/admin/economicActivity")
 */
class EconomicActivityController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="economic_activity")
     */
    public function indexAction()
    {
        return $this->render('manager/economic_activity.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/list/economicActivity", name="economic_activity_list")
     */
    public function economicActivityListAction()
    {
        $economicActivities = $this->getDoctrine()->getRepository(EconomicActivity::class)->findAll();

        $data = array('data' => array());

        /** @var EconomicActivity $economicActivity */
        foreach ($economicActivities as $economicActivity) {

            $parameters = array(
                'suffix' => 'actividad economica',
                'grammaticalGender' => 'f',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('economic_activity_modal', array('id' => $economicActivity->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $economicActivity->getLastUpdate()->format('Y/m/d H:i:s'),
                $economicActivity->getCode(),
                $economicActivity->getName(),
                $economicActivity->getAliquotFormatted(),
                $economicActivity->getMinimumTaxableFormatted(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param EconomicActivity $economicActivity
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/economicActivity/{id}", name="economic_activity_modal", defaults={"id": "null"})
     */
    public function taxpayerModalAction(Request $request, EconomicActivity $economicActivity = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(EconomicActivityType::class, $economicActivity, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== $economicActivity->getTaxpayer()->count() || 0 !== $economicActivity->getTaxReturnEconomicActivity()->count()) {
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
            'suffix' => 'Actividad Economica',
            'grammaticalGender' => 'f',
            'action' => $this->generateUrl('economic_activity_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
