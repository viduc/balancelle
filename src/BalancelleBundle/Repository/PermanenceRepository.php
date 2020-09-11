<?php

namespace BalancelleBundle\Repository;

use BalancelleBundle\Entity\Calendrier;
use BalancelleBundle\Entity\Famille;
use BalancelleBundle\Entity\Semaine;
use BalancelleBundle\Entity\Structure;
use Doctrine\ORM\EntityRepository;

/**
 * PermanenceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PermanenceRepository extends EntityRepository
{
    /** Récupère les permanences réalisées pour une famille
     * @param Famille $famille
     * @return mixed
     */
    public function recupererLesPermanencesRealisees($famille)
    {
        return $this
            ->createQueryBuilder('p')
            ->from(Calendrier::class, 'c')
            ->from(Semaine::class, 's')
            ->where('p.famille = :famille')
            ->andWhere('p.fin < :date')
            ->andWhere('c.active = 1')
            ->andWhere('s.calendrier = c')
            ->andWhere('p.semaine = s')
            ->setParameter('famille', $famille)
            ->setParameter('date', date('Y-m-d H:i:s'))
            ->orderBy('p.debut', 'ASC')
            ->getQuery()
            ->execute();
    }

    /** Récupère les permanences réalisées des années antérieurs pour une famille
     * @param Famille $famille
     * @return mixed
     */
    public function recupererLesPermanencesRealiseesAnterieures($famille)
    {
        return $this
            ->createQueryBuilder('p')
            ->from(Calendrier::class, 'c')
            ->from(Semaine::class, 's')
            ->where('p.famille = :famille')
            ->andWhere('p.fin < :date')
            ->andWhere('c.active = 0')
            ->andWhere('s.calendrier = c')
            ->andWhere('p.semaine = s')
            ->setParameter('famille', $famille)
            ->setParameter('date', date('Y-m-d H:i:s'))
            ->orderBy('p.debut', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function recupererLesPermanencesInscrite($famille)
    {
        return $this
            ->createQueryBuilder('p')
            ->from(Calendrier::class, 'c')
            ->from(Semaine::class, 's')
            ->where('p.famille = :famille')
            ->andWhere('c.active = 1')
            ->andWhere('s.calendrier = c')
            ->andWhere('p.semaine = s')
            ->setParameter('famille', $famille)
            ->orderBy('p.debut', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Récupère toutes les permanences pour une structure entre deux dates
     * @param $structure - la structure (nomCourt)
     * @param $debut - date de début de récupération - peut être null
     * @param $fin - de de fin de récupération - peut être null
     * @return mixed
     */
    public function recupererToutesLesPermanences(
        $structure,
        $debut = null,
        $fin = null
    ) {
        $qb = $this
            ->createQueryBuilder('p')
            ->from(Calendrier::class, 'c')
            ->from(Semaine::class, 's')
            ->from(Structure::class, 'st')
            ->where('st.nomCourt = :structure')
            ->andWhere('c.structure = st.id')
            ->andWhere('s.calendrier = c.id')
            ->andWhere('p.semaine = s.id')
            ->andWhere('p.active = :active')
            ->setParameter('structure', $structure)
            ->setParameter('active', 1);
        if ($debut !== null) {
            $qb->andWhere('p.debut > :debut')->setParameter('debut', $debut);
        }
        if ($fin !== null) {
            $qb->andWhere('p.fin < :fin')->setParameter('fin', $fin);
        }
        return $qb->orderBy('p.debut', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * Récupère toutes les permanences libres pour une structure entre deux dates
     * @param $structure - la structure (nomCourt)
     * @param $debut - date de début de récupération - peut être null
     * @param $fin - de de fin de récupération - peut être null
     * @return mixed
     */
    public function recupererToutesLesPermanencesLibre(
        $structure,
        $debut = null,
        $fin = null
    ) {
        $permanences = $this->recupererToutesLesPermanences(
            $structure,
            $debut,
            $fin
        );
        $tabRetour = [];
        foreach ($permanences as $permanence) {
            if ($permanence->getFamille() !== null) {
                $tabRetour[] = $permanence;
            }
        }

        return $tabRetour;
    }

    /**
     * Permet de récupérer toutes les permanences dont on doit rappeler
     * l'inscription aux familles.
     * @param $debut - la date de début de la permanence
     * @param $fin - la date de fin ($debut + 1 jour)
     * @return array
     */
    public function recupererToutesLesPermanencesPourRappel($debut, $fin)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $expr = $queryBuilder->expr();
        $qb = $queryBuilder
            ->where('p.active = :active')
            ->andWhere('p.debut >= :debut')
            ->andWhere('p.fin < :fin')
            ->andWhere($expr->isNotNull('p.famille'))
            ->setParameter('active', 1)
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin);

        return $qb->orderBy('p.debut', 'ASC')
                  ->getQuery()
                  ->execute();
    }

    /**
     * Récupère les informations sur les permanences d'une famille
     * @param Famille $famille
     * @return mixed
     */
    public function getInformationPermanenceFamille(Famille $famille)
    {
        $permanence['faite'] = $this->recupererLesPermanencesRealisees(
            $famille
        );
        $permanence['aFaire'] = $famille->getNombrePermanence()+
            $famille->getSoldePermanence();
        $permanence['soldePermanence'] = $famille->getSoldePermanence();
        $permanence['inscrit'] = $this->recupererLesPermanencesInscrite(
            $famille
        );
        $permanence['pourcentage'] = 0;
        if ($permanence['aFaire']) {
            $permanence['pourcentage'] =
                count($permanence['faite'])*100/$permanence['aFaire'];
            if ($permanence['pourcentage']>100) {
                $permanence['pourcentage'] = 100;
            }
        }

        return $permanence;
    }

    /**
     * Formate la liste des permanences réalisée.
     * @param $permanences - la tableau des information de permanences
     * @return array
     */
    public function formaterListePermanence($permanences)
    {
        $retour = [];
        foreach ($permanences['inscrit'] as $perm) {
            $perm->realise = in_array($perm, $permanences['faite'], true);
            $retour[] = $perm;
        }

        return $retour;
    }
}
