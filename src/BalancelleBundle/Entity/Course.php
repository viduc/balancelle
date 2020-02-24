<?php

namespace BalancelleBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Course
 *
 * @ORM\Table(name="course")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\CourseRepository")
 */
class Course
{
    /**
     * @ORM\ManyToOne(targetEntity="BalancelleBundle\Entity\Magasin", inversedBy="courses")
     */
    private $magasin;

    /**
     * @ORM\ManyToOne(targetEntity="BalancelleBundle\Entity\Famille", inversedBy="courses")
     */
    private $famille;

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
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=false)
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
     * @return Magasin
     */
    public function getMagasin()
    {
        return $this->magasin;
    }

    /**
     * @param mixed $magasin
     *
     * @return Course
     */
    public function setMagasin($magasin)
    {
        $this->magasin = $magasin;

        return $this;
    }

    /**
     * @return Famille
     */
    public function getFamille()
    {
        return $this->famille;
    }

    /**
     * @param mixed $famille
     *
     * @return Course
     */
    public function setFamille($famille)
    {
        $this->famille = $famille;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     *
     * @return Course
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * @param string|null $commentaire
     *
     * @return Course
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return Course
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }


}
