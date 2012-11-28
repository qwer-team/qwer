<?php

namespace Itc\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\Menu\Menu;
use Itc\AdminBundle\Form\MenuType;
use Itc\AdminBundle\Form\MenuImageType;
use Itc\AdminBundle\Form\SearchMenuType;
use Itc\AdminBundle\Tools\TranslitGenerator;
use Itc\AdminBundle\Tools\BreadCrumbsGeneration;
use Symfony\Component\Locale\Locale;
use Itc\AdminBundle\Tools\LanguageHelper;

/**
 * Menu controller.
 *
 * @Route("/menu")
 */
class MenuController extends Controller {    
    /**
     * Lists all Menu entities.
     *
     * @Route("/{coulonpage}/{page}/{parent_id}", name="menu",
     * requirements={"parent_id" = "\d+", "coulonpage" = "\d+","page" = "\d+"}, 
     * defaults={ "parent_id" = null, "coulonpage" = "100", "page"=1})
     * @Template("ItcAdminBundle:Menu:index.html.twig")
     */
    public function indexAction($parent_id = null, $coulonpage = 100, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();

        $repo = $em->getRepository('ItcAdminBundle:Menu\Menu');
        
        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->orderBy('M.kod', 'ASC');    
        if(null === $parent_id)
        {
            $qb->where('M.parent IS NULL');
        }
        else
        {
            $qb->where('M.parent = :parent')
               ->setParameter('parent', $parent_id);
        }
        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $qb,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $coulonpage/*limit per page*/
        );

        $deleteForm = array();   
        $visibleForm = array();
        
        foreach ($entities as $entity){
            
            $deleteForm[$entity->getId()] = $this->createDeleteForm($entity->getId())
                            ->createView();
            $visibleForm[$entity->getId()] = $this->createVisibleForm($entity)
                            ->createView();
           $changeKodForm[$entity->getId()] = 
                    $this->createChangeKodForm($entity->getKod(), $coulonpage, $page)
                            ->createView();
        }
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        
        $router = $this->get('router');
        $fields  = array( "parent_id" => NULL, "coulonpage" => $coulonpage ) ;
        
        BreadCrumbsGeneration::generate($parent_id, $fields, "menu",  
                                $repo, $breadcrumbs, $router, $locale);

        $search_form = $this->createForm(new SearchMenuType($locale));
        
