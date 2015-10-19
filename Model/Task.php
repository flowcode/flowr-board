<?php

namespace Flower\BoardBundle\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;

/**
 * Task
 */
abstract class Task {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"kanban","search"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"kanban","search"})
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     * @Groups({"kanban","search"})
     */
    protected $position;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Groups({"kanban","search"})
     */
    protected $description;

    /**
     * @var float
     *
     * @ORM\Column(name="estimated_hours", type="float", nullable=true)
     */
    protected $estimated;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $creator;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups({"kanban","search"})
     * */
    protected $assignee;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Board\Board", inversedBy="tasks")
     * @JoinColumn(name="board_id", referencedColumnName="id")
     * */
    protected $board;

    /**
     * @Gedmo\SortableGroup
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Board\TaskStatus")
     * @JoinColumn(name="status_id", referencedColumnName="id")
     * @Groups({"kanban","search"})
     * */
    protected $status;

    /**
     * @ManyToOne(targetEntity="Tracker")
     * @JoinColumn(name="tracker_id", referencedColumnName="id")
     * */
    protected $tracker;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true)
     */
    protected $startDate;
    
    /**
     * @var DateTime
     *
     * @ORM\Column(name="dueDate", type="datetime", nullable=true)
     */
    protected $dueDate;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;
    
    public function __construct()
    {
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
     * @return Task
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
     * Set description
     *
     * @param string $description
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dueDate
     *
     * @param DateTime $dueDate
     * @return Task
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return DateTime 
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set created
     *
     * @param DateTime $created
     * @return Task
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param DateTime $updated
     * @return Task
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set board
     *
     * @param \Flower\ModelBundle\Entity\Board\Board $board
     * @return Task
     */
    public function setBoard(\Flower\ModelBundle\Entity\Board\Board $board = null)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board
     *
     * @return \Flower\ModelBundle\Entity\Board\Board 
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Set status
     *
     * @param \Flower\ModelBundle\Entity\Board\TaskStatus $status
     * @return Task
     */
    public function setStatus(\Flower\ModelBundle\Entity\Board\TaskStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Flower\ModelBundle\Entity\Board\TaskStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set tracker
     *
     * @param \Flower\ModelBundle\Entity\Board\Tracker $tracker
     * @return Task
     */
    public function setTracker(\Flower\ModelBundle\Entity\Board\Tracker $tracker = null)
    {
        $this->tracker = $tracker;

        return $this;
    }

    /**
     * Get tracker
     *
     * @return \Flower\ModelBundle\Entity\Board\Tracker 
     */
    public function getTracker()
    {
        return $this->tracker;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set creator
     *
     * @param \Flower\ModelBundle\Entity\User\User $creator
     * @return Task
     */
    public function setCreator(\Flower\ModelBundle\Entity\User\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Flower\ModelBundle\Entity\User\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set assignee
     *
     * @param \Flower\ModelBundle\Entity\User\User $assignee
     * @return Task
     */
    public function setAssignee(\Flower\ModelBundle\Entity\User\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return User 
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set estimated
     *
     * @param float $estimated
     * @return Task
     */
    public function setEstimated($estimated)
    {
        $this->estimated = $estimated;

        return $this;
    }

    /**
     * Get estimated
     *
     * @return float 
     */
    public function getEstimated()
    {
        return $this->estimated;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Task
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Task
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Task
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
}
