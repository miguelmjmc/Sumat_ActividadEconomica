<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WebsiteRelatedPage;
use AppBundle\Form\WebsiteRelatedPageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/system/cms/websiteRelatedPage")
 */
class WebsiteRelatedPageController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="website_related_page")
     */
    public function indexAction()
    {
        return $this->render('system/website_related_page.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/list/websiteRelatedPage", name="website_related_page_list")
     */
    public function websiteRelatedPageListAction()
    {
        $websiteRelatedPages = $this->getDoctrine()->getRepository(WebsiteRelatedPage::class)->findAll();

        $data = array('data' => array());

        /** @var WebsiteRelatedPage $websiteRelatedPage */
        foreach ($websiteRelatedPages as $websiteRelatedPage) {

            $parameters = array(
                'suffix' => 'enlace de interés',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('website_related_page_modal', array('id' => $websiteRelatedPage->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $websiteRelatedPage->getLastUpdate()->format('Y/m/d H:i:s'),
                $websiteRelatedPage->getTitle(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param WebsiteRelatedPage $websiteRelatedPage
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/websiteRelatedPage/{id}", name="website_related_page_modal", defaults={"id": "null"})
     */
    public function websiteRelatedPageModalAction(Request $request, WebsiteRelatedPage $websiteRelatedPage = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(WebsiteRelatedPageType::class, $websiteRelatedPage, $parameters);

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
            'suffix' => 'Enlace de Interés',
            'action' => $this->generateUrl('website_related_page_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
