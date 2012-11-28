<?php

namespace Itc\AdminBundle\Entity;
use \Gedmo\Translator\TranslationProxy;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of ContentProxy
 *
 * @author root
 */
class ContentProxy extends TranslationProxy
{
    public function setTitle($name)
    {
        return $this->setTranslatedValue('title', $name);
    }
    
    public function getTitle()
    {
         $title= $this->getTranslatedValue('title');
         if(null === $title)
         {
             $title = "";
         }
         return $title;
    }
    public function setDescription($description)
    {
        return $this->setTranslatedValue('description', $description);
    }
    public function getDescription()
    {
         $description= $this->getTranslatedValue('description');
         if(null === $description)
         {
             $description = "";
         }
         return $description;
    }
    public function setContent($content)
    {
        return $this->setTranslatedValue('description', $content);
    }
    public function getContent()
    {
         $content= $this->getTranslatedValue('description');
         if(null === $content)
         {
             $content = "";
         }
         return $content;
    }
    
    public function setTranslit($name)
    {
        return $this->setTranslatedValue('translit', $name);
    }
    /**
     * @Assert\MinLength(10)
     */
    public function getTranslit()
    {
         $title= $this->getTranslatedValue('translit');
         return $title;
    }
    
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