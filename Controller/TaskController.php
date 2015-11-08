<?php

namespace Flower\BoardBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\BoardBundle\Form\Type\TaskType;
use Flower\BoardBundle\Form\Type\TimeLogType;
use Flower\ModelBundle\Entity\Clients\Account;
use Flower\ModelBundle\Entity\Board\Board;
use Flower\ModelBundle\Entity\Project\Project;
use Flower\ModelBundle\Entity\Board\Task;
use Flower\ModelBundle\Entity\User\User;
use Flower\ModelBundle\Entity\Board\TaskStatus;
use Flower\ModelBundle\Entity\Board\History;
use Flower\ModelBundle\Entity\Board\TaskType as TaskType2;
use Flower\ModelBundle\Entity\Board\TimeLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Task controller.
 *
 * @Route("/task")
 */
class TaskController extends Controller
{

    /**
     * Lists all Project entities.
     *
     * @Route("/kanban/{id}", name="task_kanban", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function kanbanAction(Board $board, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $taskStatuses = $em->getRepository('FlowerModelBundle:Board\TaskStatus')->getKanbanStatuses();
        $taskRepo = $em->getRepository("FlowerModelBundle:Board\Task");

        $tasks = array();

        foreach ($taskStatuses as $taskStatus) {
            $status = array();
            $status["entity"] = $taskStatus;
            $status["tasks"] = $taskRepo->findByStatus($taskStatus->getId());
            $tasks[] = $status;
        }

        return array(
            'board' => $board,
            'tasks' => $tasks,
        );
    }

    /**
     * Lists all Task entities.
     *
     * @Route("/{id}", name="task", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Board $board, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Board\Task')->createQueryBuilder('t');
        $this->addQueryBuilderSort($qb, 'task');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'board' => $board,
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Task entity.
     *
     * @Route("/{id}/show", name="task_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Task $task)
    {
        $deleteForm = $this->createDeleteForm($task->getId(), 'task_delete');

        $historyEntries = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Board\History")->findBy(array("enitity_id" => $task->getId(), "type" => History::TYPE_TASK));
        $tasklogs = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Board\TimeLog")->findBy(array("task" => $task->getId()),array("spentOn" => "DESC"));
        $spent = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Board\TimeLog")->getSpentByTask($task);
        $account = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Clients\Account")->findByBoard($task->getBoard());
        return array(
            'spent' => $spent,
            'task' => $task,
            'account' => $account,
            'tasklogs' => $tasklogs,
            'history_entries' => $historyEntries,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/{id}/new", name="task_new", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function newAction(Board $board)
    {
        $task = new Task();
        $task->setBoard($board);
        $task->setCreator($this->getUser());
        $form = $this->createForm($this->get('form.type.task'), $task);

        return array(
            'board' => $board,
            'task' => $task,
            'form' => $form->createView(),
        );
    }
    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/account/{id}/new", name="task_new_to_account")
     * @Method("GET")
     * @Template("FlowerBoardBundle:Task:new.html.twig")
     */
    public function newToAccountAction(Account $account)
    {
        $task = new Task();
        $task->setType(TaskType2::TYPE_TASK);
        $task->setAccount($account);
        $task->setCreator($this->getUser());
        $form = $this->createForm($this->get('form.type.task'), $task);

        return array(
            'task' => $task,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/account/{id}/bug/new", name="task_new_bug_to_account")
     * @Method("GET")
     * @Template("FlowerBoardBundle:Task:new.html.twig")
     */
    public function newBugToAccountAction(Account $account)
    {
        $task = new Task();
        $task->setType(TaskType2::TYPE_BUG);
        $task->setAccount($account);
        $task->setCreator($this->getUser());
        $form = $this->createForm($this->get('form.type.task'), $task);

        return array(
            'task' => $task,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/project/{id}/new", name="task_new_to_project")
     * @Method("GET")
     * @Template("FlowerBoardBundle:Task:new.html.twig")
     */
    public function newToProjectAction(Project $project)
    {
        $task = new Task();
        $task->setType(TaskType2::TYPE_TASK);
        $task->setProject($project);
        $task->setCreator($this->getUser());
        $task->setAssignee($this->getUser());
        if($project->getAccount()){
            $task->setAccount($project->getAccount());
        }
        $form = $this->createForm($this->get('form.type.task'), $task);

        return array(
            'task' => $task,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Task entity.
     *
     * @Route("/project/{id}/bug/new", name="task_new_bug_to_project")
     * @Method("GET")
     * @Template("FlowerBoardBundle:Task:new.html.twig")
     */
    public function newBugToProjectAction(Project $project)
    {
        $task = new Task();
        $task->setType(TaskType2::TYPE_BUG);
        $task->setProject($project);
        $task->setCreator($this->getUser());
        $task->setAccount($project->getAccount());
        $form = $this->createForm($this->get('form.type.task'), $task);

        return array(
            'task' => $task,
            'form' => $form->createView(),
        );
    }

    /**
     *
     * @Route("/{id}/bulk_user", name="task_bulk_user", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function bulkSetAssigneeAction(User $user, Request $request)
    {
        $tasks = $request->query->get("tasks");
        if(!$tasks){
            return new JsonResponse(null, 403);
        }
        $em = $this->getDoctrine()->getManager();
        foreach ($tasks as $taskId) {
            $task = $em->getRepository('FlowerModelBundle:Board\Task')->find($taskId);
            $task->setAssignee($user);
            $em->flush();
        }
        return new JsonResponse(null, 200);
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/board\{id}/tasks/create", name="board_task_create")
     * @Method("POST")
     * @Template("FlowerBoardBundle:Board:taskNew.html.twig") //VER CAMBIO EN ANGULAR
     */
    public function taskProjectCreateAction(Board $board, Request $request)
    {
        $task = new Task();

        $form = $this->createForm(new TaskType(), $task);
        if ($form->handleRequest($request)->isValid()) {

            $task->setCreator($this->getUser());
            $task->setBoard($board);


            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirect($this->generateUrl('board_task_list', array('id' => $board->getId())));
        }

        return array(
            'task' => $task,
            'form' => $form->createView(),
        );
    }

    /**
     *
     * @Route("/{id}/bulk_status", name="task_bulk_status", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function bulkSetStatusAction(TaskStatus $taskStatus, Request $request)
    {
        $tasks = $request->query->get("tasks");
        if(!$tasks){
            return new JsonResponse(null, 403);
        }
        $em = $this->getDoctrine()->getManager();
        foreach ($tasks as $taskId) {
            $task = $em->getRepository('FlowerModelBundle:Board\Task')->find($taskId);
            $task->setStatus($taskStatus);
            $em->flush();
        }
        return new JsonResponse(null, 200);
    }
    /**
     * Creates a new Task entity.
     *
     * @Route("/changepos/{id}", name="task_changepos")
     * @Method("POST")
     */
    public function changeposAction(Task $task, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $task->setPosition($request->get('pos', 0));

        $em->flush();

        return new JsonResponse(null, 200);
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/create/{id}", name="task_create", requirements={"id"="\d+"})
     * @Method("POST")
     * @Template("FlowerBoardBundle:Task:new.html.twig")
     */
    public function createAction(Board $board, Request $request)
    {
        $task = new Task();
        $task->setBoard($board);
        $form = $this->createForm($this->get('form.type.task'), $task);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $task->setCreator($this->getUser());

            $em->persist($task);
            $em->flush();


            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'task_new' : 'task_show';

            return $this->redirectToRoute($nextAction, array("id" => $task->getId()));
        }

        return array(
            'task' => $task,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     * @Route("/{id}/edit", name="task_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Task $task)
    {
        $editForm = $this->createForm($this->get('form.type.task'), $task, array(
            'action' => $this->generateUrl('task_update', array('id' => $task->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($task->getId(), 'task_delete');

        $historyEntries = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Board\History")->findBy(array("enitity_id" => $task->getId(), "type" => History::TYPE_TASK));
        $tasklogs = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Board\TimeLog")->findBy(array("task" => $task->getId()),array("spentOn" => "DESC"));
        $spent = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Board\TimeLog")->getSpentByTask($task);
        $account = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Clients\Account")->findByBoard($task->getBoard());
        $project = $this->getDoctrine()->getManager()->getRepository("FlowerModelBundle:Project\Project")->findByBoard($task->getBoard());
        if(!$account && $project){
            $account = $project->getAccount();
        }
        return array(
            'project' => $project,
            'account' => $account,
            'spent' => $spent,
            'history_entries' => $historyEntries,
            'tasklogs' => $tasklogs,
            'task' => $task,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Task entity.
     *
     * @Route("/{id}/update", name="task_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerBoardBundle:Task:edit.html.twig")
     */
    public function updateAction(Task $task, Request $request)
    {
        $editForm = $this->createForm($this->get('form.type.task'), $task, array(
            'action' => $this->generateUrl('task_update', array('id' => $task->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('task_show', array('id' => $task->getId())));
        }
        $deleteForm = $this->createDeleteForm($task->getId(), 'task_delete');

        return array(
            'task' => $task,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="task_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('task', $field, $type);

        return $this->redirect($this->generateUrl('task'));
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
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Deletes a Task entity.
     *
     * @Route("/{id}/delete", name="task_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Task $task, Request $request)
    {
        $form = $this->createDeleteForm($task->getId(), 'task_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('task'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return Form
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
     * Displays a form to create a new TimeLog entity.
     *
     * @Route("/{id}/timelog/new", name="task_timelog_new")
     * @Method("GET")
     * @Template("FlowerBoardBundle:TimeLog:new.html.twig")
     */
    public function timelogNewAction(Task $task)
    {
        $timelog = new TimeLog();
        $timelog->setTask($task);
        $form = $this->createForm(new TimeLogType(), $timelog);

        return array(
            'timelog' => $timelog,
            'form' => $form->createView(),
        );
    }

}
