<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PaymentMethod;
use AppBundle\Form\PaymentMethodType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/system/admin/paymentMethod")
 */
class PaymentMethodController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/", name="payment_method")
     */
    public function indexAction()
    {
        return $this->render('manager/payment_method.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/list/paymentMethod", name="payment_method_list")
     */
    public function paymentMethodListAction()
    {
        $paymentMethods = $this->getDoctrine()->getRepository(PaymentMethod::class)->findAll();

        $data = array('data' => array());

        /** @var PaymentMethod $paymentMethod */
        foreach ($paymentMethods as $paymentMethod) {

            $parameters = array(
                'suffix' => 'método de pago',
                'actions' => array('show', 'edit', 'delete'),
                'path' => $this->generateUrl('payment_method_modal', array('id' => $paymentMethod->getId())),
            );

            $btn = $this->renderView('@App/base/table_btn.html.twig', $parameters);

            $data['data'][] = array(
                $paymentMethod->getLastUpdate()->format('Y/m/d H:i:s'),
                $paymentMethod->getName(),
                $btn,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     * @param PaymentMethod $paymentMethod
     * @param int $id
     *
     * @return Response
     *
     * @Route("/modal/paymentMethod/{id}", name="payment_method_modal", defaults={"id": "null"})
     */
    public function paymentMethodModalAction(Request $request, PaymentMethod $paymentMethod = null, $id = null)
    {
        $parameters = array('method' => $request->getMethod());

        if ('GET' === $request->getMethod() || 'DELETE' === $request->getMethod()) {
            $parameters['attr'] = array('readonly' => true);
        }

        $form = $this->createForm(PaymentMethodType::class, $paymentMethod, $parameters);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('DELETE' === $request->getMethod()) {
                if (0 !== $paymentMethod->getTaxReturn()->count()) {
                    return new Response('error');
                }

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
            'suffix' => 'Método de Pago',
            'action' => $this->generateUrl('payment_method_modal', array('id' => $id)),
            'method' => $request->getMethod(),
        );

        return $this->render('@App/base/modal.html.twig', $parameters);
    }
}
