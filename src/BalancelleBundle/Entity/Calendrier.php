<?php

namespace BalancelleBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Calendrier
 *
 * @ORM\Table(name="calendrier")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\CalendrierRepository")
 */
class Calendrier
{
    /**
     * @ORM\ManyToOne(targetEntity="BalancelleBundle\Entity\Structure", inversedBy="calendriers")
     */
    private $structure;

    /**
     * @ORM\OneToMany(targetEntity="BalancelleBundle\Entity\Semaine", cascade={"persist", "remove"}, mappedBy="calendrier")
     */
    private $semaines;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="dateDebut", type="date", nullable=true)
     */
    private $dateDebut;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="dateFin", type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @var int
     *
     * @ORM\Column(name="anneeDebut", type="integer")
     */

    private $anneeDebut;

    /**
     * @var int
     *
     * @ORM\Column(name="anneeFin", type="integer")
     */
    private $anneeFin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="nbrPermanenceMatin", type="integer")
     */
    private $nbrPermanenceMatin;

    /**
     * @var int
     *
     * @ORM\Column(name="nbrPermanenceAM", type="integer")
     */
    private $nbrPermanenceAM;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateDebut.
     *
     * @param DateTime|null $dateDebut
     *
     * @return Calendrier
     */
    public function setDateDebut($dateDebut = null)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut.
     *
     * @return DateTime|null
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin.
     *
     * @param DateTime|null $dateFin
     *
     * @return Calendrier
     */
    public function setDateFin($dateFin = null)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin.
     *
     * @return DateTime|null
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set anneeDebut.
     *
     * @param int $anneeDebut
     *
     * @return Calendrier
     */
    public function setAnneeDebut($anneeDebut)
    {
        $this->anneeDebut = $anneeDebut;

        return $this;
    }

    /**
     * Get anneeDebut.
     *
     * @return int
     */
    public function getAnneeDebut()
    {
        return $this->anneeDebut;
    }

    /**
     * Set anneeFin.
     *
     * @param int $anneeFin
     *
     * @return Calendrier
     */
    public function setAnneeFin($anneeFin)
    {
        $this->anneeFin = $anneeFin;

        return $this;
    }

    /**
     * Get anneeFin.
     *
     * @return int
     */
    public function getAnneeFin()
    {
        return $this->anneeFin;
    }

    /**
     * Set commentaire.
     *
     * @param string|null $commentaire
     *
     * @return Calendrier
     */
    public function setCommentaire($commentaire = null)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire.
     *
     * @return string|null
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return Calendrier
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set structure.
     *
     * @param \BalancelleBundle\Entity\Structure|null $structure
     *
     * @return Calendrier
     */
    public function setStructure(\BalancelleBundle\Entity\Structure $structure = null)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * Get structure.
     *
     * @return \BalancelleBundle\Entity\Structure|null
     */
    public function getStructure()
    {
        return $this->structure;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->semaines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add semaine.
     *
     * @param \BalancelleBundle\Entity\Semaine $semaine
     *
     * @return Calendrier
     */
    public function addSemaine(\BalancelleBundle\Entity\Semaine $semaine)
    {
        $this->semaines[] = $semaine;

        return $this;
    }

    /**
     * Remove semaine.
     *
     * @param \BalancelleBundle\Entity\Semaine $semaine
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeSemaine(\BalancelleBundle\Entity\Semaine $semaine)
    {
        return $this->semaines->removeElement($semaine);
    }

    /**
     * Get semaines.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSemaines()
    {
        return $this->semaines;
    }

    /**
     * Set nbrPermanenceMatin.
     *
     * @param int $nbrPermanenceMatin
     *
     * @return Calendrier
     */
    public function setNbrPermanenceMatin($nbrPermanenceMatin)
    {
        $this->nbrPermanenceMatin = $nbrPermanenceMatin;

        return $this;
    }

    /**
     * Get nbrPermanenceMatin.
     *
     * @return int
     */
    public function getNbrPermanenceMatin()
    {
        return $this->nbrPermanenceMatin;
    }

    /**
     * Set nbrPermanenceAM.
     *
     * @param int $nbrPermanenceAM
     *
     * @return Calendrier
     */
    public function setNbrPermanenceAM($nbrPermanenceAM)
    {
        $this->nbrPermanenceAM = $nbrPermanenceAM;

        return $this;
    }

    /**
     * Get nbrPermanenceAM.
     *
     * @return int
     */
    public function getNbrPermanenceAM()
    {
        return $this->nbrPermanenceAM;
    }
}
