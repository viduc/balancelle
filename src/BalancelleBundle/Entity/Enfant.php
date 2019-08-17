<?php

namespace BalancelleBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Enfant
 *
 * @ORM\Table(name="enfant")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\EnfantRepository")
 */
class Enfant
{
    /**
     * @ORM\ManyToOne(targetEntity="BalancelleBundle\Entity\Famille", inversedBy="enfants")
     */
    private $famille;

    /**
     * @ORM\ManyToOne(targetEntity="BalancelleBundle\Entity\Structure", inversedBy="enfants")
     */
    private $structure;

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
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="naissance", type="date")
     */
    private $naissance;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=255)
     */
    private $commentaire;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Enfant
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Enfant
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
     * Set naissance
     *
     * @param DateTime $naissance
     *
     * @return Enfant
     */
    public function setNaissance($naissance)
    {
        $this->naissance = $naissance;

        return $this;
    }

    /**
     * Get naissance
     *
     * @return DateTime
     */
    public function getNaissance()
    {
        return $this->naissance;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Enfant
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Enfant
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
     * Set famille
     *
     * @param Famille $famille
     *
     * @return Enfant
     */
    public function setFamille(Famille $famille = null)
    {
        $this->famille = $famille;

        return $this;
    }

    /**
     * Get famille
     *
     * @return Famille
     */
    public function getFamille()
    {
        return $this->famille;
    }

    /**
     * Set structure
     *
     * @param Structure $structure
     *
     * @return Enfant
     */
    public function setStructure(Structure $structure = null)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * Get structure
     *
     * @return Structure
     */
    public function getStructure()
    {
        return $this->structure;
    }
}
