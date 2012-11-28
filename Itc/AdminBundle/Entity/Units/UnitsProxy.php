<?php

namespace Itc\AdminBundle\Entity\Units;
use \Gedmo\Translator\TranslationProxy;
use Symfony\Component\Validator\Constraints as Assert;

class UnitsProxy extends TranslationProxy
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
}