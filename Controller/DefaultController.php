<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;

/**
 * Default controller.
 * @Route("/")
 */
class DefaultController extends Controller
{
    private $menu = array( 
        'ItcAdminBundle:Menu\Menu',
        'ItcAdminBundle:Menu\MenuTranslation'
    );
    /**
     * @Route("/", defaults={ "_locale" = "ru"}, name="index")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where('M.parent IS NULL')
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();
        $entity = $entities[0];
        
        $childrens = $entity->getChildren();
        
        $galleries = $entity->getGalleries();
        
        $images = array();
        $images = $galleries[0]->getImages();
        
        return array( 
            'entities'  => $entities,
            'entity'    => $entity,
            'images'    => $images,
            'childrens'  => $childrens,
        );
    }
    
    /**
     * @Route("/faq", name="faq")
     * @Template()
     */
    public function faqAction(){
        
        return array( 'entity' => array() );
    }
    /**
     * @Route("/{translit}" , name="content")
     * @Template()
     */
    public function contentAction($translit){
       
        $entity = $this->getEntityTranslit( $this->menu, $translit )
                       ->getOneOrNullResult();
         if (!$entity) {
            throw $this->createNotFoundException('Невозможно найти страницу.');
        }
        $keywords=$entity->getKeywords();
        return array( 'entity' => $entity, 
                      'keywords' =>$keywords
                    );
    }
    
    /**     
     * Lists all Menu entities.
     * @Template()
     */
    public function menuAction(){

        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $languages  = LanguageHelper::getLanguages();
        
        $request = $this->container->get('request')->getPathInfo();
//$routeName = $request->get('_route');
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $arr = $queryBuilder->getQuery()->execute();
        
        $entities = $child_entities = array();

        foreach($arr as $v)
            if (is_null($v->getParentId()) )
                $entities[] = $v;
                
        return array( 
            "entities"  => $entities,
            "locale"    => $locale,
            'locale'    => $locale,
            'languages' => $languages,
            'route'     => $request,
        );
    }
    
/************************ Вспомогательные методы ******************************/
    /**
     * Поиск по транслиту
     * @param string $entities - сущьность с транслитом описана в массиве
     * пример $this->menu;
     * @param string $translit - транслит для поиска
     * @return результат запроса
     */
    private function getEntityTranslit( $entities, $translit ){

        if( LanguageHelper::getLocale() == LanguageHelper::getDefaultLocale() ){

            $wheres[] = "M.translit = :translit";
            $parameters['translit'] = $translit;

        } else {

            $wheres[] = "M.value    = :translit";
            $wheres[] = "M.property = :property";
            
            $parameters['translit'] = $translit;
            $parameters['property'] = "translit";
        }

        return $this->getEntities( $entities, $wheres, $parameters );
    }
    /**
     * Вытягивет сущьность по критериям
     * @param type $entities - сущьность с транслитом описана в массиве
     * пример $this->menu;
     * 
     * @param array $wheres - массив с поиском [] = "M.locale = :locale" без AND;
     * $qb->where( implode( ' AND ', $wheres ) );
     * 
     * @param array $parameters - парметры поиска, обязательное условие
     * array( ['locale'] => $locale, ... )
     * 
     * @return $qb->getQuery();
     */
     
     
    private function getEntities( $entities, array $wheres = NULL, array $parameters = NULL ){
        
        list( $entity, $translation ) = $entities;

        $em            = $this->getDoctrine()->getManager();
        $locale        = LanguageHelper::getLocale();

        if( $locale == LanguageHelper::getDefaultLocale() ){

            $table = $entity;

        } else {
            
            $table = $translation;
            $wheres[] = "M.locale = :locale";
            $parameters['locale'] = $locale;
        }

        $qb = $em->getRepository( $table )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' );

        if( $wheres !== NULL ){

            $qb->where( implode( ' AND ', $wheres ) );
            $qb->setParameters( $parameters );

        }

        return $qb->getQuery();
    }
    
    
    
        private function getLocale()
    {
        $locale = $this->getRequest()->getLocale();
        return $locale;
    }
     /**
     * есть в ITC
     * @return type
     */
    private function getRoutes()
    {
        $router = $this->container->get( 'router' );
        
        $routes = array();

        foreach ( $router->getRouteCollection()->all() as $name => $route ){
            $routes[] = $name;
        }
        return $routes;
    }
    
}
