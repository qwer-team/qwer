<?php

namespace Itc\AdminBundle\Tools;

class LanguageHelper {
    public static function getLocale()
    {
        $container = \Itc\AdminBundle\ItcAdminBundle::getContainer();
        $locale = $container->get('request')->getLocale();
        return $locale;
    }
    
    public static function getLanguages()
    {
        $em = \Itc\AdminBundle\ItcAdminBundle::getContainer()
                                    ->get('doctrine')->getManager();
        $languages = $em->getRepository('ItcAdminBundle:Languages')
                               ->findAll();
        $langs = NULL;//array();//Если языков нету валится сайт без объявления.
        foreach( $languages as $k => $v ){
            $langs[] = $v->getLang() ;
        }
        $langs = ( is_null( $langs ) ) ? array( self::getLocale() ): $langs;
        return $langs;
    }
    public static function getDefaultLocale(){
        $container = \Itc\AdminBundle\ItcAdminBundle::getContainer();
        return $container->parameters["locale"];
    }
    
}

?>
