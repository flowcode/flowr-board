<?php

namespace Flower\BoardBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Board\Board;
use Flower\CoreBundle\Form\Type\BoardType;
use Doctrine\ORM\QueryBuilder;
/**
 * Board controller.
 *
 * @Route("/board")
 */
class BoardController extends Controller
{
    /**
     * Lists all Board entities.
     *
     * @Route("/", name="board")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Board\Board')->createQueryBuilder('b');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Board entity.
     *
     * @Route("/{id}/show", name="board_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Board $board)
    {
        $deleteForm = $this->createDeleteForm($board->getId(), 'board_delete');

//        return $this->redirect($this->generateUrl('board_task_kanban', array('id' => $board->getId())));
        $editForm = $this->createForm($this->get("form.type.board"), $board, array(
            'action' => $this->generateUrl('board_update', array('id' => $board->getid())),
            'method' => 'PUT',
        ));

        return array(
            'board' => $board,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Lists all Board entities.
     *
     * @Route("/{id}/tasks/kanban", name="board_task_kanban")
     * @Method("GET")
     * @Template()
     */
    public function tasksKanbanAction(Board $board)
    {
        $em = $this->getDoctrine()->getManager();
        $projStatuses = $em->getRepository('FlowerModelBundle:Board\TaskStatus')->getKanbanStatuses();
        $taskRepo = $em->getRepository("FlowerModelBundle:Board\Task");

        $tasks = array();

        foreach ($projStatuses as $projStatus) {
            $status = array();
            $status["entity"] = $projStatus;
            $status["tasks"] = $taskRepo->findByStatusByPos($projStatus->getId(), $board->getId());
            $tasks[] = $status;
        }
        $account = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Clients\Account")->findByBoard($board);
        $opportunity = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Clients\Opportunity")->findByBoard($board);
        $project = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Project\Project")->findByBoard($board);
        return array(
            'board_opportunity' => $opportunity,
            'board_project' => $project,
            'board_account' => $account,
            'board' => $board,
            'tasks' => $tasks,
        );
    }
    private function addFilter($qb, $filter, $field){
        if($filter && count($filter) > 0){    
            if( implode(",", $filter) != ""){
                $filterAux = array();
                $nullFilter = "";
                foreach ($filter as $element) {
                    if($element == "-1"){
                        $nullFilter = " OR  (".$field." is NULL)";
                    }else{
                        $filterAux[] = $element;
                    }
                }
                if(count($filterAux) > 0){
                    $qb->andWhere(" ( ".$field." in (:".str_replace(".","_",$field)."_param) ".$nullFilter." )")->setParameter(str_replace(".","_",$field)."_param", $filterAux);
                }else{
                    $qb->andWhere(" ( 1 = 2 ".$nullFilter." )");
                }
            }
        }
    }
    /**
     * Lists all Board entities.
     *
     * @Route("/{id}/tasks/list", name="board_task_list")
     * @Method("GET")
     * @Template()
     */
    public function tasksListAction(Board $board, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Board\Task')->createQueryBuilder('t');
        $qb->join("t.status","s");
        $qb->where("t.board = :board")->setParameter("board", $board->getId());

        $this->addQueryBuilderSort($qb, 'board');
        $statusFilter = $request->query->get('statusFilter');
        $this->addFilter($qb,$statusFilter,"t.status");
        $assigneeFilter = $request->query->get('assigneeFilter');
        $this->addFilter($qb,$assigneeFilter,"t.assignee");
        
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        $statuses = $em->getRepository('FlowerModelBundle:Board\TaskStatus')->findAll();
        $users = $em->getRepository('FlowerModelBundle:User\User')->findAll();

        $account = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Clients\Account")->findByBoard($board);
        $opportunity = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Clients\Opportunity")->findByBoard($board);
        $project = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Project\Project")->findByBoard($board);
        return array(
            'board_opportunity' => $opportunity,
            'board_project' => $project,
            'board_account' => $account,
            'assigneeFilter' => $assigneeFilter,
            'statusFilter' => $statusFilter,
            'users' => $users,
            'statuses' => $statuses,
            'board' => $board,
            'paginator' => $paginator,
        );
    }

    /**
     * Displays a form to create a new Board entity.
     *
     * @Route("/new", name="board_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $board = new Board();
        $form = $this->createForm($this->get("form.type.board"), $board, array(
            'action' => $this->generateUrl('board_create'),
            'method' => 'POST',
        ));

        return array(
            'board' => $board,
            'form'   => $form->createView(),
        );
    }



    /**
     * Creates a new Board entity.
     *
     * @Route("/create", name="board_create")
     * @Method("POST")
     * @Template("FlowerBoardBundle:Board:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $board = new Board();
        $form = $this->createForm($this->get("form.type.board"), $board, array(
            'action' => $this->generateUrl('board_create'),
            'method' => 'POST',
        ));
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($board);
            $em->flush();

            return $this->redirect($this->generateUrl('board_show', array('id' => $board->getId())));
        }

        return array(
            'board' => $board,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Board entity.
     *
     * @Route("/{id}/edit", name="board_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Board $board)
    {
        $editForm = $this->createForm($this->get("form.type.board"), $board, array(
            'action' => $this->generateUrl('board_update', array('id' => $board->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($board->getId(), 'board_delete');

        return array(
            'board' => $board,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Board entity.
     *
     * @Route("/{id}/update", name="board_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerBoardBundle:Board:edit.html.twig")
     */
    public function updateAction(Board $board, Request $request)
    {
        $editForm = $this->createForm($this->get("form.type.board"), $board, array(
            'action' => $this->generateUrl('board_update', array('id' => $board->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('board_show', array('id' => $board->getId())));
        }
        $deleteForm = $this->createDeleteForm($board->getId(), 'board_delete');

        return array(
            'board' => $board,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Board entity.
     *
     * @Route("/{id}/delete", name="board_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Board $board, Request $request)
    {
        $form = $this->createDeleteForm($board->getId(), 'board_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($board);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('board'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Save order.
     *
     * @Route("/order/{board}/{field}/{type}", name="board_list_sort")
     */
    public function sortAction(Board $board, $field, $type)
    {
        $this->setOrder('board', $field, $type);

        return $this->redirect($this->generateUrl('board_task_list',array("id" => $board->getId())));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            if (strpos($order['field'], '.') !== FALSE){
                $qb->orderBy($order['field'], $order['type']);
            }else{
                $qb->orderBy($alias . '.' . $order['field'], $order['type']);
            }            
        }
    }

}
