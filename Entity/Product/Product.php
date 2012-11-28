<?php

namespace Itc\AdminBundle\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use \Itc\AdminBundle\Entity\Content;
use Itc\AdminBundle\Entity\Product\Brand;
use Itc\AdminBundle\Entity\Product\ProductProxy;
use Itc\AdminBundle\Entity\Product\ProductTranslation;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Itc\AdminBundle\ItcAdminBundle;


/**
 * Itc\AdminBundle\Entity\Product\Product
 * 
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 * @Vich\Uploadable
 */
class Product extends Content
{
  //  protected $iconImage;
   /**
    * @ORM\ManyToMany(targetEntity="Product", inversedBy="relationProducts")
    * @ORM\JoinTable(name="relations_product",
    *           joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
    *           inverseJoinColumns={@ORM\JoinColumn(name="related_id", referencedColumnName="id")}
    * )
    */
    protected $relations;
     /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="relations")
     **/
    protected $products;
    /**
     * 
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id",
     * onDelete="CASCADE", nullable=true)
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="products")
     */    
    protected $brand;
    /**
     * @var integer $product_group_id
     *
     * @ORM\Column(name="product_group_id", type="integer", nullable=true)
     */
    protected $productGroupId;
    /**
     * @ORM\JoinColumn(name="product_group_id", referencedColumnName="id",
     * onDelete="CASCADE", nullable=true)
     * @ORM\ManyToOne(targetEntity="Itc\AdminBundle\Entity\Product\ProductGroup", inversedBy="products")
     */    
    protected $productGroup;
     /**
     * @ORM\Column(name="unit_id", type="integer", nullable=true)
     * @var int 
     */    
    protected $unitId;
    /**
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="id",
     * nullable=true)
     * @ORM\ManyToOne(targetEntity="Itc\AdminBundle\Entity\Units\Units", inversedBy="products")
     */    
    protected $unit;

    /**
    * @ORM\OneToMany(
    *     targetEntity="ProductTranslation",
    *     mappedBy="translatable",
    *     cascade={"persist"}
    * )
    */
    protected $translations;

    /**
     * @var decimal $price
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $price;
        
    /**
     * @var boolean $topSales
     *
     * @ORM\Column(name="top_sales", type="boolean", nullable=true)
     */
    protected $topSales;
    /**
     * @var boolean $novelty
     *
     * @ORM\Column(name="novelty", type="boolean", nullable=true)
     */
    protected $novelty;
    /**
     * @var boolean $bestSeller
     *
     * @ORM\Column(name="bestSeller", type="boolean", nullable=true)
     */
    protected $bestSeller;        
    /**
     * @var string $article
     * 
     * @ORM\Column(name="article", type="string", length=100, nullable=true)
     */
    protected $article;
    /**
     * @var string $warranty
     * 
     * @ORM\Column(name="warranty", type="string", length=100, nullable=true)
     */
    protected $warranty;
    /**
     * @var integer $count
     * 
     * @ORM\Column(name="count", type="integer", nullable=true)
     */
    protected $count;
    
    public function __construct() {
        parent::__construct();
        $this->products  = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relations = new \Doctrine\Common\Collections\ArrayCollection();
        //$this->relationProducts = new \Doctrine\Common\Collections\ArrayCollection();        
    }
    /**
     * Set topSales
     *
     * @param boolean $topSales
     * @return Product
     */
    public function setTopSales( $topSales )
    {
        $this->topSales = $topSales;
    
        return $this;
    }

    /**
     * Get topSales
     *
     * @return boolean 
     */
    public function getTopSales()
    {
        return $this->topSales;
    }
        
    /**
     * Set price
     *
     * @param integer $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }
    /**
     * Set unitId
     *
     * @param integer $unitId
     * @return Product
     */
    public function setUnitId($unitId)
    {
        $this->unitId = $unitId;
    
        return $this;
    }

    /**
     * Get unitId
     *
     * @return integer 
     */
    public function getUnitId()
    {
        return $this->unitId;
    }

    /**
     * Set productGroupId
     *
     * @param integer $productGroupId
     * @return Product
     */
    public function setProductGroupId($productGroupId)
    {
        $this->productGroupId = $productGroupId;
    
        return $this;
    }

    /**
     * Get productGroupId
     *
     * @return integer 
     */
    public function getProductGroupId()
    {
        return $this->productGroupId;
    }

    public function setBrand(\Itc\AdminBundle\Entity\Product\Brand $brand = null)
    {
        $this->brand = $brand;
    
        return $this;
    }

    /**
     * Get brand
     *
     * @return \Itc\AdminBundle\Entity\Product\Brand 
     */
    public function getBrand()
    {
        return $this->brand;
    }
    public function setUnit(\Itc\AdminBundle\Entity\Units\Units $unit = null)
    {
        $this->unit = $unit;
    
        return $this;
    }

    /**
     * Get brand
     *
     * @return \Itc\AdminBundle\Entity\Units\Units
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set productGroup
     *
     * @param \Itc\AdminBundle\Entity\Product\ProductGroup $productGroup
     * @return Product
     */
    public function setProductGroup(\Itc\AdminBundle\Entity\Product\ProductGroup $productGroup = null)
    {
        $this->productGroup = $productGroup;
    
        return $this;
    }

    /**
     * Get productGroup
     *
     * @return \Itc\AdminBundle\Entity\Product\ProductGroup 
     */
    public function getProductGroup()
    {
        return $this->productGroup;
    }
    
    public function getRelations()
    {
        return $this->relations;
    }
    public function  setRelations($relation)
    {
        $this->relations[] = $relation;
        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }
    public function  setProducts($product)
    {
        $this->products[] = $product;
        return $this;
    }
    
    public function setIconImage($image)
    {
        $this->iconImage = $image;
    }
    
    public function getIconImage()
    {
        return $this->iconImage;
    } 
    public function setArticle($article)
    {
        $this->article = $article;
    }
    
    public function getArticle()
    {
        return $this->article;
    }    
    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;
    }
    
    public function getWarranty()
    {
        return $this->warranty;
    }    
    public function setNovelty($novelty)
    {
        $this->novelty = $novelty;
    }
    
    public function getNovelty()
    {
        return $this->novelty;
    }    
    public function setBestSeller($bestSeller)
    {
        $this->bestSeller = $bestSeller;
    }
    
    public function getBestseller()
    {
        return $this->bestSeller;
    }    
    public function setCount($count)
    {
        $this->count = $count;
    }
    
    public function getCount()
    {
        return $this->count;
    }    
    /**
     * Add relationProducts
     *
     * @param Itc\AdminBundle\Entity\Product\Product $relationProducts
     * @return $relations
     */
    public function addRelationProduct(\Itc\AdminBundle\Entity\Product\Product $relationProducts)
    {
        $this->relationProducts[] = $relationProducts;
    
        return $this;
    }

    /**
     * Get relationProducts
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRelationProducts()
    {
        return $this->relationProducts;
    }
    
    /**
     * Set src
     *
     * @param string $src
     * @return Image
     */
  /*  public function setSrc($src)
    {
        $this->src = $src;
    
        return $this;
    }
*/
    /**
     * Get src
     *
     * @return string 
     */
  /*  public function getSrc()
    {
        return $this->src;
    }
*/    /**
     * Set smallSrc
     *
     * @param string $smallSrc
     * @return Image
     */
  /*  public function setSmallSrc($smallSrc)
    {
        $this->smallSrc = $smallSrc;
    
        return $this;
    }
*/
    /**
     * Get smallSrc
     *
     * @return string 
     */
  /*  public function getSmallSrc()
    {
        return $this->smallSrc;
    }
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }
    
    public function getImage()
    {
        return $this->image;
    }
    public function setSmallImage($smallImage)
    {
        $this->smallImage = $smallImage;
        return $this;
    }
    
    public function getSmallImage()
    {
        return $this->smallImage;
    }
    /**
    * @ORM\PostPersist
    */
  /*  public function createSmallImage()
    {
        $imagine = new Imagine();
        $mode    = ImageInterface::THUMBNAIL_OUTBOUND;
        $size    = new Box(100, 100);
       
        $container = ItcAdminBundle::getContainer();
        $helper = 
            $container->get('vich_uploader.templating.helper.uploader_helper');
        $rootDir =  $container->get('kernel')->getRootDir();
        $relPath = $helper->asset($this, 'image');
        $pathParts = explode("/", $relPath);
        $folder = "";
        for($i = 0; $i < count($pathParts) - 1; $i++)
        {
            $folder .= $pathParts[$i]."/";
        }
        $pathToImage = $rootDir. "/../web".
                       $helper->asset($this, 'image');
        $this->smallSrc = "small_{$this->src}";
        $pathToSmall =$rootDir."/../web".
                      $folder.$this->smallSrc;
        $image = $imagine->open($pathToImage);
        if($image->getSize()->getWidth() > 100 || 
                $image->getSize()->getHeight() > 100){
            $this->smallImage = 
                    $image->thumbnail($size, $mode)->save($pathToSmall);
        }
        else
        {
            $this->smallImage = $image->save($pathToSmall);
        }
    }
    */
    /**
     * @ORM\PostPersist
     */
  /*  public function thumbImage()
    {
        $imagine = new Imagine();
        $mode    = ImageInterface::THUMBNAIL_INSET;
        $size    = new Box(1024, 800);
        
        $container = ItcAdminBundle::getContainer();
        $helper = 
            $container->get('vich_uploader.templating.helper.uploader_helper');
        $rootDir =  $container->get('kernel')->getRootDir();
        $pathToImage = $rootDir. "/../web".$helper->asset($this, 'image');
        $image = $imagine->open($pathToImage);
        if($image->getSize()->getWidth() > 1024 || $image->getSize()->getHeight() > 800){
            $image->thumbnail($size, $mode)
                  ->save($pathToImage);
        }
    }
    
*/    
}

