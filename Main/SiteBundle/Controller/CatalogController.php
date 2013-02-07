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
     * @Route("catalog/{translit}/", name="catalog")
     * @Template()
     */
    public function CurrentCatalogAction( $translit )
    {
      $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $entity = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->Where('M.translit=:translit')
                        ->setParameter('translit', $translit)
                        ->setParameter('locale', $locale)
                        ->getQuery()->getOneOrNullResult();
        $entities = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->orderBy('M.kod', 'ASC')
                        ->where('M.productGroup = :productGroup')
                        ->setParameter('productGroup', $entity->getId())
                        ->getQuery()->execute();
        return array( 
            'entity'     => $entity,
            'entities'   => $entities,
            'locale'     => $locale
        );
    }
    
     /**
     * @Route("product/{translit}/", name="product")
     * @Template()
     */
    public function OneProductAction( $translit )
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        
        $entity = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->Where('M.translit=:translit')
                        ->setParameter('translit', $translit)
                        ->setParameter('locale', $locale)
                        ->getQuery()->getOneOrNullResult();
        
        $relative=$em->getRepository('ItcAdminBundle:Product\RelationsProdToProd')
                        ->createQueryBuilder('M')
                        ->select('M')
                        ->where('M.prod_id = :prod_id')
                        ->setParameter('prod_id', $entity->getId())
                        ->orderBy('M.kod', 'ASC')
                        ->getQuery()->execute();
        
        $relatives=$galary=$galary_images=array();
        
        foreach($relative as $rel){
            $relatives[]=$rel->getRelProd();
        }
        
        $galary=$em->getRepository('ItcAdminBundle:Product\ProductGalary')
                        ->createQueryBuilder('M')
                        ->select( 'M' )
                        ->Where('M.productId=:id')
                        ->setParameter('id', $entity->getId())
                        ->getQuery()->getOneOrNullResult();
        if(is_object($galary)){
        $galary_images=$em->getRepository('ItcAdminBundle:Product\ProductImage')
                        ->findByGallery($galary->getId());
        }
        
        return array( 
            'entity'     => $entity,
            'relatives'  => $relatives,
            'images'     => $galary_images,
            'locale'     => $locale
        );
    }
    
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
    /**
     * @Route("search/", name="search_in_site")
     * @Template()
     */
    public function SearchPageAction()
    {
        $em = $this->getDoctrine()->getManager();
        if($_POST['productid']!=''){
            $product = $em->getRepository('ItcAdminBundle:Product\Product')->find($_POST['productid']);
             return $this->redirect($this->generateUrl('product', array('translit' => $product->getTranslit())));
        }
    }
    /**
     * @Template()
     */
   public function SmallCartAction(){
       $col=0;
       $sum=0;
       $cart="";
       if($this->getCartSession()!=''){
        $cart=$this->getCartSession();
       foreach($this->getCartSession() as $product){
           $col=$col+1;
           $sum=$sum+$product['price'];
       }}
        return array( 
            'cart'  => $cart, 
            'sum'   => $sum, 
            'col'   => $col );
    }
   private function getCartSession(){

        return $this->getRequest()->getSession()->get('cart_user');
    }
}