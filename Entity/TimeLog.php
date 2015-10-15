<?php

namespace Flower\ModelBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use Flower\ModelBundle\Entity\User\User;

/**
 * TimeLog
 *
 * @ORM\Table(name="time_log")
 * @ORM\Entity(repositoryClass="Flower\ModelBundle\Repository\TimeLogRepository")
 */
class TimeLog
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
     * @var float
     *
     * @ORM\Column(name="hours", type="float")
     */
    private $hours;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ManyToOne(targetEntity="Task")
     * @JoinColumn(name="task_id", referencedColumnName="id")
     * */
    private $task;
    
    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    private $user;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="spent_on", type="datetime")
     */
    private $spentOn;

    /**
     * @var DateTime
     * 
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_on", type="datetime")
     */
    private $createdOn;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_on", type="datetime")
     */
    private $updatedOn;
    
    function __construct()
    {
        $this->spentOn = new DateTime();   
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
     * Set hours
     *
     * @param float $hours
     * @return TimeLog
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return float 
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return TimeLog
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
     * Set spentOn
     *
     * @param DateTime $spentOn
     * @return TimeLog
     */
    public function setSpentOn($spentOn)
    {
        $this->spentOn = $spentOn;

        return $this;
    }

    /**
     * Get spentOn
     *
     * @return DateTime 
     */
    public function getSpentOn()
    {
        return $this->spentOn;
    }

    /**
     * Set createdOn
     *
     * @param DateTime $createdOn
     * @return TimeLog
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return DateTime 
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set updatedOn
     *
     * @param DateTime $updatedOn
     * @return TimeLog
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return DateTime 
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * Set task
     *
     * @param \Flower\ModelBundle\Entity\Task $task
     * @return TimeLog
     */
    public function setTask(\Flower\ModelBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Flower\ModelBundle\Entity\Task 
     */
    public function getTask()
    {
        return $this->task;
    }

    public function __toString()
    {
        return $this->description;
    }


    /**
     * Set user
     *
     * @param \Flower\ModelBundle\Entity\User\User $user
     * @return TimeLog
     */
    public function setUser(\Flower\ModelBundle\Entity\User\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Flower\ModelBundle\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
