<?php

namespace Flower\BoardBundle\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use Flower\ModelBundle\Entity\User\User;

/**
 * History
 *
 */
abstract class History
{

    const TYPE_TASK = "task";
    const TYPE_PROJECT = "project";
    const TYPE_ACCOUNT = "account";
    const TYPE_CONTACT = "contact";
    const TYPE_EVENT = "event";
    const TYPE_CALL_EVENT = "call_event";
    const TYPE_CAMPAIGN_MAIL = "campaign_mail";

    const CRUD_CREATE = "create";
    const CRUD_UPDATE = "update";
    const CRUD_DELETE = "delete";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="enitity_id", type="string", length=255, nullable=true)
     */
    protected $enitity_id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="attribute", type="string", length=255, nullable=true)
     */
    protected $attribute;

    /**
     * @var string
     *
     * @ORM\Column(name="value_old", type="string", length=255, nullable=true)
     */
    protected $oldValue;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    protected $value;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    protected $message;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $user;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="changedOn", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $changedOn;
    
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
     * Set type
     *
     * @param string $type
     * @return History
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
     * Set changedOn
     *
     * @param DateTime $changedOn
     * @return History
     */
    public function setChangedOn($changedOn)
    {
        $this->changedOn = $changedOn;

        return $this;
    }

    /**
     * Get changedOn
     *
     * @return DateTime 
     */
    public function getChangedOn()
    {
        return $this->changedOn;
    }


    /**
     * Set enitity_id
     *
     * @param string $enitityId
     * @return History
     */
    public function setEnitityId($enitityId)
    {
        $this->enitity_id = $enitityId;

        return $this;
    }

    /**
     * Get enitity_id
     *
     * @return string 
     */
    public function getEnitityId()
    {
        return $this->enitity_id;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     * @return History
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * Get oldValue
     *
     * @return string 
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return History
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param \Flower\ModelBundle\Entity\User\User $user
     * @return History
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

    /**
     * Set attribute
     *
     * @param string $attribute
     * @return History
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return string 
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set message
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
