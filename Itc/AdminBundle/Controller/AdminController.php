<?php

namespace Itc\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Admin controller.
 * @Route("/")
 */
class AdminController extends Controller {

    /**
     * Lists all MenuSys entities
     * @Route("/", defaults={ "_locale" = "ru"}, name="admin_index")
     * @Template()
     */
    public function indexAction() {
        
        return array("vassa" => "vassa");
    }
    
    /**
     
     * Lists all MenuSys entities.
     * @Template()
     */
    public function menuAction(){
        $em = $this->getDoctrine()->getManager();
        $locale =  $this->getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')
                        ->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->orderBy('M.kod', 'ASC');        
        //$queryBuilder->where( 'M.parent IS NULL ');
        $arr = $queryBuilder->getQuery()
                             ->execute();
        
        $entities = $child_entities = array();
        foreach($arr as $v) 
            if (is_null($v->getParentId()) )
                $entities[] = $v;
            else 
                array_push ($child_entities, $v );
                
        return array( 
            "entities" => $entities,
            "locale" => $this->getLocale(),
            "routes" => $this->getRoutes(),
            "child_entities" => $child_entities
        );
    }
    
    private function getLocale()
    {
        $locale = $this->getRequest()->getLocale();
        return $locale;
    }

    private function getRoutes()
    {
        $router = $this->container->get('router');
        
        $routes = array();
        foreach ($router->getRouteCollection()->all() as $name => $route)
        {
            $routes[] = $name;
        }
        return $routes;
    }
}

?>