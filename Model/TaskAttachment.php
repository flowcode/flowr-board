<?php

namespace Flower\BoardBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use JMS\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * TaskAttachment
 *
 */
abstract class TaskAttachment
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"kanban", "full"})
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Task", inversedBy="attachments")
     * @JoinColumn(name="task_id", referencedColumnName="id")
     * */
    protected $task;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"full"})
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Groups({"full"})
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     * @Groups({"full"})
     */
    protected $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="added_on", type="datetime")
     */
    protected $addedOn;

    public function __construct()
    {
        $this->addedOn = new \DateTime();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return TaskStatus
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return TaskStatus
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set addedOn
     *
     * @param DateTime $addedOn
     * @return TimeLog
     */
    public function setAddedOn($addedOn)
    {
        $this->addedOn = $addedOn;

        return $this;
    }

    /**
     * Get addedOn
     *
     * @return DateTime
     */
    public function getAddedOn()
    {
        return $this->addedOn;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setTask($task)
    {
        $this->task = $task;
    }

    public function getTask()
    {
        return $this->task;
    }

}
