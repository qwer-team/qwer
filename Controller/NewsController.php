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
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();

        $wheres[] = "M.routing = :routing ";
        $parameters["routing"] = "news";
        $entity = $this->getEntities( $this->menu, $wheres, $parameters )
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

        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();

        $wheres[] = "M.parent_id = :parent_id";
        $parameters["parent_id"] = 26;

        $entity = $this->getEntityTranslit( $this->menu, $translit, $wheres, $parameters )
                       ->getOneOrNullResult();
        if( ! $entity ) echo "llalaa";
        return array( 
            'entity' => $entity,
            'locale' => $locale
        );
    }
}
