<?php

namespace BalancelleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Magasin
 *
 * @ORM\Table(name="magasin")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\MagasinRepository")
 */
class Magasin
{

    /**
     * @ORM\OneToMany(targetEntity="BalancelleBundle\Entity\Course", cascade={"persist", "remove"}, mappedBy="magasin")
     */
    private $courses;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=false)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="horaire", type="string", length=255, nullable=true)
     */
    private $horaire;

    /**
     * @var string|null
     *
     * @ORM\Column(name="paiement", type="string", length=255, nullable=true)
     */
    private $paiement;

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
     * @return string|null
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string|null $nom
     *
     * @return Magasin
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param string|null $adresse
     *
     * @return Magasin
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * @param string|null $horaire
     *
     * @return Magasin
     */
    public function setHoraire($horaire)
    {
        $this->horaire = $horaire;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaiement()
    {
        return $this->paiement;
    }

    /**
     * @param string|null $paiement
     *
     * @return Magasin
     */
    public function setPaiement($paiement)
    {
        $this->paiement = $paiement;

        return $this;
    }

    /**
     * Set commentaire.
     *
     * @param string|null $commentaire
     *
     * @return Magasin
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
     * @return Magasin
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
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    /**
     * Add course.
     *
     * @param Course $course
     *
     * @return Magasin
     */
    public function addCourse(Course $course)
    {
        $this->courses[] = $course;

        return $this;
    }

    /**
     * Remove course.
     *
     * @param Course $course
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCourse(Course $course)
    {
        return $this->courses->removeElement($course);
    }

    /**
     * Get courses.
     *
     * @return Collection
     */
    public function getCourses()
    {
        return $this->courses;
    }

}
