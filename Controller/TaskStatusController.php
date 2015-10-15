<?php

namespace Flower\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Flower\CoreBundle\Form\Type\TaskStatusType;
use Flower\ModelBundle\Entity\TaskStatus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * TaskStatus controller.
 *
 * @Route("/taskstatus")
 */
class TaskStatusController extends Controller
{

    /**
     * Lists all TaskStatus entities.
     *
     * @Route("/", name="taskstatus")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:TaskStatus')->createQueryBuilder('t');
        $this->addQueryBuilderSort($qb, 'taskstatus');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);

        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a TaskStatus entity.
     *
     * @Route("/{id}/show", name="taskstatus_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(TaskStatus $taskstatus)
    {
        $deleteForm = $this->createDeleteForm($taskstatus->getId(), 'taskstatus_delete');

        return array(
            'taskstatus' => $taskstatus,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new TaskStatus entity.
     *
     * @Route("/new", name="taskstatus_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $taskstatus = new TaskStatus();
        $form = $this->createForm(new TaskStatusType(), $taskstatus);

        return array(
            'taskstatus' => $taskstatus,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new TaskStatus entity.
     *
     * @Route("/create", name="taskstatus_create")
     * @Method("POST")
     * @Template("FlowerCoreBundle:TaskStatus:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $taskstatus = new TaskStatus();
        $form = $this->createForm(new TaskStatusType(), $taskstatus);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($taskstatus);
            $em->flush();

            return $this->redirect($this->generateUrl('taskstatus_show', array('id' => $taskstatus->getId())));
        }

        return array(
            'taskstatus' => $taskstatus,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TaskStatus entity.
     *
     * @Route("/{id}/edit", name="taskstatus_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(TaskStatus $taskstatus)
    {
        $editForm = $this->createForm(new TaskStatusType(), $taskstatus, array(
            'action' => $this->generateUrl('taskstatus_update', array('id' => $taskstatus->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($taskstatus->getId(), 'taskstatus_delete');

        return array(
            'taskstatus' => $taskstatus,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing TaskStatus entity.
     *
     * @Route("/{id}/update", name="taskstatus_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:TaskStatus:edit.html.twig")
     */
    public function updateAction(TaskStatus $taskstatus, Request $request)
    {
        $editForm = $this->createForm(new TaskStatusType(), $taskstatus, array(
            'action' => $this->generateUrl('taskstatus_update', array('id' => $taskstatus->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('taskstatus_show', array('id' => $taskstatus->getId())));
        }
        $deleteForm = $this->createDeleteForm($taskstatus->getId(), 'taskstatus_delete');

        return array(
            'taskstatus' => $taskstatus,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="taskstatus_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('taskstatus', $field, $type);

        return $this->redirect($this->generateUrl('taskstatus'));
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
     * Deletes a TaskStatus entity.
     *
     * @Route("/{id}/delete", name="taskstatus_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(TaskStatus $taskstatus, Request $request)
    {
        $form = $this->createDeleteForm($taskstatus->getId(), 'taskstatus_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($taskstatus);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('taskstatus'));
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

}
