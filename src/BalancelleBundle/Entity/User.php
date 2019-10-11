<?php
// src/BalancelleBundle/Entity/User.php

namespace BalancelleBundle\Entity;

use DateTime;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email", column=@ORM\Column(type="string", name="email", length=255, unique=false, nullable=true)),
 *      @ORM\AttributeOverride(name="emailCanonical", column=@ORM\Column(type="string", name="email_canonical", length=255, unique=false, nullable=true))
 * })
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
    protected $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    protected $nom;
    
    /**
    *
    * @ORM\Column(name="birthday", type="date", nullable=true)
    */
    protected $birthday=null;

    /**
     * @var string
     *
     * @ORM\Column(name="civilite", type="string", length=10)
     */
    protected $civilite;

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
     * @param DateTime $birthday
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
     * @return DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set civilite
     *
     * @param string $civilite
     *
     * @return User
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return string
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * Détermine si l'utilisateur a le rôle administrateur
     * @return Boolean
     */
    public function isroleAdmin()
    {
        return $this->hasRole('ROLE_ADMIN');
    }
    
    /**
     * Enregistre le role administrateur
     * @param Boolean $bool
     * @return User
     */
    public function setRoleAdmin($bool)
    {
        $this->removeRole('ROLE_ADMIN');
        if ($bool) {
            $this->addRole('ROLE_ADMIN');
        }
        return $this;
    }

    /**
     * Récupère le role admin
     * @return Boolean
     */
    public function getRoleAdmin()
    {
        return $this->hasRole('ROLE_ADMIN');
    }
    
    /**
     * Détermine si l'utilisateur a le rôle professionnel
     * @return Boolean
     */
    public function isrolePro()
    {
        return $this->hasRole('ROLE_PRO');
    }
    
    /**
     * Enregistre le role professionnel
     * @param Boolean $bool
     * @return User
     */
    public function setRolePro($bool)
    {
        $this->removeRole('ROLE_PRO');
        if ($bool) {
            $this->addRole('ROLE_PRO');
        }
        return $this;
    }
    
    /**
     * Récupère le role Pro
     * @return Boolean
     */
    public function getRolePro()
    {
        return $this->hasRole('ROLE_PRO');
    }

    /**
     * représente l'objet en string
     * @return string
     */
    public function __toString()
    {
        return $this->getPrenom() . ' ' . $this->getNom();
    }
}
