<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Main\SiteBundle\Tools\ControllerHelper;
use Main\SiteBundle\Form\SendMailType;
use Itc\AdminBundle\Tools\TranslitGenerator;
/**
 * Default controller.
 * @Route("/")
 */
class DefaultController extends ControllerHelper
{
    protected $menu = array( 
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
    }*/
    
    
    /**
     * @Route("/partners", name="partners")
     */
    public function partnersAction(){
       return $this->render('MainSiteBundle:Default:partners.html.twig', $this->getPartners("partners"));
    }
    /**
     * @Route("/partners/{translit}", name="partner")
     */
    public function partnerAction($translit){
       return $this->render('MainSiteBundle:Default:partner.html.twig', $this->getPartners("partners", $translit));
    }
    
    /**
     * @Route("/clients", name="clients")
     */
    public function clientsAction(){
        return $this->render('MainSiteBundle:Default:partners.html.twig', $this->getPartners("clients"));
    }
    /**
     * @Route("/clients/{translit}", name="client")
     */
    public function clientAction($translit){
        return $this->render('MainSiteBundle:Default:partner.html.twig', $this->getPartners("clients", $translit));
    }
    /**
     * @Route("/faq", name="faq")
     * @Template()
     */
    public function faqAction(){
        
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
//        
//        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
//                        ->createQueryBuilder('M')
//                        ->select( 'M' )
//                        ->where( "M.routing = :routing ")
//                        ->setParameter( "routing", "faq" );
//
//        $entity = $queryBuilder->getQuery()->getOneOrNullResult();
        $wheres[] = "M.routing = :routing ";
        $parameters["routing"] = "faq";
        $entity = $this->getEntities( $this->menu, $wheres, $parameters)
                       ->getOneOrNullResult();
        
        return array( 
            'entity' => $entity,
        );
    }
    /**
     * @Route("/{translit}", name="other")
     * @Template()
     */
    public function otherAction( $translit ){
        
        $query = $this->getEntityTranslit( $this->menu, $translit );
        $entity = $query->getOneOrNullResult();

        if( $entity === NULL ){
           
            $r = "index";
            $res = $this->redirect( $this->generateUrl( $r, array() ) );
            
        }elseif( ( $r = $entity->getRouting() ) !== NULL &&
                    in_array( $r, $this->getRoutes() ) ){

            $httpKernel = $this->container->get('http_kernel');
            $controller = $this->getController( $r );
            $res = $httpKernel->forward( $controller, array(
                "translit" => $translit,
                "entity"   => $entity,
            ));

        } else {
            
            $httpKernel = $this->container->get('http_kernel');
            $res = $httpKernel->forward("MainSiteBundle:Default:content", array(
                "translit" => $translit
            ));
           
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
        $entities = $this->getMenus( $parent_id );

        return array( 
            'entity'    => $entity,
            'keywords'  => $keywords,
            'menus'     => $entities,//$entity->getChildren(),
            'link'      => $link,
            'locale'    => LanguageHelper::getLocale(),
        );
    }
    /**
     * Для правого блока меню
     * 
     * @param type $parent_id
     * @return type 
     */
    protected function getMenus($parent_id){
        
        if(null === $parent_id)
        {
            $wheres[] = "M.parent IS NULL";
            $parameters['parent'] = "";
        }
        else
        {

            $wheres[] = "M.parent = :parent";
            $parameters['parent'] = $parent_id;
        }

        $entity = $this->getEntities( $this->menu, $wheres, $parameters )
                       ->execute();

        return $entity;
    }
    /**
     * @Route("/{translit}",  name="content")
     * @Template()
     */
    public function contentAction($translit){
        
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $deflocale = LanguageHelper::getDefaultLocale();
        
        $entity = $this->getEntityTranslit( array( 'ItcAdminBundle:Menu\Menu', "a" ), $translit)->getOneOrNullResult();
/*        
                        $enti = $this->getAllTranslitForEntity('ItcAdminBundle:Menu\Menu', 
                            $locale, 'en', 'translit', $entity->getTranslit())->getOneOrNullResult();
echo $enti->translate('en')->getTranslit();
*/
        $keywords = $em->getRepository('ItcAdminBundle:Keyword\Keyword')
                       ->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                        'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->getQuery()->execute();   
       
        $parent = $entity->getParent();
        if ($parent === null ){
            $parent = $entity->getId();  
        }
        
        $entities = $this->getMenus($parent);
        
        return array( 'entity' => $entity, 
                      'keywords' => $keywords,
                      'menus'    => $entities,
                      'locale'   => $locale,
                      'default'  => $deflocale,
                      'link'     => "/",
                    );
    }
    /**
     *@Route("/tag/{keyword}",  name="tag")
     *@Template()
     */
    public function tagAction($keyword){
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $deflocale=LanguageHelper::getDefaultLocale();
        if($locale == $deflocale){
        $entity=$em->getRepository('ItcAdminBundle:Keyword\Keyword')
                       ->createQueryBuilder('M')
                        ->select('M')
                        ->where("M.translit = :translit ")
                        ->setParameter('translit', $keyword)
        ->getQuery()->getOneOrNullResult();    
        }else{
        $entity=$em->getRepository('ItcAdminBundle:Keyword\Keyword')
                       ->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                        'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->where("T.property='translit'")
        ->getQuery()->getOneOrNullResult();    
        }
        if (!$entity) {
            throw $this->createNotFoundException('The keyword does not exist');
        }     
        $entities=$entity->getMenus();
        return array( 'entity'   => $entity,
                       'locale'   => $locale,
                       'entities' => $entities
                    );
    }
    
     /**
     *@Route("/contacts",  name="contacts")
     *@Template()
     */
    public function contactsAction(){
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $entity=$em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where("M.routing = 'contacts'")
                        ->setParameter('locale', $locale);

        $entity = $entity->getQuery()->getOneOrNullResult();
        if (!$entity) {
            throw $this->createNotFoundException('The contact page does not exist');
        }     
        $sendMailType = new SendMailType( $locale );
        $form = $this->createForm( $sendMailType );

        return array( 'entity'   => $entity,
                       'locale'   => $locale,
                       'form'     => $form->createView()
                    );
    }
    /**
     *
     *@Template("MainSiteBundle:Default:callback.html.twig")
     */
    public function callbackAction(){
       
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $deflocale=LanguageHelper::getDefaultLocale();
        $entity=$em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where("M.routing = 'contacts'")
                        ->setParameter('locale', $locale);

        $entity = $entity->getQuery()->getOneOrNullResult(); 
        $sendMailType = new SendMailType( LanguageHelper::getLocale() );
        $form = $this->createForm( $sendMailType );

        return array( 'entity'   => $entity,
                       'locale'   => $locale,
                       'form'     => $form->createView()
                    );
    }
        /**
     * @Route("/{translit}/sendMail", name="sendMail")
     * @Template("MainSiteBundle:Default:contacts.html.twig")
     */
    public function sendMailAction( $translit, Request $request ){
         
        $sendMailType = new SendMailType( LanguageHelper::getLocale() );
        
        $form = $this->createForm( $sendMailType );
        $form->bind( $request );
        $data = $form->getData();

        if( $form->isValid() ) {
            $body = $this->renderView( 'MainSiteBundle:Default:sendMail.txt.twig', 
                                array( 'text' => "Пользователь ".$data['fio']." email:".$data['email'].".Телефон:".$data['telefon']."Оставил сообщение:".$data['body'] ) );

            $message = \Swift_Message::newInstance()
                        ->setSubject( $data['email'] )
                        ->setFrom( $data['email'] )
                        ->setTo( 'neversmoke@i.ua' )
                        ->setBody( $body );

            $this->get( 'mailer' )->send( $message );

            $c = "index";
            $url = $this->generateUrl( $c, array( "translit" => $translit, "locale" => LanguageHelper::getLocale() ) );
            $res = $this->redirect( $url );

            return $res;


        }
        $entity = $this->getEntityTranslit( $this->menu, $translit )
                       ->getOneOrNullResult();

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'locale' => LanguageHelper::getLocale()
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
        $parents = array();

        foreach($entities as $v)
                array_push($parents, $v->getId());
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where('M.parent IN  ( '.implode(",", $parents).' )')
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
    /*    
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();

        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->where("M.routing = 'footer' ")
                        ->setParameter('locale', $locale);
     */
        $entity = $this->getEntityRouting($this->menu, 'footer')
                       ->getOneOrNullResult();
        
        //$entity = isset($entities[0]) ? $entities[0] : array();
        return array( 
            "entity" => $entity,
            "locale" => LanguageHelper::getLocale(),
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
        $em = $this->getDoctrine()->getManager();
        $em->clear();
        if($routing == "site_index")
        {
            $routing = "index";
        }else if( ! isset( $routing ) )
        {
             $routing = "content";
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
          /**
     * Вытягивет сущьность или сущность с прямыми потомками по критериям, 
     * возвращает также текущий язык.
     * @param type $routing - парметр поиска (обязательное условие)
     * 
     * @param type $translit - парметр поиска (не обязательное условие), 
     * если задано - вытягивает потомка, в противном случае сущность вместе с потомками, текущий routing
     * 
     * @return array 
    */ 
    protected function getPartners($routing, $translit=null)
    {
        $menu = array( 
                'ItcAdminBundle:Menu\Menu',
                'ItcAdminBundle:Menu\MenuTranslation'
                );
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
            
        $wheres[] = "M.routing = :routing";
        $routingf["routing"] = $routing;
        $entity = $this->getEntities( $menu, $wheres, $routingf )
                       ->getOneOrNullResult();
        if($translit==null)
        {
            return array( 
                'entity' => $entity,
                'partners'   => $entity->getChildren(),
                'locale' => $locale, 
                'routing' => $routing
                );
         }
         else
         {
             $wheres2[] = "M.parent_id = :parent_id";
             $parameters["parent_id"] = $entity->getId();
             $entity2 = $this->getEntityTranslit( $menu, $translit, $wheres2, $parameters )
                            ->getOneOrNullResult();
             return array( 
                 'entity' => $entity2,
                 'locale' => $locale,
                 'routing' => $routing
                    );
         }
    }

}
