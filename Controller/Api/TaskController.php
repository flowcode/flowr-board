<?php

namespace Flower\BoardBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Flower\ModelBundle\Entity\Board\Board;

/**
 * Board controller.
 */
class TaskController extends FOSRestController
{
    public function getByBoardAction(Request $request, $board_id)
    {
        $em = $this->getDoctrine()->getManager();
        $boardStatuses = $em->getRepository('FlowerModelBundle:Board\TaskStatus')->getKanbanStatuses();
        $taskRepo = $em->getRepository("FlowerModelBundle:Board\Task");

        $tasks = array();

        foreach ($boardStatuses as $boardStatus) {
            $status = array();
            $status["entity"] = $boardStatus;
            $status["tasks"] = $taskRepo->findByStatus($boardStatus->getId(), $board_id);
            array_push($tasks, $status);
        }


        $view = FOSView::create($tasks, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('kanban'));
        return $this->handleView($view);
    }
}
