<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taxpayer;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/system")
 */
class DefaultController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_FISCAL', $user)) {
            $this->render('system/index.html.twig');
        }

        $taxpayers = $this->getDoctrine()->getRepository(Taxpayer::class)->findAll();

        $taxpayerTotals = array(
            'total' => 0,
            'solvent' => 0,
            'gracePeriod' => 0,
            'insolvent' => 0,
        );

        /** @var Taxpayer $taxpayer */
        foreach ($taxpayers as $taxpayer) {
            $taxpayerTotals['total']++;

            switch ($taxpayer->getStatusCode()) {
                case 0:
                    $taxpayerTotals['insolvent']++;
                    break;

                case 1:
                    $taxpayerTotals['gracePeriod']++;
                    break;

                case 2:
                    $taxpayerTotals['solvent']++;
                    break;
            }
        }

        return $this->render('system/index.html.twig', array('taxpayerTotals' => $taxpayerTotals));
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
