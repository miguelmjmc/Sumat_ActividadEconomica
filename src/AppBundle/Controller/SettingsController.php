<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Settings;
use AppBundle\Form\SettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/settings")
 */
class SettingsController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="settings")
     */
    public function indexAction()
    {
        $settings = $this->getDoctrine()->getRepository(Settings::class)->find(1);

        $parameters = array('method' => 'GET', 'attr' => array('readonly' => true));

        $form = $this->createForm(SettingsType::class, $settings, $parameters);

        return $this->render('settings.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/modal/settings", name="settings_modal")
     */
    public function settingsModalAction(Request $request)
    {
        $settings = $this->getDoctrine()->getRepository(Settings::class)->find(1);

        if (null === $settings) {
            $method = 'POST';
        } else {
            $method = 'PUT';
        }

        $form = $this->createForm(SettingsType::class, $settings, array('method' => $method));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (null === $settings) {
                $em->persist($form->getData());
            }

            $em->flush();

            return new Response('success-reload');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'Configuraciones',
            'action' => $this->generateUrl('settings_modal'),
            'method' => $method,
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
