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
class TaskStatusController extends FOSRestController
{
    public function getAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $statuses = $em->getRepository('FlowerModelBundle:Board\TaskStatus')->findAll();
        $view = FOSView::create($statuses, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('kanban'));
        return $this->handleView($view);
    }
}
