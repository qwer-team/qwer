<?php

namespace Main\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Itc\AdminBundle\Tools\ControllerHelper;
use Main\SiteBundle\Form\AcceptOrderType;
use Main\SiteBundle\Form\ProductCommentsType;
use Itc\AdminBundle\Entity\Comments\ProductComments;
use Main\SiteBundle\Event\ProductCommentsEvent;

/**
 * Catalog controller.
 * Routing registered in routing.yml
 */
class CatalogController extends ControllerHelper //Controller
{
    const VIEW_CATALOG = "view_catalog";
    const SORT_CATALOG = "sort_catalog";
    const TYPE_CATALOG = "type_catalog";

    public function CurrentUser() {
      
       $securityContext = $this->container->get('security.context');
       if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
             $user= $securityContext->getToken()->getUser();
       }else{ $user=""; }
       return $user;
    }

    /**
     * @Route("catalog/{translit}/{sort}/{sortType}/{coulonpage}/{page}", name="catalog",
     * requirements={"coulonpage" = "\d+","page"="\d+"}, 
     * defaults={ "sort" = "kod", "sortType" = "ASC", "coulonpage" = "10", "page"=1})
     * @Template()
     */
    public function CurrentCatalogAction($translit, $page, $sort = 'kod', $sortType = 'ASC',  $coulonpage = 10, $view = NULL)
    {
        return $this->getCurrentCatalog($this->productGroup, $translit, $page, $sort, $sortType , $coulonpage);
    }
    
    private function getCurrentCatalog($entName, $translit, $page, $sort, $sortType, $coulonpage, $param=null)
    {
        $wheres = NULL;
        $params = NULL;
        $route = NULL;

        $entity = $this->getEntityTranslit($entName, $translit);

        if($entity && $param === NULL){
            $wheres[] = "M.productGroup = :productGroup";
            $params['productGroup'] = $entity->getId();
        }

        if ($param !== NULL)
            $wheres[] = "M.$param = 1";

        $order = array('M.' . $sort, $sortType);

        $entities = $this->getEntities($this->product, $wheres, $params, $order);

        $products = $this->paginator($entities, $page, $coulonpage);

        $totalPages = ceil($products->getTotalItemCount() / $coulonpage);

        return array(
            'entity'      => $entity,
            'entities'    => $products,
            'locale'      => LanguageHelper::getLocale(),
            'sort'        => $sort,
            'coulonpage'  => $coulonpage,
            'page'        => $page,
            'translit'    => $translit,
            'total_pages' => $totalPages,
            'route' => $route
        );
    }

    /**
     * @Route(
     *  "catalogup/{translit}/{page}/{coulonpage}/{sort}/{type}/{view}", 
     *  name="catalogup",
     *  requirements={"sort"="\w+", "type"="\w+", "coulonpage"="\d+", "page"="\d+", "view"="\w+"}, 
     *  defaults={"sort"=NULL, "type"=NULL, "coulonpage"="6", "page"=1, "view"=NULL}
     * )
     * @Template()
     */
    public function CurrentCatalogUpAction($translit, $page = 1, $sort = NULL, $type = NULL, $coulonpage = 6, $view = NULL)
    {
        $sort = $this->checkParamSession(self::SORT_CATALOG, $sort, "kod");
        $view = $this->checkParamSession(self::VIEW_CATALOG, $view, "list");
        $type = $this->checkParamSession(self::TYPE_CATALOG, $type, "ASC");
        
        $res = $this->getCurrentCatalog($this->productGroup, $translit, $page, $sort, $type, $coulonpage);
        $res['view'] = $view;
        $res['type'] = $type;

        return $res;
    }

    /**
     * @Route(
     *  "bestsellers/{translit}/{page}/{coulonpage}/{sort}/{type}/{view}", 
     *  name="bestsellers",
     *  requirements={"sort"="\w+", "type"="\w+", "coulonpage"="\d+", "page"="\d+", "view"="\w+"}, 
     *  defaults={"sort"=NULL, "type"=NULL, "coulonpage"="6", "page"=1, "view"=NULL}
     * )
     * @Template("MainSiteBundle:Catalog:CurrentCatalogUp.html.twig")
     */
    public function BestSellerAction($translit, $page = 1, $sort = NULL, $type = NULL, $coulonpage = 6, $view = NULL)
    {

        $sort = $this->checkParamSession(self::SORT_CATALOG, $sort, "kod");
        $view = $this->checkParamSession(self::VIEW_CATALOG, $view, "list");
        $type = $this->checkParamSession(self::TYPE_CATALOG, $type, "ASC");

        $res = $this->getCurrentCatalog($this->menu, $translit, $page, $sort, $type, $coulonpage, 'bestSeller');
        $res['entity'] = $this->GetMenuRouting(self::BESTSELLERS);
        $res['view']   = $view;
        $res['type']   = $type;
        $res['route']  = $res['entity']->getRouting();

        return $res;
    }

    private function checkParamSession($name, $param, $default)
    {

        /* записываем в сессию текущий вид страници */

        if ($param === NULL) {
            $param = $this->getSession($name);
            if ($param === NULL)
                $param = $default;
        }

        $this->setSession($name, $param);

        return $param;
    }

    private function getSession($name)
    {
        return $this->getRequest()->getSession()->get($name);
    }

    private function setSession($name, $value)
    {
        return $this->getRequest()->getSession()->set($name, $value);
    }

    /**
     * @Route("/product/{translit}", name="product")
     * @Template()
     */
    public function OneProductAction($translit)
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        
        $entity = $this->getEntityTranslit($this->product, $translit);

        $keywords = $entity->getKeywords();
        
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

        $galary = $em->getRepository('ItcAdminBundle:Product\ProductGalary')
        ->createQueryBuilder('M')
        ->select('M')
        ->Where('M.productId=:id')
        ->setParameter('id', $entity->getId())
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();

        if (is_object($galary)) {

            $galary_images = $em->getRepository('ItcAdminBundle:Product\ProductImage')
            ->findByGallery($galary->getId());
        }

        $menuCatalog = $this->GetCategory();

        $breadCrumbs = array('categories'    => $menuCatalog, 
                             'catalogup'     => $entity->getProductGroup()->translate($locale), 
                             'product'       => $entity->translate($locale)
                       );

        return array(
            'entity' => $entity,
            'relatives' => $relatives,
            'images' => $galary_images,
            'locale' => $locale,
            'keywords' => $keywords,
            'bread_crumbs' => $breadCrumbs,
        );
    }

    /**
     * 
     * @param type $parent_id
     * @param type $coulonpage
     * @param type $page
     * @return type
     * @Route("/{coulonpage}/{page}/{parent_id}", name="productcomments",
     * requirements={"parent_id" = "\d+", "coulonpage" = "\d+","page" = "\d+"}, 
     * defaults={ "parent_id" = null, "coulonpage" = "10", "page"=1})
     * @Template()
     */
    public function ProductCommentsAction($parent_id, $coulonpage = 10, $page = 1)
    {
        $productComentsCreator    = $this->container->get("get.product.comments");
        $entities    = $productComentsCreator->getProductComents(
            $parent_id,
            $coulonpage,
            $page
        );
        $entity = array();
        $form = array();
        $isAuthenticated= false;
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $isAuthenticated= true;
            $entity = new ProductComments();
            $form = $this->createForm(new ProductCommentsType(), $entity)
                         ->createView();
        }

        return array(
            'entities' => $entities,
            'newEntity' => $entity,
            'isAuthenticated' => $isAuthenticated,
            'form' => $form,
            'parent_id' => $parent_id,
            'coulonpage' => $coulonpage,
            'page' => $page,
            'message' => null,
            'route' => 'productcomments',
        );
    }

    /**
     * Creates a new Comments\ProductComments entity.
     *
     * @Route("/create/{coulonpage}/{page}/{parent_id}", name="comments_in_product_create",
     * requirements={"parent_id" = "\d+", "coulonpage" = "\d+","page" = "\d+"}, 
     * defaults={ "parent_id" = null, "coulonpage" = "10", "page"=1})
     * @Template("MainSiteBundle:Catalog:ProductComments.html.twig")
     */
    public function createAction(Request $request, $parent_id, $coulonpage = 10, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new ProductComments();
        
        $locale = LanguageHelper::getLocale();
        $entity->setLang($locale);

        $product = $em->getRepository('ItcAdminBundle:Product\Product')
        ->find($parent_id);
        $entity->setProduct($product);

        $entity->setVisible(1);

        $Autor = $this->get('security.context')->getToken()->getUser();
        $entity->setAutor($Autor);

        $form = $this->createForm(new ProductCommentsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();
            $array = $this->ProductCommentsAction($parent_id);
            $array['message'] = 'good';
            return $array;
            //return $this->redirect($this->generateUrl('productcomments_edit', array('id' => $entity->getId())));
        }
        $array = $this->ProductCommentsAction($parent_id);
        $array['message'] = 'bad';
        return $array;
    }

    /**
     * @Route("/producttag/{keyword}",  name="product_tag")
     * @Template()
     */
    public function ProductTagAction($keyword)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = LanguageHelper::getLocale();
        $deflocale = LanguageHelper::getDefaultLocale();

        $entity = $this->getEntityTranslit('ItcAdminBundle:Keyword\Keyword', $translit)
        ->getOneOrNullResult();
