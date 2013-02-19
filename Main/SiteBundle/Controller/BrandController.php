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
class BrandController extends ControllerHelper //Controller
{
    private $brand = array( 
        'ItcAdminBundle:Product\Brand',
        'ItcAdminBundle:Product\BrandTranslation'
    );
    /**
     * @Template()
     */
    public function BrandAction(){
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Brand')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->setMaxResults( 5 );

        $entities = $queryBuilder->getQuery()->execute();

        return array( 
            'entities' => $entities,
            'locale' => $locale
        );
    }
}
