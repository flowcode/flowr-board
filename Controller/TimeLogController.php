<?php

namespace Flower\BoardBundle\Controller;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Flower\BoardBundle\Form\Type\TimeLogType;
use Flower\ModelBundle\Entity\Board\TimeLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * TimeLog controller.
 *
 * @Route("/timelog")
 */
class TimeLogController extends Controller
{

    /**
     * Lists all TimeLog entities.
     *
     * @Route("/", name="timelog")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Board\TimeLog')->createQueryBuilder('t');
        $this->addQueryBuilderSort($qb, 'timelog');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a TimeLog entity.
     *
     * @Route("/{id}/show", name="timelog_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(TimeLog $timelog)
    {
        $deleteForm = $this->createDeleteForm($timelog->getId(), 'timelog_delete');

        return array(
            'timelog' => $timelog,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new TimeLog entity.
     *
     * @Route("/new", name="timelog_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $timelog = new TimeLog();
        $form = $this->createForm(new TimeLogType(), $timelog);

        return array(
            'timelog' => $timelog,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new TimeLog entity.
     *
     * @Route("/create/dinamic", name="timelog_create_dinamic")
     * @Method("POST")
     */
    public function createDinamic(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $taskArr = $request->get("task");
        $hours = $request->get("hours");
        $description = $request->get("description");


        $task = $em->getRepository("FlowerModelBundle:Board\Task")->find($taskArr['id']);
        if ($task && $hours && $description) {
            $timelog = new TimeLog();
            $timelog->setHours($hours);
            $timelog->setDescription($description);
            $timelog->setUser($this->getUser());
            $timelog->setSpentOn(new DateTime());
            $timelog->setTask($task);


            $em->persist($timelog);
            $em->flush();
            return new JsonResponse();
        }

        return new JsonResponse(array("message" => "invalid"), 500);
    }

    /**
     * Creates a new TimeLog entity.
     *
     * @Route("/create", name="timelog_create")
     * @Method("POST")
     * @Template("FlowerCoreBundle:TimeLog:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $timelog = new TimeLog();
        $form = $this->createForm(new TimeLogType(), $timelog);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $timelog->setUser($this->getUser());
            $timelog->setSpentOn(new DateTime());
            $em->persist($timelog);
            $em->flush();

            return $this->redirect($this->generateUrl('task_show', array('id' => $timelog->getTask()->getId())));
        }

        return array(
            'timelog' => $timelog,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TimeLog entity.
     *
     * @Route("/{id}/edit", name="timelog_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(TimeLog $timelog)
    {
        $editForm = $this->createForm(new TimeLogType(), $timelog, array(
            'action' => $this->generateUrl('timelog_update', array('id' => $timelog->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($timelog->getId(), 'timelog_delete');

        return array(
            'timelog' => $timelog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing TimeLog entity.
     *
     * @Route("/{id}/update", name="timelog_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:TimeLog:edit.html.twig")
     */
    public function updateAction(TimeLog $timelog, Request $request)
    {
        $editForm = $this->createForm(new TimeLogType(), $timelog, array(
            'action' => $this->generateUrl('timelog_update', array('id' => $timelog->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('timelog_show', array('id' => $timelog->getId())));
        }
        $deleteForm = $this->createDeleteForm($timelog->getId(), 'timelog_delete');

        return array(
            'timelog' => $timelog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="timelog_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('timelog', $field, $type);

        return $this->redirect($this->generateUrl('timelog'));
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
     * Deletes a TimeLog entity.
     *
     * @Route("/{id}/delete", name="timelog_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(TimeLog $timelog, Request $request)
    {
        $form = $this->createDeleteForm($timelog->getId(), 'timelog_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($timelog);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('timelog'));
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
     *
     * @Route("/export", name="timelog_export")
     * @Method("GET")
     */
    public function exportViewAction(Request $request)
    {
        $filters = $request->getSession()->get("timelog_report_filters");

        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('FlowerModelBundle:Board\TimeLog')->getAllQB($filters, $filters['from_date'], $filters['to_date']);
        $timelogs = $qb->getQuery()->getResult();

        $data = $this->get("board.service.report")->getDataExport($timelogs);
        $this->get("client.service.excelexport")->exportData($data, "Timelogs", "Registros de tiempos");

        die();
        return $this->redirectToRoute("timelog_report_index");
    }

}
