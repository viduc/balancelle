<?php

namespace BalancelleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Enfant
 *
 * @ORM\Table(name="annee")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\AnneeRepository")
 */
class Annee
{
    /**
     * @ORM\OneToMany(targetEntity="BalancelleBundle\Entity\Famille", cascade={"persist", "remove"}, mappedBy="annee")
     */
    private $familles;

    public function __construct()
    {
        $this->familles = new ArrayCollection();
    }

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="annee", type="integer")
     */
    private $annee;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get annee
     * @return int
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set annee
     * @param int $annee
     * @return Annee
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get active
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Is active
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set active
     * @param bool $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Add famille.
     * @param Famille $famille
     * @return Annee
     */
    public function addFamille(Famille $famille)
    {
        $this->familles[] = $famille;

        return $this;
    }

    /**
     * Remove famille.
     * @param Famille $famille
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFamille(Famille $famille)
    {
        return $this->familles->removeElement($famille);
    }

    /**
     * Get familles.
     * @return Collection
     */
    public function getFamilles()
    {
        return $this->familles;
    }
}
