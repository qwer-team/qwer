<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Main\SiteBundle\Tools\ControllerHelper;

/**
 * News controller.
 * Routing registered in routing.yml
 */
class NewsController extends ControllerHelper //Controller
{
    private $menu = array( 
        'ItcAdminBundle:Menu\Menu',
        'ItcAdminBundle:Menu\MenuTranslation'
    );
    /**
     * @Route("/", name="news")
     * @Template()
     */
    public function indexAction()
    {
        $locale =  LanguageHelper::getLocale();

        $wheres[] = "M.routing = :routing ";
        $parameters["routing"] = "news";
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

        $entity = $this->getEntityTranslit( $this->menu, $translit )
                       ->getOneOrNullResult();

        return array( 
            'entity' => $entity,
            'locale' => $locale
        );
    }
}
