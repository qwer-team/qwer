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
class ControllerHelper extends Controller
{

    protected $menu = 'ItcAdminBundle:Menu\Menu';

/************************ Вспомогательные методы ******************************/
    /**
     * Поиск сущности по роутингу
     * @param string $entities - сущьность с транслитом описана в массиве
     * пример $this->menu;
     * @param string $translit - транслит для поиска
     * @return результат запроса
     */
    protected function getEntityRouting($entities, $routing)
    {
            $wheres[] = "M.routing = :routing";
            $parameters['routing'] = $routing;

        return $this->getEntities($entities, $wheres, $parameters)
                    ->setMaxResults(1)
                    ->getOneOrNullResult();
    }
    
    /**
     * Поиск сущности по транслиту
     * @param string $entities - сущьность с транслитом описана в массиве
     * пример $this->menu;
     * @param string $translit - транслит для поиска
     * @return результат запроса
     */
    protected function getEntityTranslit($entities, $translit)
    {
        $locale = LanguageHelper::getLocale();
        if($locale == LanguageHelper::getDefaultLocale()){
            $wheres[] = "M.translit = :translit";
            $parameters['translit'] = $translit;
            $entity = $this->getEntities($entities, $wheres, $parameters)
                           ->setMaxResults(1)
                           ->getOneOrNullResult();

        } else {
            list($entity, $translation) = (!is_array($entities))? 
                array($entities, $entities."Translation"): $entities;

            $parameters['locale']   = $locale;
            $parameters['value']    = $translit;
            $parameters['property'] = "translit";

            $em = $this->getDoctrine()->getManager();
            $qb = $em->getRepository($translation)
                     ->createQueryBuilder('T')
                     ->select('T')
                     ->where("   T.value    = :value")
                     ->andWhere("T.locale   = :locale")
                     ->andWhere("T.property = :property")
                     ->setParameters($parameters)
                     ->getQuery();

            $ent = $qb->getOneOrNullResult();
            $entity = $ent->getTranslatable();
        }

        return $entity;
    }
    /**
     * Вытягивет сущьность по критериям
     * 
     * !!! Переводимые поля должны быть T.
     * !!! Непереводимые M.
     * 
     * Можно прописать или вытягивать переводимые/непереводимые поля в массив, 
     * но это потом...
     * 
     * @param type $entities - сущьность с транслитом описана в массиве
     * пример $this->menu;
     * 
     * @param array $wheres - массив с поиском [] = "M.locale = :locale" без AND;
     * $qb->where(implode(' AND ', $wheres));
     * 
     * @param array $parameters - парметры поиска, обязательное условие
     * array(['locale'] => $locale, ...)
     * 
     * @return $qb->getQuery();
     */
    protected function getEntities($entities, array $wheres     = NULL, 
                                               array $parameters = NULL, 
                                               array $orderby    = NULL)
    {
        list($entity, $translation) = (!is_array($entities))? 
                array($entities, $entities."Translation"): $entities;
            
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository($entity)
                 ->createQueryBuilder('M')
                 ->select('M');

        if($wheres)     $qb->where(implode(' AND ', $wheres));
        if($parameters) $qb->setParameters($parameters);

        if($orderby !== NULL){
            list($sort, $order) = $orderby;
            $qb->orderBy($sort, $order);
        }

        $query =  $qb->getQuery();

        return $query;
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
        $router = $this->container->get('router');
        
        $routes = array();

        foreach ($router->getRouteCollection()->all() as $name => $route){
           $routes[] = $name;
          
        }
        return $routes;
    }

    protected function getController($name){

        return $this->container->get('router')
                    ->getRouteCollection()
                    ->get($name)
                    ->getDefault("_controller");
    }
    
}

?>
