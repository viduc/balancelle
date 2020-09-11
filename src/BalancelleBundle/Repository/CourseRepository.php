<?php

namespace BalancelleBundle\Repository;

use BalancelleBundle\Entity\Famille;
use Doctrine\ORM\EntityRepository;

/**
 * CourseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CourseRepository extends EntityRepository
{
    public function recupererLesCoursesDuneFamille(Famille $famille)
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.famille = :famille')
            ->andWhere('p.active = 1')
            ->setParameter('famille', $famille)
            ->orderBy('p.dateDebut', 'ASC')
            ->getQuery()
            ->execute();
    }
}