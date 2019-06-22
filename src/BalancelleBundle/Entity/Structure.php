<?php

namespace BalancelleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Structure
 *
 * @ORM\Table(name="structure")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\StructureRepository")
 * @UniqueEntity(fields={"nomCourt"}, message="Un article existe déjà avec ce titre.", groups={"Enregistrement"})
 */
class Structure
{
    /**
     * @ORM\OneToMany(targetEntity="BalancelleBundle\Entity\Calendrier", cascade={"persist", "remove"}, mappedBy="structure")
     */
    private $calendriers;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="nomCourt", type="string", length=10, unique=true)
     */
    private $nomCourt;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return Structure
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set commentaire.
     *
     * @param string|null $commentaire
     *
     * @return Structure
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
     * @return Structure
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
     * Set nomCourt.
     *
     * @param string $nomCourt
     *
     * @return Structure
     */
    public function setNomCourt($nomCourt)
    {
        $this->nomCourt = $nomCourt;

        return $this;
    }

    /**
     * Get nomCourt.
     *
     * @return string
     */
    public function getNomCourt()
    {
        return $this->nomCourt;
    }

    /**
     * représente l'objet en string
     * @return string
     */
    public function __toString()
    {
        return $this->getNomCourt() . " - " . $this->getNom();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->calendriers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add calendrier.
     *
     * @param \BalancelleBundle\Entity\Calendrier $calendrier
     *
     * @return Structure
     */
    public function addCalendrier(\BalancelleBundle\Entity\Calendrier $calendrier)
    {
        $this->calendriers[] = $calendrier;

        return $this;
    }

    /**
     * Remove calendrier.
     *
     * @param \BalancelleBundle\Entity\Calendrier $calendrier
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCalendrier(\BalancelleBundle\Entity\Calendrier $calendrier)
    {
        return $this->calendriers->removeElement($calendrier);
    }

    /**
     * Get calendriers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalendriers()
    {
        return $this->calendriers;
    }
}