//        if($locale == $deflocale){
//        $entity=$em->getRepository('ItcAdminBundle:Keyword\Keyword')
//                       ->createQueryBuilder('M')
//                        ->select('M')
//                        ->where("M.translit = :translit ")
//                        ->setParameter('translit', $keyword)
//        ->getQuery()->getOneOrNullResult();    
//        }else{
//        $entity=$em->getRepository('ItcAdminBundle:Keyword\Keyword')
//                       ->createQueryBuilder('M')
//                        ->select('M, T')
//                        ->leftJoin('M.translations', 'T',
//                        'WITH', "T.locale = :locale")
//                        ->setParameter('locale', $locale)
//                        ->where("T.property='translit'")
//        ->getQuery()->getOneOrNullResult();    
//        }
        if (!$entity) {
            throw $this->createNotFoundException('The keyword does not exist');
        }
        $entities = $entity->getProducts();
        return array('entity' => $entity,
            'entities' => $entities
        );
    }

    /**
     * @Route("/{translit}",  name="categories")
     * @Template()
     */
    public function CategoriesPageAction()
    {

        return $this->CategoriesBlockAction();
    }

    /**
     * @Template()
     */
    public function CategoriesBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale = LanguageHelper::getLocale();

        $entity = $this->GetCategory();

        $entities = $em->getRepository($this->productGroup)
        ->createQueryBuilder('M')
        ->select('M')
