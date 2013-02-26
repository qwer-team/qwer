<?php

namespace Main\SiteBundle\Model;

use Doctrine\ORM\EntityRepository;
/**
 * @entity(repositoryClass="Main\SiteBundle\Model\BaseRepository")
 */
class Base {}

class BaseRepository extends EntityRepository {
    /************************ Вспомогательные методы ******************************/
    /**
     * Поиск сущности по роутингу
     * @param string $entities - сущьность с транслитом описана в массиве
     * пример $this->menu;
     * @param string $translit - транслит для поиска
     * @return результат запроса
     */
    protected function getEntityRouting( $entities, $routing, 
                                            array $wheres = NULL, 
                                            array $parameters = NULL,
                                            array $orderby = NULL ){
            $wheres[] = "M.routing = :routing";
            $parameters['routing'] = $routing;

        return $this->getEntities( $entities, $wheres, $parameters );
    }
    /**
     * Поиск сущности по транслиту
     * @param string $entities - сущьность с транслитом описана в массиве
     * пример $this->menu;
     * @param string $translit - транслит для поиска
     * @return результат запроса
     */
    protected function getEntityTranslit( $entities, $translit, 
                                            array $wheres = NULL, 
                                            array $parameters = NULL,
                                            array $orderby = NULL ){

        if( LanguageHelper::getLocale() == LanguageHelper::getDefaultLocale() ){

            $wheres[] = "M.translit = :translit";
            $parameters['translit'] = $translit;

        } else {

            $wheres[] = "T.value    = :translit";
            $wheres[] = "T.property = :property";
            
            $parameters['translit'] = $translit;
            $parameters['property'] = "translit";
        }

        return $this->getEntities( $entities, $wheres, $parameters );
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
     * $qb->where( implode( ' AND ', $wheres ) );
     * 
     * @param array $parameters - парметры поиска, обязательное условие
     * array( ['locale'] => $locale, ... )
     * 
     * @return $qb->getQuery();
     */
    protected function getEntities( $entities, array $wheres = NULL, 
                                               array $parameters = NULL, 
                                               array $orderby = NULL ){

        list( $entity, $translation ) = $entities;

        $em            = $this->_em;//getDoctrine()->getManager();
        $locale        = LanguageHelper::getLocale();

        if( $locale == LanguageHelper::getDefaultLocale() ){

            foreach( $wheres as $v ){
                $w[] = str_replace( "T.", "M.", $v );
            }

            $wheres = $w;

            $qb = $em->getRepository( $entity )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' );

        } else {

            $wheres[] = "T.locale = :locale";
            $parameters['locale'] = $locale;

            $qb = $em->getRepository( $entity )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' )
                     ->join( "M.translations", 'T' );
        }

        if( $wheres !== NULL ){

            $qb->where( implode( ' AND ', $wheres ) );
            $qb->setParameters( $parameters );

        }

        if( $orderby !== NULL ){

            list( $sort, $order ) = $orderby;
            $qb->orderBy( $sort, $order );
        }

        return $qb->getQuery();

    }
    //put your code here
}

?>
