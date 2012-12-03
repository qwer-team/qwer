<?php

namespace Itc\AdminBundle\Entity;
use Gedmo\Translator\TranslationProxy as TP;

class TranslationProxy extends TP {
    
    public function __get($property)
    {
        if (in_array($property, $this->properties)) {
            if (method_exists($this->translatable, $getter = 'get'.ucfirst($property))) {
                //echo $getter;
                return $this->translatable->$getter();
            }

            return $this->getTranslatedValue($property);
        }

        return $this->translatable->$property;
    }
}