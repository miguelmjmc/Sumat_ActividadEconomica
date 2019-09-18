<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WebsiteVideo;
use AppBundle\Form\WebsiteVideoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/system/cms/websiteVideo")
 */
class WebsiteVideoController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="website_video")
     */
    public function indexAction()
    {
        return $this->render('manager/website_video.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/list/websiteVideo", name="website_video_list")
     */
    public function websiteVideoListAction()
    {
        $websiteVideos = $this->getDoctrine()->getRepository(WebsiteVideo::class)->findAll();

        $data = array('data' => array());

        /** @var WebsiteVideo $websiteVideo */
        foreach ($websiteVideos as $websiteVideo) {

            $parameters = array(
                'suffix' => 'video',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('website_video_modal', array('id' => $websiteVideo->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $websiteVideo->getLastUpdate()->format('Y/m/d H:i:s'),
                $websiteVideo->getTitle(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param WebsiteVideo $websiteVideo
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/websiteVideo/{id}", name="website_video_modal", defaults={"id": "null"})
     */
    public function websiteVideoModalAction(Request $request, WebsiteVideo $websiteVideo = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(WebsiteVideoType::class, $websiteVideo, $parameters);

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
            'suffix' => 'Video',
            'action' => $this->generateUrl('website_video_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
