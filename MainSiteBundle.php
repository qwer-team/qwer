<?php

namespace Main\SiteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MainSiteBundle extends Bundle
{
    private static $containerInstance = null; 
    public function setContainer(\Symfony\Component\DependencyInjection 
        \ContainerInterface $container = null) 
    { 
       parent::setContainer($container); 
       self::$containerInstance = $container; 
    } 
    /**
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface 
     */
    public static function getContainer() 
    { 
      return self::$containerInstance; 
    } 
}
