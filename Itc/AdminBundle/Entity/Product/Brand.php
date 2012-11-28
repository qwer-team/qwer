<?php

namespace Itc\AdminBundle\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use \Itc\AdminBundle\Entity\Content;

/**
 * Itc\AdminBundle\Entity\Product\Brand
 * 
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 * @Vich\Uploadable
 */
class Brand extends Content{    

    protected $metaTitle;
    protected $metaDescription;
    protected $metaKeyword;
    
    protected $kod;    
     /**
     * @ORM\OneToMany(
     *     targetEntity="Product",
     *     mappedBy="brand",
     *     cascade={"remove"}
     * )
     */
    private $products;
    /**
    * @ORM\OneToMany(
    *     targetEntity="BrandTranslation",
    *     mappedBy="translatable",
    *     cascade={"persist"}
    * )
    */
    protected $translations;    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function setTranslit($translit)
    {
        $this->translit = $translit;
    
        return $this;
    }

    /**
     * Get translit
     *
     * @return string 
     */
    public function getTranslit()
    {
        return $this->translit;
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
     * Add translations
     *
     * @param \Itc\AdminBundle\Entity\Product\BrandTranslation $translations
     * @return Brand
     */
    public function addTranslation(\Itc\AdminBundle\Entity\Product\BrandTranslation $translations)
    {
        $this->translations[] = $translations;
    
        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Itc\AdminBundle\Entity\Product\BrandTranslation $translations
     */
    public function removeTranslation(\Itc\AdminBundle\Entity\Product\BrandTranslation $translations)
    {
        $this->translations->removeElement($translations);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslations()
    {
        return $this->translations;
    }
    /**
     * Add products
     *
     * @param \Itc\AdminBundle\Entity\Product\Product $products
     * @return Brand
     */
    public function addProduct(\Itc\AdminBundle\Entity\Product\Product $product)
    {
        $this->products[] = $product;
    
        return $this;
    }
    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }
}