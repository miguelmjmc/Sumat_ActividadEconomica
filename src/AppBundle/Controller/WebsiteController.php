<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taxpayer;
use AppBundle\Entity\WebsiteActivity;
use AppBundle\Entity\WebsiteMainSlide;
use AppBundle\Entity\WebsiteNotice;
use AppBundle\Entity\WebsiteRelatedPage;
use AppBundle\Entity\WebsiteSettings;
use AppBundle\Entity\WebsiteVideo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebsiteController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="website")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $settings= $em->find(WebsiteSettings::class, 1);
        $slides = $em->getRepository(WebsiteMainSlide::class)->findAll();
        $relatedPages = $em->getRepository(WebsiteRelatedPage::class)->findAll();
        $notices = $em->getRepository(WebsiteNotice::class)->findAll();
        $videos = $em->getRepository(WebsiteVideo::class)->findAll();
        $activities = $em->getRepository(WebsiteActivity::class)->findAll();

        return $this->render(
            'website/index.html.twig',
            array(
                'settings' => $settings,
                'slides' => $slides,
                'relatedPages' => $relatedPages,
                'notices' => $notices,
                'videos' => $videos,
                'activities' => $activities,
            )
        );
    }

    /**
     * @Route("/notice/{id}", name="website_notice")
     *
     * @param WebsiteNotice $notice
     *
     * @return Response
     */
    public function noticeAction(WebsiteNotice $notice)
    {

        return $this->render('website/notice.html.twig', array('notice' => $notice));
    }
}
