<?php

namespace Itc\AdminBundle\Entity\Product;
use Itc\AdminBundle\Entity\ContentProxy;

class ProductProxy extends ContentProxy
{
    public function setMetaDescription($metaDescription)
    {
        return $this->setTranslatedValue('metaDescription', $metaDescription);
    }
    public function getMetaDescription()
    {
         $metaDescription= $this->getTranslatedValue('metaDescription');
         if(null === $metaDescription)
         {
             $metaDescription = "";
         }
         return $metaDescription;
    }

    public function setMetaTitle($metaTitle)
    {
        return $this->setTranslatedValue('metaTitle', $metaTitle);
    }
    public function getMetaTitle()
    {
         $metaTitle= $this->getTranslatedValue('metaTitle');
         if(null === $metaTitle)
         {
             $metaTitle = "";
         }
         return $metaTitle;
    }
    public function setMetaKeyword($metaKeyword)
    {
        return $this->setTranslatedValue('metaKeyword', $metaKeyword);
    }
    public function getMetaKeyword()
    {
         $metaKeyword= $this->getTranslatedValue('metaKeyword');
         if(null === $metaKeyword)
         {
             $metaKeyword = "";
         }
         return $metaKeyword;
    }

}