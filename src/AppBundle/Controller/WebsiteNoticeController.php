<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WebsiteNotice;
use AppBundle\Form\WebsiteNoticeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/system/cms/websiteNotice")
 */
class WebsiteNoticeController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="website_notice")
     */
    public function indexAction()
    {
        return $this->render('manager/website_notice.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/list/websiteNotice", name="website_notice_list")
     */
    public function websiteNoticeListAction()
    {
        $websiteNotices = $this->getDoctrine()->getRepository(WebsiteNotice::class)->findAll();

        $data = array('data' => array());

        /** @var WebsiteNotice $websiteNotice */
        foreach ($websiteNotices as $websiteNotice) {

            $parameters = array(
                'suffix' => 'noticia',
                'actions' => array('show', 'edit', 'delete'),
                'grammaticalGender' => 'f',
                'path' => $this->generateUrl('website_notice_modal', array('id' => $websiteNotice->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $websiteNotice->getLastUpdate()->format('Y/m/d H:i:s'),
                $websiteNotice->getTitle(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param WebsiteNotice $websiteNotice
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/websiteNotice/{id}", name="website_notice_modal", defaults={"id": "null"})
     */
    public function websiteNoticeModalAction(Request $request, WebsiteNotice $websiteNotice = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(WebsiteNoticeType::class, $websiteNotice, $parameters);

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
            'suffix' => 'Noticia',
            'grammaticalGender' => 'f',
            'action' => $this->generateUrl('website_notice_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
