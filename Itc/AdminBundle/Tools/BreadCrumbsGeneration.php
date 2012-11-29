<?php

namespace Itc\AdminBundle\Tools;
class BreadCrumbsGeneration {
    
    public static function generate( $start_id, $fields, $routing, $repository, 
                                     $breadcrumbs, $router, $locale)
    {
        $arr =( is_array( $fields ) ) ? $fields: array( $fields => null );

        $breadcrumbs->addItem("Home", 
        $router->generate( $routing, $arr ) );

        $helpArray = array();
        while($start_id){
            if(null === $start_id) continue;

            $parent = $repository->findOneById($start_id);
            array_unshift($helpArray, 
                          array("title" => 
                             $parent->translate($locale)->getTitle(),
                                 "parent_id" =>  $parent->getId(),
                                )
                           );
            $start_id = $parent->getParentId();
        }
        foreach( $helpArray as $h )
        {
            $arr = array() ;
            if( is_array( $fields ) ){

                foreach( $fields as $k => $v ){
                    $arr[$k] = is_null( $v ) ? $h['parent_id']: $v;
                }
            } else {

                $arr[$fields] = $h[$fields];
            }
            $breadcrumbs->addItem( $h["title"], 
                                   $router->generate( $routing, $arr )
                          );
        }
        //возвращать ничего не надо, бандл генерящий крошки сам все найдет
    }
}

?>
