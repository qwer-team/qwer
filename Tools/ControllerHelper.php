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

        $qb = $em->getRepository( $entity );

        if( $locale == LanguageHelper::getDefaultLocale() ){
            $qb->select( 'M' );
        } else {

            $wheres[] = "T.locale = :locale";
            $parameters['locale'] = $locale;
            
             $qb->select( 'M, T' )
                ->join('M.translations', 'T');
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
