<?php

namespace Flower\BoardBundle\Service;


use Flower\ModelBundle\Entity\Board\TaskAttachment;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TaskService implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Upload user image.
     *
     * @param TaskAttachment $entity
     * @return TaskAttachment
     */
    public function uploadImage(TaskAttachment $entity)
    {

        /* the file property can be empty if the field is not required */
        if (null === $entity->getFile()) {
            return $entity;
        }

        $uploadBaseDir = $this->container->getParameter("uploads_base_dir");
        $uploadDir = $this->container->getParameter("task_attachment_dir");

        /* set the path property to the filename where you've saved the file */
        $filename = $entity->getFile()->getClientOriginalName();
        $extension = $entity->getFile()->getClientOriginalExtension();

        $imageName = md5($filename . time()) . '.' . $extension;

        $entity->setPath($uploadDir . $imageName);
        $entity->getFile()->move($uploadBaseDir . $uploadDir, $imageName);

        $entity->setFile(null);

        return $entity;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}