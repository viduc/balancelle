<?php

namespace BalancelleBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * FamilleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FamilleRepository extends EntityRepository
{
    /**
     * Permet de récupérer une famille en fonction d'une utilisateur
     * @param int $userId - l'id de l'utilisateur
     * @return mixed
     */
    public function findByFamille($userId)
    {
        try {
            return $this
                ->createQueryBuilder('a')
                ->where('a.parent1 = :userId')
                ->orWhere('a.parent2 = :userId')
                ->setParameter('userId', "$userId")
                ->andWhere('a.active = true')
                ->getQuery()
                ->getOneOrNullResult()
                ;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Méthode de recherche pour l'autocomplétion
     * @param string $recherche - la recherche
     * @return mixed
     */
    public function autocomplete($recherche)
    {
        return $this
            ->createQueryBuilder('a')
            ->andWhere('a.nom LIKE :recherche')
            ->setParameter('recherche', "%$recherche%")
            ->orderBy('a.nom')
            ->getQuery()
            ->execute()
            ;
    }
}
