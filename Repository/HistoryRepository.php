<?php

namespace Flower\BoardBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Flower\ModelBundle\Entity\Board\History;

/**
 * HistoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HistoryRepository extends EntityRepository
{
    /**
     * @param History $history
     * @return History
     */
    public function save(History $history){
        $this->_em->persist($history);
        $this->_em->flush();

        return $history;
    }

    /**
     * @param History $history
     * @return History
     */
    public function update(History $history){
        $this->_em->flush();
        return $history;
    }


}
