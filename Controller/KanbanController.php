<?php

namespace Flower\BoardBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\CoreBundle\Form\Type\TaskType;
use Flower\CoreBundle\Form\Type\TimeLogType;
use Flower\ModelBundle\Entity\Board\History;
use Flower\ModelBundle\Entity\Board\Task;
use Flower\ModelBundle\Entity\Board\TaskType as TaskType2;
use Flower\ModelBundle\Entity\Board\TimeLog;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Task controller.
 *
 * @Route("/p/api/kanban")
 */
class KanbanController extends FOSRestController
{

    /**
     * Lists all Task entities.
     *
     * @Route("/tasks/list/filter/{task_filter_id}", name="kanban_tasks_list")
     * @Method("GET")
     */
    public function tasksAllAction(Request $request, $task_filter_id)
    {
        $em = $this->getDoctrine()->getManager();
        $taskRepo = $em->getRepository('FlowerModelBundle:Board\Task');

        $taskFilter = $em->getRepository('FlowerModelBundle:Board\TaskFilter')->find($task_filter_id);
        $filter = $this->get("board.service.task")->getTaskFilter($taskFilter->getFilter());

        $tasks = array();
        $tasks = $taskRepo->findByStatus(null, $filter);

        $view = FOSView::create($tasks, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('kanban'));
        return $this->handleView($view);
    }

    /**
     * Lists all Task entities.
     *
     * @Route("/tasks/kanban/filter/{task_filter_id}", name="kanban_tasks_kanban")
     * @Method("GET")
     */
    public function tasksAction(Request $request, $task_filter_id)
    {
        $em = $this->getDoctrine()->getManager();
        $boardStatuses = $em->getRepository('FlowerModelBundle:Board\TaskStatus')->getKanbanStatuses();

        $taskRepo = $em->getRepository('FlowerModelBundle:Board\Task');

        $taskFilter = $em->getRepository('FlowerModelBundle:Board\TaskFilter')->find($task_filter_id);
        $filter = $this->get("board.service.task")->getTaskFilter($taskFilter->getFilter());

        $tasks = array();
        foreach ($boardStatuses as $boardStatus) {
            $status = array();
            $status["entity"] = $boardStatus;
            $status["tasks"] = $taskRepo->findByStatus($boardStatus->getId(), $filter);
            array_push($tasks, $status);
        }

        $view = FOSView::create($tasks, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('kanban'));
        return $this->handleView($view);
    }

