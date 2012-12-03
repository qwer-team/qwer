<?php

namespace Itc\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\MenuSys\MenuSys;
use Itc\AdminBundle\Form\MenuSysType;
use Itc\AdminBundle\Form\MenuSysImageType;
use Itc\AdminBundle\Form\MenuSysNewType;
use Itc\AdminBundle\Tools\TranslitGenerator;
use Itc\AdminBundle\Tools\BreadCrumbsGeneration;
use Symfony\Component\Locale\Locale;
use Itc\AdminBundle\Tools\LanguageHelper;

/**
 * MenuSys controller.
 *
 * @Route("/menu_sys")
 */
class MenuSysController extends Controller
{
    private $langs = NULL ;
    /**
     * Lists all MenuSys entities.
     *
     * @Route("/{coulonpage}/{parent_id}.{_format}", name="menu_sys",
     * requirements={"parent_id" = "\d+", "coulonpage" = "\d+"}, 
     * defaults={ "parent_id" = null, "_format" = "html", "coulonpage" = "20"})
     * @Template()
     */
    public function indexAction( $parent_id = null, $coulonpage = 20 )
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  $this->getLocale();

        $repo = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys');
        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale);
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
            $this->get('request')->query->get( 'page', 1 )/*page number*/,
            $coulonpage/*limit per page*/
        );

        $updateForm     = array();
        $deleteForm     = array();
        $visibleForm    = array();
        foreach( $entities as $entity ){
            $id = $entity->getId() ;
            $visibleForm[$id] = $this->createVisibleForm( $entity )
                                     ->createView();
            /*$updateForm[$id] = 
                            $this->createForm(new MenuSysType(), $entity)
                            ->createView();
             * 
             */
            $deleteForm[$id] = $this->createDeleteForm($id)
                            ->createView();
        }

        $children    = $repo->createQueryBuilder('M')
                            ->select('M, C')
                            ->Join( 'Itc\AdminBundle\Entity\MenuSys\MenuSys', 'C', 'WITH',
                                    'C.parent = M.id');
        if(null === $parent_id)
            $children->where( 'M.parent IS NULL' );
        else
            $children->where( 'M.parent = :parent' )
                    ->setParameter('parent', $parent_id );
        //$children = $repo->find(array( 'parent'=>$parent_id) );
        $children = $children->getQuery()->execute();
        $childrenMap = array() ;
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $router = $this->get('router');

        $fields  = array( "parent_id" => NULL, "coulonpage" => $coulonpage ) ;

        BreadCrumbsGeneration::generate($parent_id, $fields, "menu_sys",  
                                $repo, $breadcrumbs, $router, $locale);
        
        foreach( $children as $child )
        {
            $id = $child->getParentId();
            isset( $childrenMap[$id]) ? 
                $childrenMap[$id]++ : $childrenMap[$id] = 1 ;
        }
        return array(
            'entities'     => $entities,
            'coulonpage'   => $coulonpage,
            'locale'       => $locale,
            'parent_id'    => $parent_id,
            'chmap'        => $childrenMap,
            'delete_form'  => $deleteForm,
            'update_form'  => $updateForm,
            'visible_form' => $visibleForm,
            
        );
    }

    /**
     * Finds and displays a MenuSys entity.
     *
     * @Route("/{id}/show", name="menu_sys_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuSys entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'locale' => $this->getLocale()
        );
    }

    /**
     * Displays a form to create a new MenuSys entity.
     *
     * @Route("/new/{parent_id}", name="menu_sys_new",
     * requirements={"parent_id" = "\d+"}, defaults={ "parent_id" = null})
     * @Template()
     */
    public function newAction($parent_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new MenuSys();
        $languages = $this->getLanguages();
        if( null !== $parent_id )
        {
            $parent = 
                $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')->find($parent_id);
            $entity->setParentId( $parent_id );
        }
        //$imageForm  = $this->createForm( new MenuSysImageType(), $entity);
        $entity->setTag( 'tag'.mt_rand(0, 100000 ) );
        $entity->setTitle('Title'.mt_rand(0, 100000 ));
        $entity->setRouting('Routing'.mt_rand(0, 100000 ));
        $entity->setVisible( true );
        $entity->setIcon('icon'.'parent_id');

        $form = $this->createForm(new MenuSysNewType($this->getLocale(), $languages), $entity);

        return array(
            'entity'     => $entity,
            //'image_form' => $imageForm->createView(),
            'form'       => $form->createView(),
            'languages'  => $languages,
            'locale'     => $this->getLocale(),
        );
    }

    /**
     * Creates a new MenuSys entity.
     *
     * @Route("/create", name="menu_sys_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:MenuSys:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity    = new MenuSys();
        $languages = $this->getLanguages();
        $locale    = $this->getLocale();

        $form = $this->createForm(new MenuSysNewType($locale, $languages), $entity);
        $form->bind( $request );

        $data = $form->getData();

        $parent_id = $data->getParentId();
        //echo "lalal".$data->getIconImage() ;

        $entity->setKod( $this->getKodeForMenuSys( $parent_id ) );
        //$imageForm  = $this->createForm( new MenuSysImageType(), $entity);

        if ($form->isValid()) {

            $em     = $this->getDoctrine()->getManager();
            $parent = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')
                         ->findOneById($parent_id);
            $entity->setParent( $parent );
            $em->persist( $entity );
            $em->flush();
            foreach( $this->getTranslits( $data, $entity ) as $lang => $translit ){
                $entity->translate( $lang )->setTranslit( $translit );
            }
            $em->flush();

            return $this->redirect(
                        $this->generateUrl('menu_sys_edit', 
                        array('id' => $entity->getId()))
            );
        }

        return array(
            'entity'     => $entity,
          //  'image_form' => $imageForm->createView(),
            'form'       => $form->createView(),
            'languages'  => $languages,
            'locale'     => $locale,
        );
    }

    /**
     * Displays a form to edit an existing MenuSys entity.
     *
     * @Route("/{id}/edit", name="menu_sys_edit")
     * @Template()
     */
    public function editAction( $id )
    {
        $languages  = $this->getLanguages();
        $locale     = $this->getLocale();

        $em = $this->getDoctrine()->getManager();

        $entity     = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')
                         ->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuSys entity.');
        }

        $editForm   = $this->createForm( new MenuSysType( $locale, $languages), 
                                         $entity)  ;
        $imageForm  = $this->createForm( new MenuSysImageType(), $entity);
        $deleteForm = $this->createDeleteForm( $id );

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'image_form'  => $imageForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'locale'      => $locale,
            'languages'   => $languages,
        );
    }
    /**
     * 
     * @return type 
     */
    protected function getLanguages(){
        return LanguageHelper::getLanguages();
    }
    /**
     * Edits an existing MenuSys entity.
     *
     * @Route("/{id}/update", name="menu_sys_update")
     * @Method("POST")
     * @Template("ItcAdminBundle:MenuSys:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $languages = $this->getLanguages();
        $locale = $this->getLocale();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')->find( $id );

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuSys entity.');
        }

        //$entity->setIcon( "" );
        $oldParent = $entity->getParent() ;
        $old_parent_id = is_null( $oldParent ) ? null : $oldParent->getId();

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm( new MenuSysType( $locale, $languages), 
                                         $entity );
        $editForm->bind($request);

        $data     = $editForm->getData();
        $parent   = $data->getParent();
        $icon     = $data->getIcon();

        $translits = $this->getTranslits( $data, $entity ) ;

        $parent_id = ( null === $parent ) ? null :  $parent->getId();

        if( $old_parent_id != $parent_id )
        {
            $newKod = $this->getKodeForMenuSys($parent_id) ;
            $entity->setKod( $newKod ) ;
        }

        if ( $editForm->isValid() ) {

            if( ! is_null( $entity->getParent() ) ) $em->persist( $entity ) ;

            $queryBuilder = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')
                               ->createQueryBuilder('M')
                               ->select("M")
                               ->where('M.id = :parent_id')
                               ->setParameter( 'parent_id', $parent_id );
            $parent = $queryBuilder->getQuery()->getOneOrNullResult();
            $entity->setParent($parent);

            foreach( $this->getTranslits( $data, $entity ) as $lang => $translit ){
                $entity->translate( $lang )->setTranslit( $translit );
            }

            $em->flush();

            return $this->redirect($this->generateUrl('menu_sys_edit', array('id' => $id)));
        }

        $imageForm  = $this->createForm( new MenuSysImageType(), $entity);
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'image_form'  => $imageForm->createView(),
            'languages'   => $languages,
            'locale'      => $locale,
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Edits an existing MenuSys entity.
     *
     * @Route("/{id}/update_image.{_format}", name="menu_sys_update_image",
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template()
     */
    public function updateImageAction(Request $request, $id)
    {
        //если файлик не сейвится надо раздать права
        // на запись в папку куда он должен сейвицца!!!!!!!
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')->find( $id );
        $entity->setIcon("");
        $imageForm = $this->createForm( new MenuSysImageType(), $entity);
        //$request->getIconImage();
        $imageForm->bind( $request );
//echo $imageForm['iconImage']->getData()->getClientOriginalName();
//echo $entity->setIcon()->getIcon();
        if ( $imageForm->isValid() ) {
            //echo "alallalala".$entity->getIcon()."kiki";
            $em->flush();
//            return $this->redirect($this->generateUrl('menu_sys_edit', array('id' => $id)));
        }

        //$languages = $this->getLanguages() ;
        // $editForm = $this->createForm( new MenuSysType($this->getLocale(), $languages), $entity);
/*
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'image_form'  => $imageForm->createView(),
            'languages'   => $languages,
            'locale'      => $this->getLocale(),
            'delete_form' => $this->createDeleteForm($id)->createView(),
        );
*/
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Deletes a MenuSys entity.
     *
     * @Route("/{id}/delete", name="menu_sys_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MenuSys entity.');
            }
            
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('menu_sys'));
    }
    /**
     * Edits an existing MenuSys entity.
     *
     * @Route("/{id}/delete_ajax", name="menu_sys_delete_ajax")
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template("ItcAdminBundle:MenuSys:deleteMenuSys.json.twig")
     */
    public function deleteMenuAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')->find($id);

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
    /**
     * Edits an existing MenuSys entity.
     *
     * @Route("/{id}/menu_sys_update_visible", name="menu_sys_update_visible",
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template()
     */
    public function updateVisibleAction( Request $request, $id ){

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository( 'ItcAdminBundle:MenuSys\MenuSys' )
                     ->find( $id );

        $isVisible = true;

        $imageForm = $this->createVisibleForm( $entity );
        $imageForm->bind( $request );

        if ( $imageForm->isValid() ) {
            $em->flush();
        }else{
            
            print_r($imageForm->getErrors());
        }

    }
    /**
     * @param type $visible
     */
    private function createVisibleForm( $entity ){

       return  $this->createFormBuilder( $entity )
                    ->add( 'visible', 'checkbox' )
                    ->getForm();
    }
    /**
     *
     * @return type 
     */
    private function getLocale()
    {
        return LanguageHelper::getLocale();
    }
    /**
     *
     * @param type $parent_id
     * @return type 
     */
    private function getKodeForMenuSys($parent_id)
    {
        //$parent_id = $parent_id ? $parent_id : " NULL" ;
        //$is = $parent_id ? " = " : " IS " ;
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')
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
        
        $kod = $queryBuilder->getQuery()->getSingleScalarResult() + 1;
        return $kod;
    }
    /**
     *
     * @param string $title
     * @param object $entity
     * @return array() [lang]=>translit ~ _id
     */
    private function getTranslits( $data, $entity ){
        $translits = array();        
        foreach( $this->getLanguages() as $lang ){
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
        $count = $this->checkTranslitRepeat($preTranslit);
        return array( $preTranslit, ( $count != 0 ) );
    }
    
    private function checkTranslitRepeat($preTranslit)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $this->getLocale();
        $defaultLocale = $this->container->parameters["locale"];
        if($locale == $defaultLocale)
        {
            $queryBuilder = $em->getRepository('ItcAdminBundle:MenuSys\MenuSys')
                                    ->createQueryBuilder('M')
                                    ->select('count(M.id)')
                                    ->where("M.translit = :translit")
                                    ->setParameter("translit", $preTranslit);
            $count = $queryBuilder->getQuery()->getSingleScalarResult();
        }
        else
        {
            $queryBuilder = $em->getRepository('ItcAdminBundle:MenuSys\MenuSysTranslation')
                                    ->createQueryBuilder('T')
                                    ->select('count(T.id)')
                                    ->where("T.locale = :locale 
                                            AND T.property = :property
                                            AND T.value = :translit")
                                    ->setParameter("locale", $locale)
                                    ->setParameter("property", "translit")
                                    ->setParameter("translit", $preTranslit);
            $count = $queryBuilder->getQuery()->getSingleScalarResult();
        }
        return $count;
    }
}
