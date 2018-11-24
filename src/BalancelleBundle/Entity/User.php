<?php
// src/BalancelleBundle/Entity/User.php

namespace BalancelleBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
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
    *
    * @ORM\Column(name="birthday", type="date", nullable=true)
    */
    protected $birthday=null;

    public function __construct()
    {
        parent::__construct();
        $this->userTypes = new ArrayCollection();
    }

    /**
     * Add userType
     *
     * @param \BalancelleBundle\Entity\UserType $userType
     *
     * @return User
     */
    public function addUserType(\BalancelleBundle\Entity\UserType $userType)
    {
        $this->userTypes[] = $userType;

        return $this;
    }

    /**
     * Remove userType
     *
     * @param \BalancelleBundle\Entity\UserType $userType
     */
    public function removeUserType(\BalancelleBundle\Entity\UserType $userType)
    {
        $this->userTypes->removeElement($userType);
    }

    /**
     * Get userTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserTypes()
    {
        return $this->userTypes;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return User
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
     * @return User
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
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }
}
