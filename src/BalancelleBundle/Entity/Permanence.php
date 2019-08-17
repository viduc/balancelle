<?php

namespace BalancelleBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Permanence
 *
 * @ORM\Table(name="permanence")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\PermanenceRepository")
 */
class Permanence
{
    /**
     * @ORM\ManyToOne(targetEntity="BalancelleBundle\Entity\Famille")
     * @ORM\JoinColumn(nullable=true)
     */
    private $famille;

    /**
     * @ORM\ManyToOne(targetEntity="BalancelleBundle\Entity\Semaine", inversedBy="permanences")
     * @ORM\JoinColumn(nullable=true)
     */
    private $semaine;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="debut", type="datetime")
     */
    private $debut;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="fin", type="datetime")
     */
    private $fin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @var string|null
     *
     * @ORM\Column(name="couleur", type="string", length=50, nullable=true)
     */
    private $couleur;

    /**
     * @var bool|null
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
     * Set titre.
     *
     * @param string $titre
     *
     * @return Permanence
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre.
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set commentaire.
     *
     * @param string|null $commentaire
     *
     * @return Permanence
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
     * @return Permanence
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
     * Set debut.
     *
     * @param DateTime $debut
     *
     * @return Permanence
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * Get debut.
     *
     * @return DateTime
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set fin.
     *
     * @param DateTime $fin
     *
     * @return Permanence
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin.
     *
     * @return DateTime
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set famille.
     *
     * @param Famille|null $famille
     *
     * @return Permanence
     */
    public function setFamille(Famille $famille = null)
    {
        $this->famille = $famille;

        return $this;
    }

    /**
     * Get famille.
     *
     * @return Famille|null
     */
    public function getFamille()
    {
        return $this->famille;
    }

    /**
     * Set semaine.
     *
     * @param Semaine|null $semaine
     *
     * @return Permanence
     */
    public function setSemaine(Semaine $semaine = null)
    {
        $this->semaine = $semaine;

        return $this;
    }

    /**
     * Get semaine.
     *
     * @return Semaine|null
     */
    public function getSemaine()
    {
        return $this->semaine;
    }

    /**
     * Set couleur.
     *
     * @param string|null $couleur
     *
     * @return Permanence
     */
    public function setCouleur($couleur = null)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur.
     *
     * @return string|null
     */
    public function getCouleur()
    {
        return $this->couleur;
    }
}
