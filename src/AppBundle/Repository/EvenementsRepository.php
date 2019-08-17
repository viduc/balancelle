<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EvenementsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EvenementsRepository extends EntityRepository
{
    public function findAllActiveEvenement()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT e FROM AppBundle:Evenements e WHERE e.date >= '
                    . 'CURRENT_DATE() AND e.active = 1 ORDER BY e.date ASC'
            )
            ->getResult();
    }
}
