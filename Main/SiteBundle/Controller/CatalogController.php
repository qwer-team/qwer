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
 * Catalog controller.
 * Routing registered in routing.yml
 */
class CatalogController extends ControllerHelper //Controller
{
    private $categories = array( 
        'ItcAdminBundle:Product\ProductGroup',
        'ItcAdminBundle:Product\ProductGroupTranslation'
    );
    /**
     * @Template()
     */
    public function CategoriesBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        return array( 
            'entities' => $entities,
            'locale' => $locale
        );
    }
    /**
     * @Template()
     */
    public function NoveltyBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->Where('M.novelty=1')
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        return array( 
            'entities' => $entities,
            'locale' => $locale
        );
    }
    /**
     * @Template()
     */
    public function BestSellerBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->Where('M.bestSeller=1')
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        return array( 
            'entities' => $entities,
            'locale' => $locale
        );
    }
    /**
     * @Template()
     */
    public function TopSalesBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->Where('M.topSales=1')
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        return array( 
            'entities' => $entities,
            'locale' => $locale
        );
    }

}
