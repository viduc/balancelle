<?php

namespace BalancelleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enfant
 *
 * @ORM\Table(name="preference")
 * @ORM\Entity(repositoryClass="BalancelleBundle\Repository\PreferenceRepository")
 */
class Preference
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
     * @var bool
     *
     * @ORM\Column(name="covid", type="boolean")
     */
    private $covid;

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
     * Set active
     *
     * @param boolean $active
     *
     * @return Preference
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
     * Set covid
     *
     * @param boolean $covid
     *
     * @return Preference
     */
    public function setCovid($covid)
    {
        $this->covid = $covid;

        return $this;
    }

    /**
     * Get covid
     *
     * @return bool
     */
    public function getCovid()
    {
        return $this->covid;
    }
}