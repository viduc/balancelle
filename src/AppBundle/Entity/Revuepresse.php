<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Evenements
 *
 * @ORM\Table(name="revuepresse")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RevuepresseRepository")
 */
class Revuepresse
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
     * @var string
     *
     * @ORM\Column(name="scan", type="string", length=255, nullable=true)
     * @Assert\File(
     *     mimeTypes = {"application/pdf"}
     * )
     */
    private $scan;
    
    /**
     * @var string
     *
     * @ORM\Column(name="media", type="string", length=255, nullable=true)
     */
    private $media;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;
    
    /**
     * @var string
     *
     * @ORM\Column(name="information", type="text", nullable=true)
     */
    private $information;

    /**
     * @var string
     *
     * @ORM\Column(name="auteur", type="string", length=255, nullable=true)
     */
    private $auteur;
    
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
     * @return Revuepresse
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
     * @return Revuepresse
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
     * @return Revuepresse
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
     * Set scan
     *
     * @param string $scan
     *
     * @return Revuepresse
     */
    public function setScan($scan)
    {
        $this->scan = $scan;

        return $this;
    }

    /**
     * Get scan
     *
     * @return string
     */
    public function getScan()
    {
        return $this->scan;
    }
    
    /**
     * Set media
     *
     * @param string $media
     *
     * @return Revuepresse
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Revuepresse
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set information
     *
     * @param string $information
     *
     * @return Revuepresse
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
     * Set uuteur
     *
     * @param string $auteur
     *
     * @return Revuepresse
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return string
     */
    public function getAuteur()
    {
        return $this->auteur;
    }
    
    /**
     * Set active
     *
     * @param string $active
     *
     * @return Revuepresse
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
