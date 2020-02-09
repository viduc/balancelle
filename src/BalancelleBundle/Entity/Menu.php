<?php

namespace BalancelleBundle\Entity;

use phpDocumentor\Reflection\Types\String_;
use PhpParser\Node\Expr\Array_;

class Menu
{
    protected $path;

    protected $parametre;

    protected $icon;

    protected $libelle;

    protected $type = 'menu';

    public function __construct(
        $path = null,
        $libelle = null,
        $icon = null,
        $parametre = []
    ) {
        $this->path = $path;
        $this->libelle = $libelle;
        $this->icon = $icon;
        $this->parametre = $parametre;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Le path (routing) de l'url qui sera générée
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getParametre()
    {
        return $this->parametre;
    }

    /**
     * @param mixed $parametre
     */
    public function setParametre($parametre)
    {
        $this->parametre = $parametre;
    }

    /**
     * Ajoute un parametre au tableau des parametres
     * @param String_ $key
     * @param String_ $value
     */
    public function addParametre($key, $value)
    {
        $this->parametre[$key] = $value;
    }

    public function getType()
    {
        return $this->type;
    }
}