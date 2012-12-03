<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;

/**
 * Default controller.
 * @Route("/")
 */
class DefaultController extends Controller
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
        
        $images = $news = array();
        
        $images = isset($galleries[0]) ? $galleries[0]->getImages() :array();
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Keyword\Keyword')
                        ->createQueryBuilder('M')
                        ->select( 'M' )
                        ->where("M.keyword = 'showcase' ");
        
        $portfolio = $queryBuilder->getQuery()->execute();
        
        $topPortfolio = $portfolio[0]->getMenus();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M' )
                        ->innerJoin('M.parent', 'P',
                                'WITH', "P.routing IN ( 'news', 'blog') ")                        
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.date_create', 'DESC')
                        ->setMaxResults(3)
                        ->setParameter('locale', $locale);
        
        $news = $queryBuilder->getQuery()->execute();
                
        return array( 
            'entities'  => $entities,
            'entity'    => $entity,
            'images'    => $images,
            'childrens' => $childrens,
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
                        ->select( 'M' )
                        ->innerJoin('M.parent', 'P',
                                'WITH', "P.routing IN ( 'portfolio') ")                        
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.kod', 'DESC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        $images = array();
        
        foreach($entities as $entity ){
            $galleries = $entity->getGalleries();
            if (isset($galleries[0])){
                $images[$galleries[0]->getMenuId()] = $galleries[0]->getImages();
            print_r($galleries[0]->getMenuId());
            }
        }
        
        return array( 
            'entities'  => $entities,
            'entity'    => $entity,
            'images'    => $images,
            'locale'    => $locale,
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
                        ->where( "M.id = 7");

        $entity = $queryBuilder->getQuery()->getOneOrNullResult();
        
        return array( 
            'entity' => $entity,
        );
    }
    /**
     * @Route("/{translit}",  name="content")
     * @Template()
     */
    public function contentAction($translit){
        
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getEntityTranslit( $this->menu, $translit )
                       ->getOneOrNullResult();
         if (!$entity) {
            throw $this->createNotFoundException('Невозможно найти страницу.');
                }
                
        $keywords = $em->getRepository('ItcAdminBundle:Keyword\Keyword')->findAll();
                $parent_id=$entity->getParent();
                if ($parent_id === null ){
                  $parent_id=$entity->getId();  
                }
                $entities=$this->getMenus($parent_id);
        return array( 'entity' => $entity, 
                      'keywords' =>$keywords,
                      'menus' =>$entities
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
        
        $child_entities = $child = array();

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
        if($routing == "site_index")
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
            
/************************ Вспомогательные методы ******************************/
    /**
     * Поиск по транслиту
     * @param string $entities - сущьность с транслитом описана в массиве
     * пример $this->menu;
     * @param string $translit - транслит для поиска
     * @return результат запроса
     */
    private function getEntityTranslit( $entities, $translit ){

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
     
     
    private function getEntities( $entities, array $wheres = NULL, array $parameters = NULL ){
        
        list( $entity, $translation ) = $entities;

        $em            = $this->getDoctrine()->getManager();
        $locale        = LanguageHelper::getLocale();

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
    }
    /**
     * Для правого блока меню
     * 
     * @param type $parent_id
     * @return type 
     */
    private function getMenus($parent_id){
        $em     = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $repo   = $em->getRepository('ItcAdminBundle:Menu\Menu');
        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->where('M.parent = :parent')
                        ->setParameter('parent', $parent_id);
        return $qb->getQuery()->execute();
    }
    
    
        private function getLocale()
    {
        $locale = $this->getRequest()->getLocale();
        return $locale;
    }
     /**
     * есть в ITC
     * @return type
     */
    private function getRoutes()
    {
        $router = $this->container->get( 'router' );
        
        $routes = array();

        foreach ( $router->getRouteCollection()->all() as $name => $route ){
            $routes[] = $name;
        }
        return $routes;
    }
    
}
