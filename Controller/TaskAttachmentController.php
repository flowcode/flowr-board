<?php

namespace Flower\BoardBundle\Controller;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Flower\BoardBundle\Form\Type\TaskAttachmentType;
use Flower\ModelBundle\Entity\Board\Task;
use Flower\ModelBundle\Entity\Board\TaskAttachment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * TaskAttachment controller.
 *
 * @Route("/task/attachment")
 */
class TaskAttachmentController extends Controller
{


    /**
     * Displays a form to create a new TaskAttachment entity.
     *
     * @Route("/{id}/new", name="taskAttachment_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Task $task)
    {
        $taskAttachment = new TaskAttachment();
        $taskAttachment->setTask($task);

        $form = $this->createForm(new TaskAttachmentType(), $taskAttachment);

        return array(
            'taskAttachment' => $taskAttachment,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new TaskAttachment entity.
     *
     * @Route("/create", name="taskAttachment_create")
     * @Method("POST")
     * @Template("FlowerBoardBundle:TaskAttachment:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $taskAttachment = new TaskAttachment();
        $form = $this->createForm(new TaskAttachmentType(), $taskAttachment);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $taskAttachment = $this->get('board.service.task')->uploadImage($taskAttachment);

            $em->persist($taskAttachment);
            $em->flush();

            return $this->redirect($this->generateUrl('task_show', array(
                'id' => $taskAttachment->getTask()->getId()
            )));
        }

        return array(
            'taskAttachment' => $taskAttachment,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TaskAttachment entity.
     *
     * @Route("/{id}/edit", name="taskAttachment_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(TaskAttachment $taskAttachment)
    {
        $editForm = $this->createForm(new TaskAttachmentType(), $taskAttachment, array(
            'action' => $this->generateUrl('taskAttachment_update', array('id' => $taskAttachment->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($taskAttachment->getId(), 'taskAttachment_delete');

        return array(
            'taskAttachment' => $taskAttachment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing TaskAttachment entity.
     *
     * @Route("/{id}/update", name="taskAttachment_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerBoardBundle:TaskAttachment:edit.html.twig")
     */
    public function updateAction(TaskAttachment $taskAttachment, Request $request)
    {
        $editForm = $this->createForm(new TaskAttachmentType(), $taskAttachment, array(
            'action' => $this->generateUrl('taskAttachment_update', array('id' => $taskAttachment->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('taskAttachment_show', array('id' => $taskAttachment->getId())));
        }
        $deleteForm = $this->createDeleteForm($taskAttachment->getId(), 'taskAttachment_delete');

        return array(
            'taskAttachment' => $taskAttachment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a TaskAttachment entity.
     *
     * @Route("/{id}/delete", name="taskAttachment_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(TaskAttachment $taskAttachment, Request $request)
    {
        $form = $this->createDeleteForm($taskAttachment->getId(), 'taskAttachment_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($taskAttachment);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('taskAttachment'));
    }

}
