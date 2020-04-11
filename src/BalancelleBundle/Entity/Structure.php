<?php

namespace BalancelleBundle\Entity;

use BalancelleBundle\Form\CourseType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\OneToMany(targetEntity="BalancelleBundle\Entity\Enfant", cascade={"persist", "remove"}, mappedBy="structure")
     */
    private $enfants;

    /**
     * @ORM\OneToMany(targetEntity="BalancelleBundle\Entity\Course", cascade={"persist", "remove"}, mappedBy="structure")
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
     * @var string
     *
     * @ORM\Column(name="heure_debut_permanence_matin", type="string", length=255, nullable=true)
     */
    private $heureDebutPermanenceMatin;

    /**
     * @var string
     *
     * @ORM\Column(name="heure_fin_permanence_matin", type="string", length=255, nullable=true)
     */
    private $heureFinPermanenceMatin;

    /**
     * @var string
     *
     * @ORM\Column(name="heure_debut_permanence_am", type="string", length=255, nullable=true)
     */
    private $heureDebutPermanenceAM;

    /**
     * @var string
     *
     * @ORM\Column(name="heure_fin_permanence_am", type="string", length=255, nullable=true)
     */
    private $heureFinPermanenceAM;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email(checkMX=true)
     */
    private $email;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->calendriers = new ArrayCollection();
        $this->enfants = new ArrayCollection();
        $this->courses = new ArrayCollection();
    }

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
     * Set email.
     *
     * @param string $email
     *
     * @return Structure
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * représente l'objet en string
     * @return string
     */
    public function __toString()
    {
        return $this->getNomCourt() . ' - ' . $this->getNom();
    }

    /**
     * Add calendrier.
     *
     * @param Calendrier $calendrier
     *
     * @return Structure
     */
    public function addCalendrier(Calendrier $calendrier)
    {
        $this->calendriers[] = $calendrier;

        return $this;
    }

    /**
     * Remove calendrier.
     *
     * @param Calendrier $calendrier
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCalendrier(Calendrier $calendrier)
    {
        return $this->calendriers->removeElement($calendrier);
    }

    /**
     * Get calendriers.
     *
     * @return Collection
     */
    public function getCalendriers()
    {
        return $this->calendriers;
    }

    /**
     * Add enfant.
     *
     * @param Enfant $enfant
     *
     * @return Structure
     */
    public function addEnfant(Enfant $enfant)
    {
        $this->enfants[] = $enfant;

        return $this;
    }

    /**
     * Remove enfant.
     *
     * @param Enfant $enfant
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEnfant(Enfant $enfant)
    {
        return $this->enfants->removeElement($enfant);
    }

    /**
     * Get enfants.
     *
     * @return Collection
     */
    public function getEnfants()
    {
        return $this->enfants;
    }

    /**
     * Add course.
     *
     * @param Course $course
     *
     * @return Structure
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
        return $this->enfants->removeElement($course);
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

    /**
     * Set heureDebutPermanenceMatin.
     *
     * @param string|null $heureDebutPermanenceMatin
     *
     * @return Structure
     */
    public function setHeureDebutPermanenceMatin($heureDebutPermanenceMatin = null)
    {
        $this->heureDebutPermanenceMatin = $heureDebutPermanenceMatin;

        return $this;
    }

    /**
     * Get heureDebutPermanenceMatin.
     *
     * @return string|null
     */
    public function getHeureDebutPermanenceMatin()
    {
        return $this->heureDebutPermanenceMatin;
    }

    /**
     * Set heureFinPermanenceMatin.
     *
     * @param string|null $heureFinPermanenceMatin
     *
     * @return Structure
     */
    public function setHeureFinPermanenceMatin($heureFinPermanenceMatin = null)
    {
        $this->heureFinPermanenceMatin = $heureFinPermanenceMatin;

        return $this;
    }

    /**
     * Get heureFinPermanenceMatin.
     *
     * @return string|null
     */
    public function getHeureFinPermanenceMatin()
    {
        return $this->heureFinPermanenceMatin;
    }

    /**
     * Set heureDebutPermanenceAM.
     *
     * @param string|null $heureDebutPermanenceAM
     *
     * @return Structure
     */
    public function setHeureDebutPermanenceAM($heureDebutPermanenceAM = null)
    {
        $this->heureDebutPermanenceAM = $heureDebutPermanenceAM;

        return $this;
    }

    /**
     * Get heureDebutPermanenceAM.
     *
     * @return string|null
     */
    public function getHeureDebutPermanenceAM()
    {
        return $this->heureDebutPermanenceAM;
    }

    /**
     * Set heureFinPermanenceAM.
     *
     * @param string|null $heureFinPermanenceAM
     *
     * @return Structure
     */
    public function setHeureFinPermanenceAM($heureFinPermanenceAM = null)
    {
        $this->heureFinPermanenceAM = $heureFinPermanenceAM;

        return $this;
    }

    /**
     * Get heureFinPermanenceAM.
     *
     * @return string|null
     */
    public function getHeureFinPermanenceAM()
    {
        return $this->heureFinPermanenceAM;
    }
}
