<?php
namespace Itc\AdminBundle\Namer;
use Vich\UploaderBundle\Naming\NamerInterface;

class ImageNamer implements NamerInterface{
     
    public function name($obj, $field) {
        $method = "get".ucfirst($field);
        return $obj->$method()->getClientOriginalName();
    }
    
}
