<?php

namespace BalancelleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\String_;

class MenuOuvrant
{
    protected $menus;

    protected $icon;

    protected $libelle;

    protected $type = 'menu_ouvrant';

    public function __construct($libelle = null, $icon = null)
    {
        $this->libelle = $libelle;
        $this->icon = $icon;
        $this->menus = new ArrayCollection();
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
     * Renvoie la collection menus
     * @return ArrayCollection
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Ajoute un menu à la collection menus
     * @param Menu $menu
     */
    public function addMenu(Menu $menu)
    {
        $this->menus[] = $menu;
    }

    /**
     * Supprime un menu de la collection menus
     * @param Menu $menu
     */
    public function removeMenu(Menu $menu)
    {
        $this->menus->removeElement($menu);
    }

    public function getType()
    {
        return $this->type;
    }
}