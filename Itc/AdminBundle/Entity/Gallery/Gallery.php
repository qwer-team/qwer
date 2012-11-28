<?php

namespace Itc\AdminBundle\Entity\Gallery;
use Itc\AdminBundle\Entity\Menu\Menu;
use Doctrine\ORM\Mapping as ORM;

/**
 * Itc\AdminBundle\Entity\Gallery
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Gallery
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
     /**
     *  @ORM\Column(name="menu_id", type="integer")
     * @var int 
     */
    private $menuId;
    /**
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id",
     * onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Itc\AdminBundle\Entity\Menu\Menu", inversedBy="galleries")
     */
    private $menu;
   
    /**
     * @ORM\OneToMany(
     *     targetEntity="Image",
     *     mappedBy="gallery",
     *     cascade={"persist"}
     * )
     */
    private $images;
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Gallery
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Get images
     * @return Image 
     */
    public function  getImages()
    {
        return $this->images;
    }
    /**
     * Add image
     * @param Image $image
     * @return Gallery
     */
    public function addImage($image)
    {
        $this->images[] = $image;
        return $this;
    }
    /**
     * Set menu
     *
     * @param string $menu
     * @return Gallery
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    
        return $this;
    }

    /**
     * Get menu
     *
     * @return string 
     */
    public function getMenu()
    {
        return $this->menu;
    }
    /**
     * Set menuId
     *
     * @param string $menuId
     * @return Gallery
     */
    public function setMenuId($menuId)
    {
        $this->menuId = $menuId;
    
        return $this;
    }

    /**
     * Get menuId
     *
     * @return string 
     */
    public function getMenuId()
    {
        return $this->menuId;
    }
    
    public  function __toString()
    {
        return $this->title === null? "": $this->title;
    }
}
