<?php

namespace AppBundle\Controller;

use AppBundle\Entity\HistorySession;
use AppBundle\Entity\HistoryRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/history")
 */
class HistoryController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/session", name="history_session")
     */
    public function historySessionAction()
    {
        return $this->render('history_session.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/request", name="history_request")
     */
    public function historyRequestAction()
    {
        return $this->render('history_request.html.twig');
    }

    /**
     * @Route("/list/historySession", name="history_session_list")
     */
    public function historySessionListAction()
    {
        $histories = $this->getDoctrine()->getRepository(HistorySession::class)->findAll();

        $data = array('data' => array());

        /** @var HistorySession $history */
        foreach ($histories as $history) {
            $data['data'][] = array(
                $history->getDateLogin()->format('Y/m/d H:i:s'),
                $history->getDateLogout()->format('Y/m/d H:i:s'),
                $history->getUser()->getUsername(),
                $history->getIp(),
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/list/historyRequest", name="history_request_list")
     */
    public function historyRequestListAction()
    {
        $histories = $this->getDoctrine()->getRepository(HistoryRequest::class)->findAll();

        $data = array('data' => array());

        /** @var HistoryRequest $history */
        foreach ($histories as $history) {
            $data['data'][] = array(
                $history->getDate()->format('Y/m/d H:i:s'),
                $history->getUser()->getUsername(),
                $history->getUri(),
                $history->getMethod(),
                $history->getStatusCode(),
                $history->getIp(),
            );
        }

        return new JsonResponse($data);
    }
}
