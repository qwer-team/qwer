<?php

namespace Itc\AdminBundle\Entity;
class TranslatableEntity {
    protected $fields;
    protected $defaultLocale = "ru";
    protected $translations;


    public function __construct()
    {
       $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function translate($locale)
    {
        if ('ru' == $locale) {
            return $this;
        }
        $className = get_class($this);
        $className = str_replace("Proxies\__CG__", "", $className);
        $proxyClass = $className.'Proxy';
        return new $proxyClass($this,
        /* Locale                            */ $locale,
        /* List of translatable properties:  */ $this->fields,
        /* Translation entity class:         */ $className.'Translation',
        /* Translations collection property: */ $this->translations
        );
    }
    
    public function getRuTranslation()
    {
        return $this->translate('ru');
    }
    
    public function getEnTranslation()
    {
        return $this->translate('en');
    }
    public function getUaTranslation()
    {
        return $this->translate('ua');
    }
    
    public function getItTranslation()
    {
        return $this->translate('it');
    }
    
    function __toString(){
        return is_null( $this->title ) ? "" : $this->title ;
    }
}

?>
