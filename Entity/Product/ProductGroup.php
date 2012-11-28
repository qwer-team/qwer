<?php
namespace Itc\AdminBundle\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use \Itc\AdminBundle\Entity\Content;
use Itc\AdminBundle\Entity\Product\ProductGroupClosure;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Itc\AdminBundle\Entity\Product\ProductGroup
 * @Gedmo\Tree(type="closure")
 * @Gedmo\TreeClosure(class="Itc\AdminBundle\Entity\Product\ProductGroupClosure")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\ClosureTreeRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 * @Vich\Uploadable
 */
class ProductGroup extends Content{

     /**
     * @var integer $parent_id
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    protected $parent_id;    
    /**
     * @Gedmo\TreeParent
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @ORM\ManyToOne(targetEntity="ProductGroup", inversedBy="children")
     * 
     */
    protected $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="ProductGroup", mappedBy="parent")
     **/
    protected $children;
    
     /*
     * @ORM\OneToMany(
     *     targetEntity="Product",
     *     mappedBy="productgroup",
     *     cascade={"remove"}
     * )
     */
    protected $products;
    /**
    * @ORM\OneToMany(
    *     targetEntity="ProductGroupTranslation",
    *     mappedBy="translatable",
    *     cascade={"persist"}
    * )
    */
    protected $translations;
    
    public function __construct() {
        parent::__construct();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set parent_id
     *
     * @param integer $parentId
     * @return ProductGroup
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
     * Set parent
     *
     * @param \Itc\AdminBundle\Entity\Product\ProductGroup $parent
     * @return ProductGroup
     */
    public function setParent(\Itc\AdminBundle\Entity\Product\ProductGroup $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \Itc\AdminBundle\Entity\Product\ProductGroup 
     */
    public function getParent()
    {
        return $this->parent;
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
     * Add children
     *
     * @param \Itc\AdminBundle\Entity\Product\ProductGroup $children
     * @return ProductGroup
     */
    public function addChildren(\Itc\AdminBundle\Entity\Product\ProductGroup $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \Itc\AdminBundle\Entity\Product\ProductGroup $children
     */
    public function removeChildren(\Itc\AdminBundle\Entity\Product\ProductGroup $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function addProduct(\Itc\AdminBundle\Entity\Product\Product $product)
    {
        $this->products[] = $product;
    
        return $this;
    }
    public function getProducts()
    {
        return $this->products;
    }
    
}