        return array(
            'entities'  => $entities,
            'locale'    => $locale,
            'parent_id' => $parent_id,
            'coulonpage' => $coulonpage,
            'search_form' => $search_form->createView(),
            'delete_form' => $deleteForm,
            'visible_form' => $visibleForm,
            'change_kod_form' => $changeKodForm,
        );
    }
    /**
     * @Route("/{coulonpage}/search", name="menu_search",
     * requirements={"coulonpage" = "\d+"}, 
     * defaults={"coulonpage" = "100"})
     * @Template("ItcAdminBundle:Menu:index.html.twig")
     */
    public function searchAction(Request $request, $coulonpage = 100)
    {
        $locale =  $this->getLocale();
        $search_form = $this->createForm(new SearchMenuType($locale));
        $search_form->bind($request);
        $data = $search_form->getData();
        
        $em = $this->getDoctrine()->getManager();        

        $repo = $em->getRepository('ItcAdminBundle:Menu\Menu');
        
        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale);                        
        
        if(null !== $data["text"])
        {
            $qb->orWhere('M.id = :id')
                ->setParameter('id', $data["text"])
                ->orWhere("M.tag LIKE :tag")
                ->setParameter('tag', "%".$data["text"]."%")
                ->orWhere('M.title LIKE :title')
                ->setParameter('title', "%".$data["text"]."%")
                ->orWhere('M.translit LIKE :translit')
                ->setParameter('translit', "%".$data["text"]."%");                    
        }
    /*    if(null !== $data["id"])
        {
            $qb->andWhere('M.id = :id')
               ->setParameter('id', $data["id"]);
        }
        if(null !== $data["parent_id"])
        {
            $qb->andWhere('M.parent = :parent')
               ->setParameter('parent', $data["parent_id"]);
        }

        if(null !== $data["title"])
        {
            $qb->andWhere("M.title LIKE :title ")
               ->setParameter('title', "%".$data["title"]."%");
        }

        if(null !== $data["tag"])
        {
            $qb->andWhere("M.tag LIKE :tag ")
               ->setParameter('tag', "%".$data["tag"]."%");
        }
        if(null !== $data["translit"])
        {
            $qb->andWhere("M.tag LIKE :translit ")
               ->setParameter('translit', "%".$data["translit"]."%");
        }
        if(null !== $data["from"])
        {
            $qb->andWhere('M.date_create >= :from')
               ->setParameter('from', $data["from"]);
        }

        if(null !== $data["to"])
        {
            $qb->andWhere("M.date_create <= :to ")
               ->setParameter('to', $data["to"]);
        }*/
        $paginator = $this->get('knp_paginator');
        
        $entities = $paginator->paginate(
            $qb,
            $this->get('request')->query->get('page', 1),
            100
        );
        foreach ($entities as $entity){
            
            $deleteForm[$entity->getId()] = $this->createDeleteForm($entity->getId())
                            ->createView();
            $visibleForm[$entity->getId()] = $this->createVisibleForm($entity)
                            ->createView();
           $changeKodForm[$entity->getId()] = 
                    $this->createChangeKodForm($entity->getKod(), $coulonpage, 1)
                            ->createView();
        }
        
        return array(
            'entities'  => $entities,
            'locale'    => $locale,
            'parent_id' => null,
            'chmap'     => array(),
            'search_form' => $search_form->createView(),
            'delete_form' => $deleteForm,
            'visible_form' => $visibleForm,
            'coulonpage' => $coulonpage,
            'change_kod_form' => $changeKodForm,
        );
    }
    /**
     * Finds and displays a Menu entity.
     *
     * @Route("/{id}/show", name="menu_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Menu\Menu')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'locale' => $this->getLocale()
        );
    }
    /**
     * Creates a new Menu entity.
     *
     * @Route("/create", name="menu_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:Menu:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Menu();
        $languages  = LanguageHelper::getLanguages();
        
        $form = $this->createForm(new MenuType($this->getLocale()), $entity,
                    array("attr" => array("new" => true)) );
        $form->bind($request);
        $data = $form->getData();

        $parent_id = $data->getParentId();
        
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $parent = $em->getRepository('ItcAdminBundle:Menu\Menu')
                    ->findOneById($parent_id);
            $entity->setKod( $this->getKodForMenu( $parent_id ) );
            $entity->setParent($parent);
            $em->persist( $entity );
            $em->flush();
            foreach( $this->getTranslits( $data, $entity ) as $lang => $translit ){
                $entity->translate( $lang )->setTranslit( $translit );
            }
            $em->flush();
            
            return $this->redirect(
                        $this->generateUrl('menu_edit', 
                        array('id' => $entity->getId()))
            );
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'languages' => $languages,
        );
    }

    /**
     * Displays a form to create a new Menu entity.
     *
     * @Route("/new/{parent_id}", name="menu_new",
     * requirements={"parent_id" = "\d+"}, defaults={ "parent_id" = null})
     * @Template()
     */
    public function newAction($parent_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Menu();
        $languages  = LanguageHelper::getLanguages();

        if( null !== $parent_id )
        {
            $parent = 
                $em->getRepository('ItcAdminBundle:Menu\Menu')->find($parent_id);
            $entity->setParentId( $parent_id );            
        }
        $entity->setTag( 'tag'.mt_rand(0, 100000 ) );
        $entity->setTitle('Title'.mt_rand(0, 100000 ));
        $entity->setVisible(true);
        $entity->setIcon('icon'.'parent_id');

        $form = $this->createForm(new MenuType($this->getLocale()), $entity, 
                            array("attr" => array("new" => true)));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'languages' => $languages,
            'locale'     => $this->getLocale(),            
        );
    }
    

    /**
     * Displays a form to edit an existing Menu entity.
     *
     * @Route("/{id}/edit", name="menu_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $languages  = LanguageHelper::getLanguages();


        $entity = $em->getRepository('ItcAdminBundle:Menu\Menu')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $editForm   = $this->createForm(new MenuType($this->getLocale()), $entity,
                            array("attr" => array("new" => false)));
        $imageForm  = $this->createForm( new MenuImageType(), $entity);
        $deleteForm = $this->createDeleteForm( $id );
        $eview = $editForm->createView();

        return array(
            'entity'      => $entity,
            'edit_form'   => $eview,
            'image_form'  => $imageForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'locale' => $this->getLocale(),
            'languages' => $languages,
        );
    }

    /**
     * Edits an existing Menu entity.
     *
     * @Route("/{id}/update", name="menu_update")
     * @Method("POST")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $languages  = LanguageHelper::getLanguages();
        $entity = $em->getRepository('ItcAdminBundle:Menu\Menu')->find($id);
        $old_kod=$entity->getKod();    
 
        $old_parent_id = is_null($entity->getParent()) ? null 
                                             : $entity->getParent()->getId();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MenuType($this->getLocale()), $entity, 
                                array("attr" => array("new" => false)));
        $editForm->bind($request);
        $data = $editForm->getData();
        $parent = $data->getParent();                   
        $kod = $data->getKod();
        
        $parent_id = ( null === $parent ) ? null :  $parent->getId();
        if($old_kod!=$kod){
                        $this->getGenerateKod($parent_id, $kod);
                          }
        if ( $editForm->isValid() ) {

            if( ! is_null( $entity->getParent() ) ) $em->persist( $entity ) ;

            $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                               ->createQueryBuilder('M')
                               ->select("M")
                               ->where('M.id = :parent_id')
                               ->setParameter( 'parent_id', $parent_id );
            $parent = $queryBuilder->getQuery()->getOneOrNullResult();
            $entity->setParent($parent); 
            
            foreach( $languages as $lang ){                
                $preTranslit = $entity->translate( $lang )->getTranslit( );
                $count = $this->wasYet( $preTranslit, $entity->getId());  
                if ($count > 0){
                    $translit = $preTranslit."_".$entity->getId();
                    $entity->translate( $lang )->setTranslit( $translit );
                }                
            }
            $em->flush();
            return $this->redirect($this->generateUrl('menu_edit', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('menu_edit', array('id' => $id)));
    }

    /**
     * Deletes a Menu entity.
     *
     * @Route("/{id}/menu_delete", name="menu_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Menu\Menu')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Menu entity.');
            }
            
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('menu'));
    }
    /**
     * Edits an existing Menu entity.
     *
     * @Route("/{id}/menu_delete_ajax", name="menu_delete_ajax")
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template("ItcAdminBundle:Menu:deleteMenu.json.twig")
     */
    public function deleteMenuAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Menu\Menu')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Menu entity.');
            }
            
            $em->remove($entity);
            $em->flush();
        }else{            
            print_r($form->getErrors());
        }
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Edits an existing Menu entity.
     *
     * @Route("/{id}/menu_update_visible", name="menu_update_visible")
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template("ItcAdminBundle:Menu:updateVisible.json.twig")
     */
    public function updateVisibleAction( Request $request, $id ){

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ItcAdminBundle:Menu\Menu')
                ->find($id);  
        $imageForm = $this->createVisibleForm( $entity );
        $imageForm->bind( $request );
        
        if ( $imageForm->isValid() ) {
            $em->flush();
        }else{            
            print_r($imageForm->getErrors());
        }
        return array(
            'entity' => $entity,
        );
        
    }    
    /**
     * Edits an existing Menu entity.
     *
     * @Route("/{id}/menu_update_image.{_format}", name="menu_update_image",
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template()
     */
    public function updateImageAction(Request $request, $id)
    {
        //если файлик не сейвится надо раздать права
        // на запись в папку куда он должен сейвицца!!!!!!!
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ItcAdminBundle:Menu\Menu')->find( $id );
        $entity->setIcon("");
        $imageForm = $this->createForm( new MenuImageType(), $entity);
        $imageForm->bind( $request );
        $imageForm['iconImage']->getData()->getClientOriginalName();
        if ( $imageForm->isValid() ) {
            $em->flush();
        }
        return array(
            'entity' => $entity,
        );
    }    
    /**
     * 
     * @return type
     */
    private function getLocale()
    {
        $locale = $this->getRequest()->getLocale();
        return $locale;
    }
    /**
     *
     * @param string $title
     * @param object $entity
     * @return array() [lang]=>translit ~ _id
     */
    private function getTranslits( $data, $entity ){
        $languages  = LanguageHelper::getLanguages();
        $translits = array();        
        foreach( $languages as $lang ){
            $title = $data->translate( $lang )->getTitle();
            list($translit, $translitDuplicate ) = 
                                $this->getTranslit($title, $entity->getId());
            if( $translitDuplicate  )
            {
                $translit .= "_" .$entity->getId();
            }
            $translits[$lang] = $translit;
        }
        
        return $translits;
    }    
    private function getTranslit($title, $id)
    {        
        $preTranslit = TranslitGenerator::getTranslit($title);
        $count = $this->wasYet($preTranslit);
        return array($preTranslit, ($count != 0) );
    }
    private function getKodForMenu($parent_id)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                                        ->createQueryBuilder('M')
                                        ->select('max(M.kod) kod');
        if(null === $parent_id)
        {
            $queryBuilder->where("M.parent is NULL");
        }
        else
        {
            $queryBuilder->where("M.parent = :parent")
                         ->setParameter('parent', $parent_id);
        }        
        $res = $queryBuilder->getQuery()->execute();
        return $res[0]["kod"]+1;
    }
    private function getGenerateKod($parent_id, $kod){
        $em = $this->getDoctrine()->getManager();
           $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                                    ->createQueryBuilder('M')
                                    ->select('M.id , M.kod');
                           if(empty($parent_id))
                            {
                            $queryBuilder->where("M.kod >= :kod and M.parent_id is NULL")->setParameter("kod", $kod);
                            }
                            else
                            {
                                $queryBuilder->where("M.kod >= :kod and M.parent_id = :parent")
                                            ->setParameter("kod", $kod)->setParameter('parent', $parent_id);
                            }  
           $kods = $queryBuilder->getQuery()->getResult();
           foreach ($kods as $v) {
               /* */$queryBuilder = $em->createQueryBuilder()
                                    ->update('ItcAdminBundle:Menu\Menu', 'M')
                                    ->set('M.kod', ++$kod)
                                    ->where("M.id = :id")
                                    ->setParameter("id", $v['id']);
                $queryBuilder->getQuery()->execute();
           }
           
        
    }
    private function wasYet($preTranslit, $entity_id = NULL)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $this->getLocale();
        $defaultLocale = $this->container->parameters["locale"];
        if($locale == $defaultLocale)
        {
            $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                                    ->createQueryBuilder('M')
                                    ->select('count(M.id)')
                                    ->where("M.translit = :translit")
                                    ->setParameter("translit", $preTranslit);
            if(!is_null($entity_id)) 
               $queryBuilder->andWhere('M.id != :id')
                            ->setParameter('id', $entity_id);
            $count = $queryBuilder->getQuery()->getSingleScalarResult();
        }
        else
        {
            $queryBuilder = $em->getRepository('ItcAdminBundle:MenuTranslation')
                                    ->createQueryBuilder('T')
                                    ->select('count(T.id)')
                                    ->where("T.locale = :locale 
                                            AND T.property = :property
                                            AND T.value = :translit")
                                    ->setParameter("locale", $locale)
                                    ->setParameter("property", "translit")
                                    ->setParameter("translit", $preTranslit);
            if(!is_null($entity_id)) 
               $queryBuilder->andWhere('T.translatable_id != :id')
                            ->setParameter('id', $entity_id);
            $count = $queryBuilder->getQuery()->getSingleScalarResult();
        }
        return $count;
    }
    /**
     *
     * @param type $id
     * @return type 
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    private function createChangeKodForm( $kod , $coulonpage= null, $page = null ){

       return  $this->createFormBuilder(
                    array('kod' => $kod, 'coulonpage' => $coulonpage, 'page' => $page))
                    ->add( 'kod', 'hidden' )
                    ->add( 'coulonpage', 'hidden' )
                    ->add( 'page', 'hidden' )
                    ->getForm();
    }
    
    /**
     * @param type $entity
     */
    private function createVisibleForm( $entity ){

       return  $this->createFormBuilder( $entity )
                    ->add( 'visible', 'checkbox' )
                    ->getForm();
    }
    /**
    * @Route("/search", name="menu_search")
    * @Template("ItcAdminBundle:Menu:search.html.twig")
    */
    /*
    public function searchAction(Request $request)
    {
        $finder = $this->get('foq_elastica.index.title.Menu');
        $searchTerm = $request->query->get('search');
        $sites = $finder->find($searchTerm);
        return array('sites' => $sites);
    }
    */
    /**
     * Edits an existing Menu entity.
     *
     * @Route("/{id}/change_kod", name="menu_change_kod",
     * requirements={"id" = "\d+"}) 
     * @Method("POST")
     * @Template("ItcAdminBundle:Menu:index_table.html.twig")
     */
    public function updateKodAction(Request $request, $id)
    {
        $form = $this->createChangeKodForm($id);
        $form->bind($request);
        $data = $form->getData();
        $newKod = $data['kod'];
        $coulonpage = $data['coulonpage'];
        $page = $data['page'];
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $qb = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select('M.kod,M.parent_id')
                        ->where('M.id = :id')
                        ->setParameter('id', $id);
            $entity = $qb->getQuery()->getResult();
            
            $oldKod = $entity[0]['kod'];
            $parent_id = $entity[0]['parent_id'];
            
            $qb = $em->createQueryBuilder('M')
                        ->update('ItcAdminBundle:Menu\Menu', 'M')
                        ->set('M.kod', $oldKod)
                        ->where('M.kod = :kod')
                        ->setParameter('kod', $newKod);
            if ( !is_null($parent_id) )
            {                         
                        $qb->andWhere('M.parent = :parent')
                        ->setParameter('parent', $parent_id);
            }
            else
            {
                        $qb->andWhere('M.parent IS NULL');
            }                
            $qb->getQuery()->execute();

            $qb = $em->createQueryBuilder('M')
                        ->update('ItcAdminBundle:Menu\Menu', 'M')
                        ->set('M.kod', $newKod)
                        ->where('M.id = :id')
                        ->setParameter('id', $id);
            if ( !is_null($parent_id) )
            {                         
                        $qb->andWhere('M.parent = :parent')
                        ->setParameter('parent', $parent_id);
            }
            else
            {
                        $qb->andWhere('M.parent IS NULL');
            }
            $qb->getQuery()->execute();  
            return $this->indexAction($parent_id, $coulonpage, $page);
        }else{            
            return false;
        }
     }
    
}