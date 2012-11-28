<?php

namespace Itc\AdminBundle\Entity\Keyword;
use \Gedmo\Translator\TranslationProxy;


class KeywordProxy extends TranslationProxy {
    
    public function setKeyword($name)
    {
        return $this->setTranslatedValue('keyword', $name);
    }
    
    public function getKeyword()
    {
         $keyword= $this->getTranslatedValue('keyword');
         if(null === $keyword)
         {
             $keyword = "";
         }
         return $keyword;
    }
}

?>