//                        ->leftJoin('M.translations', 'T',
//                                'WITH', "T.locale = :locale")
        ->orderBy('M.kod', 'ASC')
//                        ->setParameter('locale', $locale)
        ->getQuery()->execute();

        return array(
            'entity' => $entity,
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
        $locale = LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Product')
        ->createQueryBuilder('M')
        ->select('M, T')
        ->leftJoin('M.translations', 'T', 'WITH', "T.locale = :locale")
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
    public function CategoriesRightBlockAction($currentGroupId = false, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
        ->createQueryBuilder('M')
        ->select('M, T')
        ->leftJoin('M.translations', 'T', 'WITH', "T.locale = :locale")
        ->orderBy('M.kod', 'ASC')
        ->setParameter('locale', $this->getLocale())
        ->getQuery()
        ->execute();

        return array(
            'currentGroupId' => $currentGroupId,
            'categories' => $categories,
        );
    }

    /**
     * @Route()
     * @Template()
     */
    public function BestSellerBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale = LanguageHelper::getLocale();
        $entities = $em->getRepository($this->product)
        ->createQueryBuilder('M')
        ->select('M, T')
        ->leftJoin('M.translations', 'T', 'WITH', "T.locale = :locale")
        ->Where('M.bestSeller=1')
        ->orderBy('M.kod', 'ASC')
        ->setParameter('locale', $locale)
        ->getQuery()
        ->execute();

        $category = $this->GetMenuRouting(self::BESTSELLERS);

        return array(
            'entities' => $entities,
            'locale' => $locale,
            'category' => $category, //->translate($locale)
        );
    }

    /**
     * @Route()
     * @Template()
     */
    public function BestSellerSmallBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale = LanguageHelper::getLocale();
        $entities = $em->getRepository($this->product)
        ->createQueryBuilder('M')
        ->select('M')
//                           ->leftJoin('M.translations', 'T',
//                                      'WITH', "T.locale = :locale")
        ->Where('M.bestSeller=1')
        ->orderBy('M.kod', 'ASC')
//                           ->setMaxResults(2)
//                           ->setParameter('locale', $locale)
        ->getQuery()
        ->execute();
        shuffle($entities);

        $entities = array_slice($entities, 1, 2);

        $category = $this->GetMenuRouting(self::BESTSELLERS);

        return array(
            'entities' => $entities,
            'locale' => $locale,
            'category' => $category->translate($locale),
        );
    }

    private function GetMenuRouting($routing)
    {

        return $this->getEntityRouting($this->menu, $routing);
    }

    private function GetCategory()
    {

        return $this->GetMenuRouting(self::CATEGORIES);
    }

    /**
     * @Template()
     */
    public function BrandAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale = LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Brand')
        ->createQueryBuilder('M')
        ->select('M, T')
        ->leftJoin('M.translations', 'T', 'WITH', "T.locale = :locale")
        ->setParameter('locale', $locale)
        ->setMaxResults(5);

        $entities = $queryBuilder->getQuery()->execute();

        return array(
            'entities' => $entities,
            'locale' => $locale
        );
    }
    
    /**
     * @Template()
     */
    public function TopSalesSliderAction()
    {

        return $this->TopSalesBlockAction();
    }
    /**
     * @Template()
     */
    public function TopSalesBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale = LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository($this->product)
        ->createQueryBuilder('M')
        ->select('M')
