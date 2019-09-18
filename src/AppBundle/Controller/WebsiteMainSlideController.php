<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WebsiteMainSlide;
use AppBundle\Form\WebsiteMainSlideType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/system/cms/websiteMainSlide")
 */
class WebsiteMainSlideController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="website_main_slide")
     */
    public function indexAction()
    {
        return $this->render('manager/website_main_slide.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/list/websiteMainSlide", name="website_main_slide_list")
     */
    public function websiteMainSlideListAction()
    {
        $websiteMainSlides = $this->getDoctrine()->getRepository(WebsiteMainSlide::class)->findAll();

        $data = array('data' => array());

        /** @var WebsiteMainSlide $websiteMainSlide */
        foreach ($websiteMainSlides as $websiteMainSlide) {

            $parameters = array(
                'suffix' => 'diapositiva',
                'grammaticalGender' => 'f',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('website_main_slide_modal', array('id' => $websiteMainSlide->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $websiteMainSlide->getLastUpdate()->format('Y/m/d H:i:s'),
                $websiteMainSlide->getTitle(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param WebsiteMainSlide $websiteMainSlide
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/websiteMainSlide/{id}", name="website_main_slide_modal", defaults={"id": "null"})
     */
    public function websiteMainSlideModalAction(Request $request, WebsiteMainSlide $websiteMainSlide = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(WebsiteMainSlideType::class, $websiteMainSlide, $parameters);

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
            'suffix' => 'Dispositiva',
            'grammaticalGender' => 'f',
            'action' => $this->generateUrl('website_main_slide_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