    /**
     * Lists all Task entities.
     *
     * @Route("/tasks/statuses/filter/{task_filter_id}", name="kanban_tasks_statuses")
     * @Method("GET")
     */
    public function tasksStatusesAction(Request $request, $task_filter_id)
    {
        $em = $this->getDoctrine()->getManager();
        $boardStatuses = $em->getRepository('FlowerModelBundle:Board\TaskStatus')->getKanbanStatuses();


        $view = FOSView::create($boardStatuses, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('kanban'));
        return $this->handleView($view);
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/updateTask/{id}", name="kanban_task_changepos")
     * @Method("POST")
     */
    public function updateTaskAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $positions = $request->get('positions');
        $this->get("flower.core.service.kanban.order")->kanbanOrderChanged($positions);
        return new JsonResponse(null, 200);
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/updateTaskExtended/{id}", name="kanban_task_update_extended")
     * @Method("POST")
     */
    public function updateTaskExtendedAction(Task $task, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $task->setName($request->get("name"));
        $task->setDescription($request->get("description"));
        $task->setEstimated($request->get("estimated"));
        $taskService = $this->get("flower.core.service.task");
        $taskService->update($task);

        return new JsonResponse($task, 200);
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/saveTask", name="kanban_task_save")
     * @Method("POST")
     */
    public function kanbanUpdateTaskAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $task = new Task();
        $task->setName($request->get("name"));
        $task->setDescription($request->get("description"));
        $statusArr = $request->get("status");

        $status = $em->getRepository('FlowerModelBundle:Board\TaskStatus')->find($statusArr['id']);
        $taskFilter = $em->getRepository('FlowerModelBundle:Board\TaskFilter')->find($request->get("task_filter_id"));
        $devTracker = $em->getRepository('FlowerModelBundle:Board\Tracker')->findOneBy(array('name' => 'development'));

        $filter = $this->get('board.service.task')->getTaskFilter($taskFilter->getFilter());

        if (isset($filter['board_id'])) {
            $board = $em->getRepository('FlowerModelBundle:Board\Board')->find($filter['board_id']);
            $task->setBoard($board);
        }

        if (isset($filter['project_id'])) {
            $project = $em->getRepository('FlowerModelBundle:Project\Project')->find($filter['project_id']);
            $task->setProject($project);
        }

        if (isset($filter['project_iteration_id'])) {
            $projectIteration = $em->getRepository('FlowerModelBundle:Project\ProjectIteration')->find($filter['project_iteration_id']);
            $task->setProjectIteration($projectIteration);
        }

        if (isset($filter['account_id'])) {
            $account = $em->getRepository('FlowerModelBundle:Board\Board')->find($filter['account_id']);
            $task->setAccount($account);
        }

        if (isset($filter['opportunity_id'])) {
            $opportunity = $em->getRepository('FlowerModelBundle:Clients\Opportunity')->find($filter['opportunity_id']);
            $task->setOpportunity($opportunity);
        }

        $task->setStatus($status);
        $task->setType($request->get("type", TaskType2::TYPE_TASK));
        $task->setCreator($this->getUser());
        $task->setAssignee($this->getUser());
        $task->setPosition($request->get("position"));
        $task->setTracker($devTracker);


        $em->persist($task);

        $em->flush();

        /*
        $taskService = $this->get("flower.core.service.task");
        $taskService->update($task);
        */

        $view = FOSView::create($task, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('kanban'));
        return $this->handleView($view);
    }

    /**
     * Creates a new Task entity.
     *
     * @Route("/create", name="task_create")
     * @Method("POST")
     * @Template("FlowerCoreBundle:Task:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(new TaskType(), $task);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $task->setCreator($this->getUser());

            $em->persist($task);
            $taskService = $this->get("flower.core.service.task");
            $taskService->update($task);


            $nextAction = $form->get('saveAndAdd')->isClicked() ? 'task_new' : 'task';

            return $this->redirectToRoute($nextAction);
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
        $editForm = $this->createForm(new TaskType(), $task, array(
            'action' => $this->generateUrl('task_update', array('id' => $task->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($task->getId(), 'task_delete');

        return array(
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
     * @Template("FlowerCoreBundle:Task:edit.html.twig")
     */
    public function updateAction(Task $task, Request $request)
    {
        $editForm = $this->createForm(new TaskType(), $task, array(
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
     * @param string $name session name
     * @param string $field field name
     * @param string $type sort type ("ASC"/"DESC")
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
     * @param string $name
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
            $taskService = $this->get("flower.core.service.task");
            $taskService->update($task);
        }

        return $this->redirect($this->generateUrl('task'));
    }

    /**
     * Create Delete form
     *
     * @param integer $id
     * @param string $route
     * @return Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Displays a form to create a new TimeLog entity.
     *
     * @Route("/{id}/timelog/new", name="task_timelog_new")
     * @Method("GET")
     * @Template("FlowerCoreBundle:TimeLog:new.html.twig")
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

    /**
     * Finds and displays a Task entity.
     *
     * @Route("/tasks/{id}", name="kanban_task_show_full", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Task $task)
    {

        $historyEntries = $this->getDoctrine()->getManager()->getRepository('FlowerModelBundle:Board\History')->findBy(array("enitity_id" => $task->getId(), "type" => History::TYPE_TASK));
        $tasklogs = $this->getDoctrine()->getManager()->getRepository('FlowerModelBundle:Board\TimeLog')->findBy(array("task" => $task->getId()), array("spentOn" => "DESC"));
        $spent = $this->getDoctrine()->getManager()->getRepository('FlowerModelBundle:Board\TimeLog')->getSpentByTask($task);

        if (is_null($task->getCreator()->getAvatar())) {
            $gravatarUrl = "http://www.gravatar.com/avatar/";
            $hash = md5(strtolower(trim($task->getCreator()->getEmail())));
            $avatarUrl = $gravatarUrl . $hash;
            $task->getCreator()->setAvatar($avatarUrl);
        }
        if (is_null($task->getAssignee()->getAvatar())) {
            $gravatarUrl = "http://www.gravatar.com/avatar/";
            $hash = md5(strtolower(trim($task->getAssignee()->getEmail())));
            $avatarUrl = $gravatarUrl . $hash;
            $task->getAssignee()->setAvatar($avatarUrl);
        }

        $taskArr = array(
            'spent' => $spent,
            'task' => $task,
            'tasklogs' => $tasklogs,
            'history_entries' => $historyEntries,
        );

        $view = FOSView::create($taskArr, Codes::HTTP_OK)->setFormat('json');
        $view->getSerializationContext()->setGroups(array('full'));
        return $this->handleView($view);
    }

}
