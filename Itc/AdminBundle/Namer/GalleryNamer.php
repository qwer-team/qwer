<?php


namespace Itc\AdminBundle\Namer;
use Vich\UploaderBundle\Naming\NamerInterface;

class GalleryNamer implements NamerInterface{
     
    public function name($obj, $field) {
        $name =  sha1(mt_rand(0, 100000 ))."_".
                    $obj->getImage()->getClientOriginalName();
        $obj->setSmallSrc("small_".$name);
        return $name;
    }
    
}

