<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AcmeAssert;

/**
 * Evenements
 *
 * @ORM\Table(name="evenements")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EvenementsRepository")
 */
class Evenements
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DateTime
     * @AcmeAssert\DateDuJourMinimum
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * @Assert\File(
     *     mimeTypes = {"image/*"}
     * )
     */
    private $image;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="heure", type="time", nullable=true)
     */
    private $heure;

    /**
     * @var string
     *
     * @ORM\Column(name="lieu", type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @var string
     *
     * @ORM\Column(name="information", type="text", nullable=true)
     */
    private $information;

    /**
     * @var string
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
     * Set date
     *
     * @param DateTime $date
     *
     * @return Evenements
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Evenements
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Evenements
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set heure
     *
     * @param DateTime $heure
     *
     * @return Evenements
     */
    public function setHeure($heure)
    {
        $this->heure = $heure;

        return $this;
    }

    /**
     * Get heure
     *
     * @return DateTime
     */
    public function getHeure()
    {
        return $this->heure;
    }

    /**
     * Set lieu
     *
     * @param string $lieu
     *
     * @return Evenements
     */
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu
     *
     * @return string
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * Set information
     *
     * @param string $information
     *
     * @return Evenements
     */
    public function setInformation($information)
    {
        $this->information = $information;

        return $this;
    }

    /**
     * Get information
     *
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * Set active
     *
     * @param string $active
     *
     * @return Evenements
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}
