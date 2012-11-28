<?php

namespace Itc\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ItcAdminBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
    private static $containerInstance = null; 
    public function setContainer(\Symfony\Component\DependencyInjection 
        \ContainerInterface $container = null) 
    { 
       parent::setContainer($container); 
       $container->get("twig")->addExtension( new \Twig_Extension_StringLoader);
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
