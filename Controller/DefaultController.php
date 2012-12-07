<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Main\SiteBundle\Tools\ControllerHelper;
use Itc\AdminBundle\Tools\TranslitGenerator;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where("M.routing = 'index' ")
                        ->setParameter('locale', $locale);

        $entity = $queryBuilder->getQuery()->getOneOrNullResult();

        $images = $news = $blog = array();
        $children = $topPortfolio = array();
                
        $queryBuilder = $em->getRepository('ItcAdminBundle:Gallery\Image')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->innerJoin('M.gallery', 'G',
                                'WITH', "G.menu = :menu ")                                        
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('menu', $entity->getId() )
                        ->setParameter('locale', $locale);

        $images = $queryBuilder->getQuery()->execute();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where("M.parent = :parent ")
                        ->setParameter('parent', $entity->getId() )
                        ->setParameter('locale', $locale);

        $children = $queryBuilder->getQuery()->execute();                
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->innerJoin('M.parent', 'P') 
                        ->innerJoin('P.parent', 'PP',
                                'WITH', "PP.routing = 'portfolio' ")                                        
                        ->innerJoin('M.keywords', 'K',
                                'WITH', "K.keyword = 'showcase' ")                                        
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale);

        $topPortfolio = $queryBuilder->getQuery()->execute();
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->innerJoin('M.parent', 'P',
                                'WITH', "P.routing IN ( 'news', 'blog') ")                        
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.date_create', 'DESC')
                        ->setMaxResults(3)
                        ->setParameter('locale', $locale);
        
        $news = $queryBuilder->getQuery()->execute();
                
        return array( 
            'entity'    => $entity,
            'images'    => $images,
            'childrens' => $children,
            'topPortfolio' => $topPortfolio,
            'news'      => $news,
            'locale'    => $locale,
        );
    }
    /**
     * @Route("/portfolio" , name="portfolio")
     * @Template()
     */
    public function portfolioAction(){
        
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->where("M.routing = 'portfolio' ")                        
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale);

        $entity = $queryBuilder->getQuery()->getOneOrNullResult();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where("M.parent = :parent ")
                        ->setParameter('parent', $entity->getId() )
                        ->setParameter('locale', $locale);

        $children = $queryBuilder->getQuery()->execute();                
        
        $list = array();
        $sites_types = array();
        $entities_keywords = array();
        
        foreach($children as $child){
            array_push ($list, $child->getId());
            $sites_types[$child->getId()] = $child;
        }
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )                                       
                        ->innerJoin('M.galleries', 'G')                        
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where("M.parent_id IN (". implode(',', $list) .") ")
                        ->orderBy('M.kod', 'DESC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Keyword\Keyword')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->innerJoin('M.menus', 'G',
                                'WITH', "G.parent_id IN (". implode(',', $list) .") ")
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")        
                        ->orderBy('M.keyword', 'ASC')
                        ->setParameter('locale', $locale);
        
        $keywords = $queryBuilder->getQuery()->execute();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Keyword\Keyword')
                        ->createQueryBuilder('M')
                        ->select( 'M.id keyword_id, G.id menu_id' )
                        ->innerJoin('M.menus', 'G',
                                'WITH', "G.parent_id IN (". implode(',', $list) .") ");
        
        $menu_keywords = $queryBuilder->getQuery()->execute();
                
        $images = array();
        $images_list = array();
        $list = array();
        $galleries = array();
        $galleries_list =array();
        
        foreach($entities as $sites ){
            array_push($list, $sites->getId() );
            $list_keywords = array();
            foreach($menu_keywords as $val){
                if ( $val['menu_id'] == $sites->getId() )
                    array_push($list_keywords, $val['keyword_id']);
            }
            $entities_keywords[$sites->getId()] = implode(',', $list_keywords);
        }
                
        $queryBuilder = $em->getRepository('ItcAdminBundle:Gallery\Gallery')
                        ->createQueryBuilder('M')
                        ->select( 'M' )
                        ->where("M.menuId IN 
                                    (". implode(',', $list) .") ");

        $galleries_list = $queryBuilder->getQuery()->execute();
        
        $list = array();
        foreach($galleries_list as $gallery){
            $galleries[$gallery->getId()] = $gallery;
            array_push($list, $gallery->getId());
        }
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Gallery\Image')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where("M.gallery IN 
                                    (". implode(',', $list) .") ")
                        ->setParameter('locale', $locale);

        $images_list = $queryBuilder->getQuery()->execute();
        
        foreach($images_list as $val){
            $menu_id = $galleries[$val->getGallery()->getId()]->getMenuId();
            if (!isset($images[$menu_id]))
                $images[$menu_id] = array();
            array_push($images[$menu_id], $val);
        }
                
        return array( 
            'entities'  => $entities,
            'entity'    => $entity,
            'images'    => $images,
            'locale'    => $locale,
            'sites_types' => $sites_types,
            'keywords'  => $keywords,
            'entities_keywords' => $entities_keywords,
        );
    }
  /*      $queryBuilder = $em->getRepository('ItcAdminBundle:Keyword\Keyword')
                        ->createQueryBuilder('M')
                        ->select( 'M.id, M.keyword, T.value trans, G.id menu_id' )
                        ->innerJoin('M.menus', 'G',
                                'WITH', "G.parent_id IN (". implode(',', $child_list) .") ")
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")        
                        ->setParameter('locale', $locale);
        
        $keywords = $queryBuilder->getQuery()->execute();
        print_r($keywords);
        foreach($keywords as $val){
            echo $val['id']."=".$val['keyword']."=".$val['trans']."=".$val['menu_id']."<br />";
            //$
        }
*/        
    /**
     * @Route("/portfolio", name="portfolio")
     * @Template()
     */
 /*   public function portfolioAction(){

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
    */
    /**
     * @Route("/{translit}",  name="content")
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
        $child = array();

        foreach($entities as $v)
                array_push($child, $v->getId());
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where('M.parent IN  ( '.implode(",", $child).' )')
                        ->andWhere('M.visible = 1')
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $child_entities = $queryBuilder->getQuery()->execute();
            
        return array( 
            "entities"  => $entities,
            'locale'    => $locale,
            'languages' => $languages,
            'route'     => $request,
            'routing'   => $routing,
            'req'       => $req,
            'childs'    => $child_entities,
        );
    }
    /**
     * @Template()
     */
    public function footerAction(){
        
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where("M.routing = 'footer' ")
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();
        
        $entity = isset($entities[0]) ? $entities[0] : array();
        
        return array( 
            "entity"  => $entity,
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
/*
        if( $locale == LanguageHelper::getDefaultLocale() ){

            $table = $entity;

        } else {
            
            $table = $translation;
            $wheres[] = "M.locale = :locale";
            $parameters['locale'] = $locale;
        }

        $qb = $em->getRepository( $table )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' );

        if( $wheres !== NULL ){

            $qb->where( implode( ' AND ', $wheres ) );
            $qb->setParameters( $parameters );

        }

        return $qb->getQuery();
    }*/
     
    
}
