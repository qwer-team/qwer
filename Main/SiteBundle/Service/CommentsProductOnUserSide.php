<?php

namespace Main\SiteBundle\Service;

use Itc\AdminBundle\Listener\ContainerAware;
use Itc\AdminBundle\Entity\Comments\ProductComments;

class CommentsProductOnUserSide extends ContainerAware
{
    /**
     * 
     * @param type $prodId
     * @param type $coulonpage
     * @param type $page
     * @return type
     */
    public function getProductComents($prodId, $coulonpage, $page)
    {
        $locale =  $this->container->get('request')->getLocale();
        $qb= $this->em->getRepository('ItcAdminBundle:Comments\ProductComments')
                      ->getCommentToProd($prodId, $locale);               
        $paginator = $this->container->get('knp_paginator');
        $entities = $paginator->paginate(
                $qb,
                $this->container->get('request')
                     ->query->get('page', $page)/*page number*/,
                $coulonpage/*limit per page*/);  

        return $entities;
    }
}