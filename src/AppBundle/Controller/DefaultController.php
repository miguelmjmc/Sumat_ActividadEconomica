<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/construction/{section}", name="under_construction", defaults={"section": null})
     */
    public function constructionAction()
    {
        return $this->render('@App/base/under_construction.html.twig');
    }
}
