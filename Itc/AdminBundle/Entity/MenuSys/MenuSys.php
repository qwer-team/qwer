<?php

namespace Itc\AdminBundle\Entity\MenuSys;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Itc\AdminBundle\Entity\Content;
use Itc\AdminBundle\Entity\MenuSys\MenuSysProxy;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Itc\AdminBundle\Entity\MenuSys\MenuSys
 * @Gedmo\Tree(type="closure")
 * @Gedmo\TreeClosure(class="Itc\AdminBundle\Entity\MenuSys\MenuSysClosure")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\ClosureTreeRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 * @Vich\Uploadable
 */
class MenuSys extends Content
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
     * @var integer $parent_id
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    protected $parent_id;
    /**
     * @Gedmo\TreeParent
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @ORM\ManyToOne(targetEntity="MenuSys", inversedBy="children")
     * 
     */
    protected $parent;
    /**
     * @var string $routing
     *
     * @ORM\Column(name="routing", type="string", length=255, nullable=true)
     */
    protected $routing;
 
    protected $fields = array('content', 'title', 'translit', 'metaTitle', 'metaDescription', 'metaKeyword');
    /**
     * @ORM\OneToMany(targetEntity="MenuSys", mappedBy="parent")
     **/
    protected $children;

    
    public function __construct() {
        parent::__construct();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set tag
     *
     * @param string $tag
     * @return MenuSys
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

    /**
     * Get children
     *
     * @return array 
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Set date_create
     *
     * @param \DateTime $dateCreate
     * @return MenuSys
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
     * @return MenuSys
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

    
    public function setIconImage($image)
    {
        $this->iconImage = $image;
    }
    
    public function getIconImage()
    {
        return $this->iconImage;
    }

    /**
     * Set parent
     *
     * @param Itc\AdminBundle\Entity\MenuSys\MenuSys $parent
     * @return MenuSys
     */
    public function setParent( $parent = null )
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return Itc\AdminBundle\Entity\MenuSys\MenuSys
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * Set parent_id
     *
     * @return integer
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
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
    * @ORM\OneToMany(
    *     targetEntity="MenuSysTranslation",
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

    public function kodOfCreate()
    {
        $this->kod =  "";
    }
    
     public function addClosure(MenuSysClosure $closure)
    {
        $this->closures[] = $closure;
    }
    
}
