<?php

namespace Flower\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Board
 *
 * @ORM\Table(name="board")
 * @ORM\Entity(repositoryClass="Flower\ModelBundle\Repository\BoardRepository")
 */
class Board
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @ManyToOne(targetEntity="Project", inversedBy="boards")
     * @JoinColumn(name="project_id", referencedColumnName="id", nullable=true)
     * */
    private $project;


    /**
     * @OneToMany(targetEntity="Task", mappedBy="board")
     * */
    private $tasks;    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true))
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dueDate", type="datetime", nullable=true))
     */
    private $dueDate;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


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
     * @return Board
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
     * @return Board
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
     * Set status
     *
     * @param integer $status
     * @return Board
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Board
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

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     * @return Board
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime 
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Board
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Board
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    /**
     * Set project
     *
     * @param \Flower\ModelBundle\Entity\Project $project
     * @return Board
     */
    public function setProject(\Flower\ModelBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Flower\ModelBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set account
     *
     * @param \Flower\ModelBundle\Entity\Clients\Account $account
     * @return Board
     */
    public function setAccount(\Flower\ModelBundle\Entity\Clients\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \Flower\ModelBundle\Entity\Clients\Account 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set opportunity
     *
     * @param \Flower\ModelBundle\Entity\Clients\Opportunity $opportunity
     * @return Board
     */
    public function setOpportunity(\Flower\ModelBundle\Entity\Clients\Opportunity $opportunity = null)
    {
        $this->opportunity = $opportunity;

        return $this;
    }

    /**
     * Get opportunity
     *
     * @return \Flower\ModelBundle\Entity\Clients\Opportunity 
     */
    public function getOpportunity()
    {
        return $this->opportunity;
    }

    /**
     * Add tasks
     *
     * @param \Flower\ModelBundle\Entity\Task $tasks
     * @return Board
     */
    public function addTask(\Flower\ModelBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \Flower\ModelBundle\Entity\Task $tasks
     */
    public function removeTask(\Flower\ModelBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    public function __toString()
    {
        return $this->name;
    }
}
