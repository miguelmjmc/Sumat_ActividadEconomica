<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/system")
 */
class UserController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/admin/user/", name="user")
     */
    public function indexAction()
    {
        return $this->render('manager/user.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/admin/user/list/user", name="user_list")
     */
    public function userListAction()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $data = array('data' => array());

        /** @var User $user */
        foreach ($users as $user) {

            $parameters = array(
                'suffix' => 'usuario',
                'actions' => array('show', 'edit'),
                'path' => $this->generateUrl('user_modal', array('id' => $user->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $user->getLastUpdate()->format('Y/m/d H:i:s'),
                $user->getFullName(),
                $user->getUsername(),
                $user->getEmail(),
                $user->hasRole('ROLE_ADMIN') ? 'Administrador' : 'Operador',
                $user->getStatus(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param User $user
     * @param int $id
     *
     * @return Response
     *
     * @Route("/admin/user/modal/user/{id}", name="user_modal", defaults={"id": "null"})
     */
    public function userModalAction(Request $request, User $user = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(UserType::class, $user, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('POST' === $request->getMethod()) {
                $em->persist($form->getData());
            }

            $em->flush();

            return new Response('success');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'Usuario',
            'action' => $this->generateUrl('user_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }

    /**
     * @return Response
     *
     * @Route("/user/profile", name="user_profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();

        $parameters = array(
            'method' => 'GET',
            'attr' => array('readonly' => true),
        );

        $form = $this->createForm(UserType::class, $user, $parameters);

        return $this->render('manager/user_profile.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/user/modal/profile", name="user_profile_modal")
     */
    public function profileModalAction(Request $request)
    {
        $user = $this->getUser();

        $parameters = array('method' => 'PUT');

        $form = $this->createForm(UserType::class, $user, $parameters );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return new Response('success-reload');
        }

        $parameters = array(
            'form' => $form->createView(),
            'suffix' => 'perfil',
            'action' => $this->generateUrl('user_profile_modal'),
            'method' => 'PUT',
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
