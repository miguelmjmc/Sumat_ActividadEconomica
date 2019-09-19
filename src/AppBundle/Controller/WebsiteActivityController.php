<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WebsiteActivity;
use AppBundle\Form\WebsiteActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/system/cms/websiteActivity")
 */
class WebsiteActivityController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="website_activity")
     */
    public function indexAction()
    {
        return $this->render('system/website_activity.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/list/websiteActivity", name="website_activity_list")
     */
    public function websiteActivityListAction()
    {
        $websiteActivities = $this->getDoctrine()->getRepository(WebsiteActivity::class)->findAll();

        $data = array('data' => array());

        /** @var WebsiteActivity $websiteActivity */
        foreach ($websiteActivities as $websiteActivity) {

            $parameters = array(
                'suffix' => 'actividad',
                'grammaticalGender' => 'f',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('website_activity_modal', array('id' => $websiteActivity->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $websiteActivity->getLastUpdate()->format('Y/m/d H:i:s'),
                $websiteActivity->getTitle(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param WebsiteActivity $websiteActivity
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/websiteActivity/{id}", name="website_activity_modal", defaults={"id": "null"})
     */
    public function websiteActivityModalAction(Request $request, WebsiteActivity $websiteActivity = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(WebsiteActivityType::class, $websiteActivity, $parameters);

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
            'suffix' => 'actividad',
            'grammaticalGender' => 'f',
            'action' => $this->generateUrl('website_activity_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
