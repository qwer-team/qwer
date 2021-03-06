<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Itc\AdminBundle\Tools\ControllerHelper;

/**
 * News controller.
 * Routing registered in routing.yml
 */
class NewsController extends ControllerHelper //Controller
{
    protected  $menu = 'ItcAdminBundle:Menu\Menu';
    /**
     * @Route("/", name="news")
     * @Template()
     */
    public function indexAction( $translit )
    {
        $locale =  LanguageHelper::getLocale();

        $wheres[] = "M.routing = :routing ";
        $parameters["routing"] = "news";
        $wheres[] = "M.translit = :translit ";
        $parameters["translit"] = $translit;

        $orderby = array( "M.date_create", "DESC" );
        $entity = $this->getEntities( $this->menu, $wheres, $parameters, $orderby )
                       ->getOneOrNullResult();

        return array( 
            'entity' => $entity,
            'news'   => $entity->getChildren(),
            'locale' => $locale
        );
    }

    /**
     * @Route("/{translit}", name="onenews")
     * @Template()
     */
    public function onenewsAction( $translit )
    {

        $locale =  LanguageHelper::getLocale();

        $entity = $this->getEntityTranslit( $this->menu, $translit );

        return array( 
            'entity' => $entity,
            'locale' => $locale
        );
    }
}
