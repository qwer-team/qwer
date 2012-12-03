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
 * Default controller.
 * @Route("/")
 */
class DefaultController extends ControllerHelper
{
    private $menu = array( 
        'ItcAdminBundle:Menu\Menu',
        'ItcAdminBundle:Menu\MenuTranslation'
    );
    /**
     * @Route("/", defaults={ "_locale" = "ru"}, name="index")
     * @Template()
     */
    public function indexAction($locale = 'ru')
    {
        $em = $this->getDoctrine()->getManager();
        
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
        
        $images = $news = $blog = $topPortfolio = array();
        //$images = $galleries[0]->getImages();
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Keyword\Keyword')
                        ->createQueryBuilder('M')
                        ->select( 'M' )
                        ->where("M.keyword = 'showcase' ");
        
        $portfolio = $queryBuilder->getQuery()->execute();
        
       // $topPortfolio = $portfolio[0]->getMenus();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M' )
                        ->innerJoin('M.parent', 'P',
                                'WITH', "P.routing = 'news' ")                        
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.date_create', 'DESC')
                        ->setMaxResults(1)
                        ->setParameter('locale', $locale);
        
        $news = $queryBuilder->getQuery()->execute();
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M' )
                        ->innerJoin('M.parent', 'P',
                                'WITH', "P.routing = 'blog' ")                        
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.date_create', 'DESC')
                        ->setMaxResults(1)
                        ->setParameter('locale', $locale);
        
        $blog = $queryBuilder->getQuery()->execute();
        
        return array( 
            'entities'  => $entities,
            'entity'    => $entity,
            'images'    => $images,
            'childrens' => $childrens,
            'topPortfolio' => $topPortfolio,
            'news'      => array_merge($news, $blog),
        );
    }
    
    /**
     * @Route("/portfolio", name="portfolio")
     * @Template()
     */
    public function portfolioAction(){

        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where( "M.routing = :routing ")
                        ->setParameter( "routing", "portfolio" )
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entity = $queryBuilder->getQuery()->getOneOrNullResult();
        
        return array( 
            'entity' => $entity,
        );
    }
    
    /**
     * @Route("/faq", name="faq")
     * @Template()
     */
    public function faqAction(){
        
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M' )
                        ->where( "M.routing = :routing ")
                        ->setParameter( "routing", "faq" );

        $entity = $queryBuilder->getQuery()->getOneOrNullResult();
        
        return array( 
            'entity' => $entity,
        );
    }
    
    /**
     * @Route("/{translit}", name="other")
     * @Template()
     */
    public function otherAction( $translit ){
        echo "other";
        $entity = $this->getEntityTranslit( $this->menu, $translit )
                       ->getOneOrNullResult();

        if( $entity === NULL ){

            $r = "index";
            $res = $this->redirect( $this->generateUrl( $r, array() ) );
            
        } elseif( ( $r = $entity->getRouting() ) !== NULL &&
                    in_array( $r, $this->getRoutes() ) ){

            $httpKernel = $this->container->get('http_kernel');
            $res = $httpKernel->forward("MainSiteBundle:Default:{$r}", array(
                "translit" => $translit,
                "entity"   => $entity,
            ));

        } else {

            $res = array( 'entity' => $entity );
        }

        return $res;
    }
    /**
     *
     * @Template()
     */
    public function rightblockAction( $parent_id, $entity, $link = '/' ){
        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository('ItcAdminBundle:Keyword\Keyword')->findAll();
        $entities = $this->getMenus($parent_id);
        return array( 
            'entity'    => $entity,
            'keywords'  => $keywords,
            'menus'     => $entities,
            'link'      => $link,
            'locale'    => LanguageHelper::getLocale(),
        );
    }
    /**
     * @Route("/{translit}",  name="content")
     * @Template()
     */
    public function contentAction($translit){
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $deflocale=LanguageHelper::getDefaultLocale();
        $entity = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                        'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)  
                        ->where("M.translit = :translit ")
                        ->setParameter('translit', $translit);
           $entity = $entity->getQuery()->getOneOrNullResult();
                if( $locale == $deflocale ){
                     $keywords = $em->getRepository('ItcAdminBundle:Keyword\Keyword')->findAll();
                }
                else{
        $keywords = $em->getRepository('ItcAdminBundle:Keyword\Keyword')
                        ->createQueryBuilder('M')
                        ->select('M, T.value')
                        ->leftJoin('M.translations', 'T',
                        'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale);
      
                       $keywords=$keywords ->getQuery()->execute();
                }
                $parent_id=$entity->getParent();
                if ($parent_id === null ){
                  $parent_id=$entity->getId();  
                }
                $entities=$this->getMenus($parent_id);
        return array( 'entity' => $entity, 
                      'keywords' =>$keywords,
                      'menus' =>$entities,
                      'locale' => $locale,
                      'default' => $deflocale
                    );
    }
    
    
    /**     
     * Lists all Menu entities.
     * @Template()
     */
    public function menuAction($routing, $req){

        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $languages  = LanguageHelper::getLanguages();
        $request = "";
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where('M.parent IS NULL')
                        ->andWhere('M.visible = 1')
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();
        
        $child_entities = array();
/*
        foreach($arr as $v)
            if (is_null($v->getParentId()) )
                $entities[] = $v;
  */              
        return array( 
            "entities"  => $entities,
            "locale"    => $locale,
            'locale'    => $locale,
            'languages' => $languages,
            'route'     => $request,
            'routing'   => $routing,
            'req'       => $req
        );
    }
    
    private $translitCollection = 
            array(
                    "translit"      => "menu",
                    "kwd_translit"  => "kwd"
                );
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Template()
     */
    public function languagesAction($req, $routing){
        if($routing == "site_index" || ! isset( $routing ) )
        {
            $routing = "index";
        }
        
        $pattern = $this->container->get('router')
                  ->getRouteCollection()
                  ->get($routing)
                  ->getPattern();

        $out = array();
        preg_match_all("/{([^}]*)}/", $pattern, $out, PREG_PATTERN_ORDER);
        
        $params = array();
        if(isset($out[1]) && count($out[1]) > 0)
        {
            $params = $out[1];
        }
        
        $urls = array();
        $locale =  LanguageHelper::getLocale();
        $languages  = LanguageHelper::getLanguages();
        foreach($languages as $lang)
        {
            $wasLocale = false;
            if($lang == $locale)
            {
                continue;
            }
            $values = array();
            foreach($params as $param)
            {
                $value = $req->get($param);
                if($param == "translit")
                {
                    $entity = $this->getEntityTranslit( $this->menu, $value )
                       ->getOneOrNullResult();
                    $value = $entity->translate($lang)->getTranslit();
                }else 
                    if($param == "_locale")
                {
                    $value = $lang;
                    $wasLocale = true;
                }    
                else 
                {
                    if($value == "")
                    {
                        $value = null;
                    }                    
                }
                $values[$param] = $value;
            }
            
            if(!$wasLocale)
            {
                $values["_locale"] = $lang;
            }
            $url = $this->generateUrl($routing, $values, true);
            $urls[$lang] = $url;
        }
        return array( 
            "locale"    => $locale,
            "urls"      => $urls
            );
        
    }

    
}
