<?php

namespace BalancelleBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Semaine
 *
 * @ORM\Table(name="semaine")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\SemaineRepository")
 */
class Semaine
{
    /**
     * @ORM\ManyToOne(targetEntity="BalancelleBundle\Entity\Calendrier", inversedBy="semaines")
     */
    private $calendrier;

    /**
     * @ORM\OneToMany(targetEntity="BalancelleBundle\Entity\Permanence", cascade={"persist", "remove"}, mappedBy="semaine")
     */
    private $permanences;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**
     * @var int
     *
     * @ORM\Column(name="annee", type="integer")
     */
    private $annee;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="dateDebut", type="datetime", nullable=true)
     */
    private $dateDebut;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="dateFin", type="datetime", nullable=true)
     */
    private $dateFin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;

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
     * Set numero.
     *
     * @param int $numero
     *
     * @return Semaine
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set annee.
     *
     * @param int $annee
     *
     * @return Semaine
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee.
     *
     * @return int
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set dateDebut.
     *
     * @param DateTime|null $dateDebut
     *
     * @return Semaine
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
     * @return Semaine
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
     * Set commentaire.
     *
     * @param string|null $commentaire
     *
     * @return Semaine
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
     * Set nbrPermanenceMatin.
     *
     * @param int $nbrPermanenceMatin
     *
     * @return Semaine
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
     * @return Semaine
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->permanences = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set calendrier.
     *
     * @param \BalancelleBundle\Entity\Calendrier|null $calendrier
     *
     * @return Semaine
     */
    public function setCalendrier(\BalancelleBundle\Entity\Calendrier $calendrier = null)
    {
        $this->calendrier = $calendrier;

        return $this;
    }

    /**
     * Get calendrier.
     *
     * @return \BalancelleBundle\Entity\Calendrier|null
     */
    public function getCalendrier()
    {
        return $this->calendrier;
    }

    /**
     * Add permanence.
     *
     * @param \BalancelleBundle\Entity\Permanence $permanence
     *
     * @return Semaine
     */
    public function addPermanence(\BalancelleBundle\Entity\Permanence $permanence)
    {
        $this->permanences[] = $permanence;

        return $this;
    }

    /**
     * Remove permanence.
     *
     * @param \BalancelleBundle\Entity\Permanence $permanence
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePermanence(\BalancelleBundle\Entity\Permanence $permanence)
    {
        return $this->permanences->removeElement($permanence);
    }

    /**
     * Get permanences.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPermanences()
    {
        return $this->permanences;
    }
}
