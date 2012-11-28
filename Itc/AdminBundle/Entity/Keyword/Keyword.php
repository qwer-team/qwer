<?php

namespace Itc\AdminBundle\Entity\Keyword;

use Doctrine\ORM\Mapping as ORM;
use Itc\AdminBundle\Entity\Menu\Menu;
use \Itc\AdminBundle\Entity\TranslatableEntity;
/**
 * Itc\AdminBundle\Entity\Keyword\Keyword
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Keyword extends TranslatableEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $keyword
     *
     * @ORM\Column(name="keyword", type="string", length=255)
     */
    private $keyword;

    /**
     * @ORM\ManyToMany(targetEntity="Itc\AdminBundle\Entity\Menu\Menu", mappedBy="keywords")
     **/
    private $menus;
    
      /**
    * @ORM\OneToMany(
    *     targetEntity="KeywordTranslation",
    *     mappedBy="translatable",
    *     cascade={"persist"}
    * )
    */
    protected $translations;
    
    protected $fields = array("keyword");
    
    public function __construct() {
        parent::__construct();
        $this->menus = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set keyword
     *
     * @param string $keyword
     * @return Keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    
        return $this;
    }

    /**
     * Get keyword
     *
     * @return string 
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Add menus
     *
     * @param Itc\AdminBundle\Entity\Menu\Menu $menus
     * @return Keyword
     */
    public function addMenu(\Itc\AdminBundle\Entity\Menu\Menu $menus)
    {
        $this->menus[] = $menus;
    
        return $this;
    }

    /**
     * Remove menus
     *
     * @param Itc\AdminBundle\Entity\Menu\Menu $menus
     */
    public function removeMenu(\Itc\AdminBundle\Entity\Menu\Menu $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Get menus
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMenus()
    {
        return $this->menus;
    }
    
    function __toString(){
        return is_null( $this->keyword ) ? "" : $this->keyword;
    }
}