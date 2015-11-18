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
class BoardController extends FOSRestController
{
    public function getAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $boards = $em->getRepository('FlowerModelBundle:Board\Board')->findAll();

        $view = FOSView::create($boards, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function getByAccountAction(Request $request, $accountId)
    {
        $em = $this->getDoctrine()->getManager();
        $boards = $em->getRepository('FlowerModelBundle:Board\Board')->findBy(array("account" => $accountId) , array("updated" => "DESC"), 20);

        $view = FOSView::create($boards, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function getByProjectAction(Request $request, $projectId)
    {
        $em = $this->getDoctrine()->getManager();
        $boards = $em->getRepository('FlowerModelBundle:Board\Board')->findBy(array("project" => $accountId) , array("updated" => "DESC"), 20);

        $view = FOSView::create($boards, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }

    public function getByIdAction(Request $request, Board $board)
    {

        $view = FOSView::create($board, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('public_api'));
        return $this->handleView($view);
    }
}
