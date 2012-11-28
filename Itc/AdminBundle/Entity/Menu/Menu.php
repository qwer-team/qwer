<?php

namespace Itc\AdminBundle\Entity\Menu;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Itc\AdminBundle\Entity\TranslatableEntity as TranslatableEntity;
use Itc\AdminBundle\Entity\Menu\MenuProxy;
use Itc\AdminBundle\Entity\Menu\MenuTranslation;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Itc\AdminBundle\Entity\Keyword\Keyword;
use Itc\AdminBundle\Entity\Content;
/**
 * Itc\AdminBundle\Entity\Menu\Menu
 * @Gedmo\Tree(type="closure")
 * @Gedmo\TreeClosure(class="Itc\AdminBundle\Entity\Menu\MenuClosure")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\ClosureTreeRepository")
 * @ORM\HasLifecycleCallbacks 
 * @ORM\Table()
 * @Vich\Uploadable
 */
class Menu extends Content
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer $parent_id
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    protected $parent_id;
    /**
     * @Gedmo\TreeParent
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="children")
     * 
     */
    protected $parent;

    

    /**
     * @var string $tag
     *
     * @ORM\Column(name="tag", type="string", length=255)
     */
    protected $tag;

    

    /**
     * @var \DateTime $date_create
     *
     * @ORM\Column(name="date_create", type="datetime")
     */
    protected $date_create;

    /**
     * @var boolean $visible
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    protected $visible;

    

       
    /**
     * @ORM\OneToMany(
     *     targetEntity="Itc\AdminBundle\Entity\Gallery\Gallery",
     *     mappedBy="menu",
     *     cascade={"persist"}
     * )
     */
    protected $galleries;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true,
     * options={"default" = NULL})
     */
    protected $description;
    

    protected $fields = array('text', 'title', 'translit', 'metaTitle', 'metaDescription', 'metaKeyword');
    /**
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="parent")
     **/
    protected $children;
    /**
     * @ORM\ManyToMany(targetEntity="Itc\AdminBundle\Entity\Keyword\Keyword", inversedBy="menus")
     * @ORM\JoinTable(name="menu_keyword")
     **/
    protected $keywords;
    
    /**
     * @var string $routing
     *
     * @ORM\Column(name="routing", type="string", length=255, nullable=true)
     */
    private $routing;
    
    public function __construct() {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->keywords = new \Doctrine\Common\Collections\ArrayCollection();
        $this->galleries = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct();
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
     * Set parent
     *
     * @param Itc\AdminBundle\Entity\Menu\Menu $parent
     * @return Menu
     */
    public function setParent(\Itc\AdminBundle\Entity\Menu\Menu $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return Itc\AdminBundle\Entity\Menu\Menu 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent_id
     *
     * @param integer $parentId
     * @return Menu
     */
    
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
    
        return $this;
    }

    /**
     * Get parent_id
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parent_id;
    }
    
    /**
     * Set description
     *
     * @param string $description
     * @return Menu
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Menu
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }


    /**
     * Set date_create
     *
     * @param \DateTime $dateCreate
     * @return Menu
     */
    public function setDateCreate($dateCreate)
    {
        $this->date_create = $dateCreate;
    
        return $this;
    }

    /**
     * Get date_create
     *
     * @return \DateTime 
     */
    public function getDateCreate()
    {
        return $this->date_create;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Menu
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    

    
    /**
     * Get galleries
     * @return Gallery 
     */
    public function setIconImage($image)
    {
        $this->iconImage = $image;
    }
    
    public function getIconImage()
    {
        return $this->iconImage;
    }
    public function  setGalleries($gallery)
    {
        $this->galleries[] = $gallery;
    }
    public function  getGalleries()
    {
        return $this->galleries;
    }   
    public function getChildren()
    {
        return $this->children;
    }
    public function getKeywords()
    {
        return $this->keywords;
    }
    public function  setKeywords(Keyword $keyword)
    {
        $this->keywords[] = $keyword;
        return $this;
    }
    /**
     * Add gallery
     * @param Gallery $gallery
     * @return \Itc\AdminBundle\Entity\Menu\Menu 
     */
    public function addGallery($gallery)
    {
        $this->galleries[] = $gallery;
        return $this;
    }
    /**
    * @ORM\OneToMany(
    *     targetEntity="MenuTranslation",
    *     mappedBy="translatable",
    *     cascade={"persist"}
    * )
    */
    protected $translations;

    function __toString(){
        return is_null( $this->title ) ? "" : $this->title ;
    }
    
    /**
    * @ORM\PrePersist
    */
    public function dateOfCreate()
    {
        $this->date_create = new \DateTime();
    }
    
    /**
     * Set routing
     *
     * @param string $routing
     * @return MenuSys
     */
    public function setRouting($routing)
    {
        $this->routing = $routing;
    
        return $this;
    }

    /**
     * Get routing
     *
     * @return string 
     */
    public function getRouting()
    {
        return $this->routing;
    }
    
}
