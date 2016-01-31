<?php

namespace Flower\BoardBundle\Service;


use Doctrine\ORM\EntityRepository;
use Flower\ModelBundle\Entity\Board\History;
use Flower\ModelBundle\Entity\User\User;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

class HistoryService
{

    private $historyRepository;
    private $translator;

    public function __construct(EntityRepository $historyRepository, TranslatorInterface $translator)
    {
        $this->historyRepository = $historyRepository;
        $this->translator = $translator;
    }

    /**
     * @param User $user
     * @param null $type
     * @param null $entity
     * @param int $page
     * @return array
     */
    public function getUserActivity(User $user = null, $type = null, $entity = null, $page = 1)
    {
        $qb = $this->historyRepository->createQueryBuilder('h');

        if($user){
            $qb->where('h.user = :user_id')->setParameter("user_id", $user->getId());
        }

        if (!is_null($type)) {
            $qb->andWhere('h.type = :type')->setParameter("type", $type);
        }

        $qb->orderBy('h.changedOn','DESC');

        $qb->setMaxResults(20);

        return $qb->getQuery()->getResult();
    }


    /**
     * Add simple user activity.
     * @param $type
     * @param $entity
     * @param $user
     * @param $crudAction
     */
    public function addSimpleUserActivity($type, User $user, $entity, $crudAction = null)
    {

        $history = new History();
        $history->setUser($user);
        $history->setEnitityId($entity->getId());
        $history->setMessage($this->getMessage($type, $user, $entity, $crudAction));
        $history->setType($type);

        $this->historyRepository->save($history);

    }

    /**
     * @param $type
     * @param User $user
     * @param $entity
     * @param $crudAction
     * @return string
     */
    public function getMessage($type, User $user, $entity, $crudAction){

        switch($type){
            case History::TYPE_PROJECT:
                $msgIn = "activity.feed.project.".$crudAction;
                $msgOut = $this->translator->trans($msgIn, array("%project_name%" => $entity->getName()));
                break;
            case History::TYPE_ACCOUNT:
                $msgIn = "activity.feed.account.".$crudAction;
                $msgOut = $this->translator->trans($msgIn, array("%account_name%" => $entity->getName()));
                break;
            case History::TYPE_TASK:
                $msgIn = "activity.feed.task.".$crudAction;
                $msgOut = $this->translator->trans($msgIn, array("%task_name%" => $entity->getName()));
                break;
            case History::TYPE_CALL_EVENT:
                $msgIn = "activity.feed.callevent.".$crudAction;
                $msgOut = $this->translator->trans($msgIn, array("%callevent_subject%" => $entity->getSubject()));
                break;
            case History::TYPE_CAMPAIGN_MAIL:
                $msgIn = "activity.feed.campaignmail.".$crudAction;
                $msgOut = $this->translator->trans($msgIn, array("%campaignmail_name%" => $entity->getName()));
                break;
            case History::TYPE_EVENT:
                $msgIn = "activity.feed.event.".$crudAction;
                $msgOut = $this->translator->trans($msgIn, array("%event_title%" => $entity->getTitle()));
                break;
        }

        return $msgOut;

    }


}