//                        ->leftJoin('M.translations', 'T',
//                                'WITH', "T.locale = :locale")
        ->Where('M.topSales=1')
        ->orderBy('M.kod', 'ASC');
//                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        $category = $this->GetCategory();

        return array(
            'entities' => $entities,
            'locale' => $locale,
            'category' => $category,
        );
    }

    /**
     * @Route("recommend_product_site/{id}", name="recommend_product_site")
     * @Template()
     */
    public function RecommendProductsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('ItcAdminBundle:Product\RelationsProdToProd')
                       ->createQueryBuilder('M')
                       ->select('M, P')
                       ->join("M.rel_prod", "P")
                       ->where('M.prod_id = :prod_id')
                       ->setParameter('prod_id', $id)
                       ->orderBy('M.kod', 'ASC')
                       ->getQuery()
                       ->execute();
        return array(
            'relative_products' => $products,
            'locale'     => LanguageHelper::getLocale(),
        );
    }

    /**
     * @Route("search/", name="search_in_site")
     * @Template()
     */
    public function SearchPageAction()
    {
        $em = $this->getDoctrine()->getManager();

        $val = explode("  ", $_POST['q']);

        $variable = isset($val[1]) ? $val[1] : $val[0];

        $members = $qb = $em->getRepository('ItcAdminBundle:Product\Product')
        ->createQueryBuilder('M')
        ->select('M');
        $qb->where("M.title LIKE :value");
        $qb->setParameter('value', "%" . $variable . "%");

        $members = $qb->getQuery()->execute();
        if (count($members) == 1) {
            return $this->redirect($this->generateUrl('product', array('translit' => $members[0]->getTranslit())));
        } else {
            return array(
                'entities' => $members
            );
        }
    }

    /**
     * @Template()
     */
    public function SmallCartAction()
    {
        $col = 0;
        $sum = 0;
        $cart = "";
        if ($this->getCartSession() != '') {
            $cart = $this->getCartSession();
            foreach ($this->getCartSession() as $product) {
                $col = $col + 1;
                $sum = $sum + $product['price'];
            }
        }
        return array(
            'cart' => $cart,
            'sum' => $sum,
            'col' => $col);
    }

    private function getCartSession()
    {

        return $this->getRequest()->getSession()->get('cart_user');
    }

    /**
     * @Route("/add_cart_wrap/{id}/{amount}", 
     *  defaults={"amount"=1},
     *  name="add_cart_wrap"
     * )
     * @Template("MainSiteBundle:Catalog:addCartWrap.json.twig")
     */
    public function AddCartWrapAction($id, $amount = 1)
    {

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $data = array("id" => $id, "amount" => $amount);
            $this->forward($this->getController("add_to_cart"), $data);
            return $this->forward("MainSiteBundle:Security:login");
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/ordering/index", name="ordering")
     * @Template()
     */
   public function OrderingAction()
   {
       $entity = NULL;
       $securityContext = $this->container->get('security.context');
       if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')){
           $entity = $securityContext->getToken()->getUser();
       }

       $form = $this->createForm(new AcceptOrderType(), $entity);

       return array(
           'form' => $form->createView()
       );
   }
    /**
     * @Route("/order/accept", name="ordering_accept")
     * @Template()
     */
   public function OrderingAcceptAction(Request $request)
   {       
       $form = $this->createForm(new AcceptOrderType());
       $form->bind($request);

       if($form->isValid()){
           $this->forward("MainSiteBundle:Cart:accept");
           return $this->redirect($this->generateUrl('usercabinet'));
       } else {
           return $this->forward($this->getController("ordering"));
       }
   }
   
    /**
     * @Route("/novelty_product", name="novelty_product")
     * @Template()
     */
    public function NoveltyProductAction()
    {
        
    }

    /**
     * @Route("/auto/ajax_search_product.{_format}", name="ajax_search_product",
     * defaults={"_format" = "json"})
     */
    public function ajaxProductSearchAction(Request $request)
    {
        $value = $request->get('term');
        $em = $this->getDoctrine()->getEntityManager();
        $qb = $em->getRepository('ItcAdminBundle:Product\Product')
        ->createQueryBuilder('M')
        ->select('M')
        ->where("M.title LIKE :value or M.article LIKE :value")
        ->setParameter('value', "%" . $value . "%");

        $members = $qb->getQuery()->execute();

        $json = array();
        foreach ($members as $member) {

            $json[] = array(
                'label' => $member->getArticle() . "  " . $member->getTitle(),
                'value' => $member->getId(),
            );
        }

        $response = new Response();
        $response->setContent(json_encode($json));

        return $response;
    }

}
