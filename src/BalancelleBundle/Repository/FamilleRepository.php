<?php

namespace BalancelleBundle\Repository;

use BalancelleBundle\Entity\Enfant;
use BalancelleBundle\Entity\Famille;
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

    /**
     * Récupère les mails des parents (user) d'une famille !!!
     * @param Famille $famille
     * @return null | array
     */
    public function getParentsEmail($famille)
    {
        $tabRetour = null;
        if ($famille !== null && $famille->getParent1()) {
            $tabRetour[] = $famille->getParent1()->getEmail();
        }
        if ($famille !== null && $famille->getParent2()) {
            $tabRetour[] = $famille->getParent2()->getEmail();
        }

        return $tabRetour;
    }

    public function getFamilleActive()
    {
        return $this
            ->createQueryBuilder('a')
            ->where('a.active = 1')
            ->getQuery()
            ->execute()
            ;
    }

    /**
     * Permet de récupérer les familles d'une structure
     * @param int $structureId - l'id de la structure
     * @return int|mixed|string
     */
    public function getFamilleDuneStructure($structureId)
    {
        return $this
            ->createQueryBuilder('a')
            ->from(Enfant::class, 'e')
            ->where('e.famille = a')
            ->andWhere('e.structure =' . $structureId)
            ->getQuery()
            ->execute()
            ;
    }
}
