<?php

namespace Main\SiteBundle\Tools;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Itc\AdminBundle\Tools\LanguageHelper;


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ControllerHelper
 *
 * @author root
 */
class ControllerHelper extends Controller{
    /************************ Вспомогательные методы ******************************/
    /**
     * Поиск по транслиту
     * @param string $entities - сущьность с транслитом описана в массиве
     * пример $this->menu;
     * @param string $translit - транслит для поиска
     * @return результат запроса
     */
    protected function getEntityTranslit( $entities, $translit, array $wheres = NULL, array $parameters = NULL ){

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
     
     
    protected function getEntities( $entities, array $wheres = NULL, array $parameters = NULL ){
        
        list( $entity, $translation ) = $entities;

        $em            = $this->getDoctrine()->getManager();
        $locale        = LanguageHelper::getLocale();

        if( $locale == LanguageHelper::getDefaultLocale() ){

            $table = $entity;
            $qb = $em->getRepository( $table )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' );
        } else {
            
            $table = $translation;
            $wheres[] = "M.locale = :locale";
            $parameters['locale'] = $locale;
            $qb = $em->getRepository( $table )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M, T' )
                     ->join('M.translations', 'T',
                                'WITH', "T.locale = :locale");
        }
        /*
        $qb = $em->getRepository( $table )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' );
         */
        if( $wheres !== NULL ){

            $qb->where( implode( ' AND ', $wheres ) );
            $qb->setParameters( $parameters );

        }

        return $qb->getQuery();
    }
   /**
     * Вытягивет сущьность или сущность с прямыми потомками по критериям, 
     * возвращает также текущий язык.
     * @param type $routing - парметр поиска (обязательное условие)
     * 
     * @param type $translit - парметр поиска (не обязательное условие), 
     * если задано - вытягивает потомка, в противном случае сущность вместе с потомками и текущий routing
     * 
     * @return array 
    */ 
    protected function getPartners($routing, $translit=null)
    {
        $menu = array( 
                'ItcAdminBundle:Menu\Menu',
                'ItcAdminBundle:Menu\MenuTranslation'
                );
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
            
        $wheres[] = "M.routing = :routing";
        $routingf["routing"] = $routing;
        $entity = $this->getEntities( $menu, $wheres, $routingf )
                       ->getOneOrNullResult();
        if($translit==null)
        {
            return array( 
                'entity' => $entity,
                'partners'   => $entity->getChildren(),
                'locale' => $locale, 
                'routing' => $routing
                );
         }
         else
         {
             $wheres2[] = "M.parent_id = :parent_id";
             $parameters["parent_id"] = $entity->getId();
             $entity2 = $this->getEntityTranslit( $menu, $translit, $wheres2, $parameters )
                            ->getOneOrNullResult();
             return array( 
                 'entity' => $entity2,
                 'locale' => $locale
                    );
         }
    }
    
    /**
     * Для правого блока меню
     * 
     * @param type $parent_id
     * @return type 
     */
    protected function getMenus($parent_id){
        $em     = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $repo   = $em->getRepository('ItcAdminBundle:Menu\Menu');
        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale);
        if(null === $parent_id)
        {
            $qb->where('M.parent IS NULL');
        }
        else
        {
            $qb->where('M.parent = :parent')
               ->setParameter('parent', $parent_id);
        }
        return $qb->getQuery()->execute();
    }
    
    
        protected function getLocale()
    {
        $locale = $this->getRequest()->getLocale();
        return $locale;
    }
     /**
     * есть в ITC
     * @return type
     */
    protected function getRoutes()
    {
        $router = $this->container->get( 'router' );
        
        $routes = array();

        foreach ( $router->getRouteCollection()->all() as $name => $route ){
            $routes[] = $name;
        }
        return $routes;
    }
}

?>
