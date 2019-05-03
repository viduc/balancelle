<?php

namespace BalancelleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Famille
 *
 * @ORM\Table(name="famille")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\FamilleRepository")
 */
class Famille
{
    /**
     * @ORM\OneToOne(targetEntity="BalancelleBundle\Entity\User", cascade={"persist"})
     */
    private $parent1;

    /**
     * @ORM\OneToOne(targetEntity="BalancelleBundle\Entity\User", cascade={"persist"})
     * @Assert\Expression("value !== this.getParent1()",message = "les deux parents doivent être différents")
     */
    private $parent2;

    /**
     * @ORM\OneToMany(targetEntity="BalancelleBundle\Entity\Enfant", cascade={"persist", "remove"}, mappedBy="famille")
     */
    private $enfants;

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
    }

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
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModification", type="datetime")
     */
    private $dateModification;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nombre_permanence", type="integer", nullable=true)
     */
    private $nombrePermanence;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Famille
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Famille
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return Famille
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Famille
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set parent1
     *
     * @param \BalancelleBundle\Entity\User $parent1
     *
     * @return Famille
     */
    public function setParent1(\BalancelleBundle\Entity\User $parent1 = null)
    {
        $this->parent1 = $parent1;

        return $this;
    }

    /**
     * Get parent1
     *
     * @return \BalancelleBundle\Entity\User
     */
    public function getParent1()
    {
        return $this->parent1;
    }

    /**
     * Set parent2
     *
     * @param \BalancelleBundle\Entity\User $parent2
     *
     * @return Famille
     */
    public function setParent2(\BalancelleBundle\Entity\User $parent2 = null)
    {
        $this->parent2 = $parent2;

        return $this;
    }

    /**
     * Get parent2
     *
     * @return \BalancelleBundle\Entity\User
     */
    public function getParent2()
    {
        return $this->parent2;
    }

    /**
     * Add enfant
     *
     * @param \BalancelleBundle\Entity\Enfant $enfant
     *
     * @return Famille
     */
    public function addEnfant(\BalancelleBundle\Entity\Enfant $enfant)
    {
        $this->enfants[] = $enfant;

        return $this;
    }

    /**
     * Remove enfant
     *
     * @param \BalancelleBundle\Entity\Enfant $enfant
     */
    public function removeEnfant(\BalancelleBundle\Entity\Enfant $enfant)
    {
        $this->enfants->removeElement($enfant);
    }

    /**
     * Get enfants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnfants()
    {
        return $this->enfants;
    }

    /**
     * Set nombrePermanence.
     *
     * @param int $nombrePermanence
     *
     * @return Famille
     */
    public function setNombrePermanence($nombrePermanence)
    {
        $this->nombrePermanence = $nombrePermanence;

        return $this;
    }

    /**
     * Get nombrePermanence.
     *
     * @return int
     */
    public function getNombrePermanence()
    {
        return $this->nombrePermanence;
    }
}
