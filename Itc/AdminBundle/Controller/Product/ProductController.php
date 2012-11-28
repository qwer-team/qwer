<?php

namespace Itc\AdminBundle\Controller\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\Product\Product;
use Itc\AdminBundle\Form\Product\ProductType;
use Itc\AdminBundle\Form\Product\ProductImageType;
use Itc\AdminBundle\Tools\LanguageHelper;
use Itc\AdminBundle\Tools\BreadCrumbsGeneration;
use Itc\AdminBundle\Tools\TranslitGenerator;

/**
 * Product\Product controller.
 *
 * @Route("/product")
 */
class ProductController extends Controller
{
     /**
     * @Route("/ajax_member.{_format}", name="ajax_member",
     * defaults={"_format" = "json"})
     * @Method("GET")
     */
    public function ajaxMemberAction(Request $request)
    {
        $value = $request->get('term');

        $em = $this->getDoctrine()->getEntityManager();
        $members = $qb = $em->getRepository( 'ItcAdminBundle:Product\Product' )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' );
        $qb->where( "M.title LIKE :value" );
        $qb->setParameter( 'value', "%".$value."%" );

        $members = $qb->getQuery()->execute();

        $json = array();
        foreach ($members as $member) {
            $json[] = array(
                'label' => $member->getTitle(),
                'value' => $member->getId()
            );
        }

        $response = new Response();
        $response->setContent( json_encode( $json ) );

        return $response;
    }
    /**
     * Lists all Product\Product entities.
     *
     * @Route("/{coulonpage}/{page}/{product_group_id}.{_format}", name="product",
     * requirements={"product_group_id" = "\d+", "coulonpage" = "\d+", "page" = "\d+"}, 
     * defaults={ "product_group_id" = null, "_format" = "html", "coulonpage" = "100", "page"=1})
     * @Template("ItcAdminBundle:Product\Product:index.html.twig")
     */
    public function indexAction($product_group_id, $coulonpage, $page, $ajax_sign = false)
    {
        
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $deleteProductForm = $changeKodForm = array();
        
        $repo = $em->getRepository('ItcAdminBundle:Product\Product');
        
        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->orderBy('M.kod', 'ASC');       
        if(null === $product_group_id)
        {
            $qb->where('M.productGroup IS NULL');
        }
        else
        {
            $qb->where('M.productGroup = :productGroup')
               ->setParameter('productGroup', $product_group_id);
        }
        $paginator = $this->get('knp_paginator');
//        $paginator->setUserRoute("product");
        
        $entities = $paginator->paginate(
            $qb, $page,
        //    $this->get('request')->query->get('page', 1)/*page number*/,
            $coulonpage/*limit per page*/
        );      

      //  $breadcrumbs = $this->get("white_october_breadcrumbs");
        
  //      $router = $this->get('router');
        
//        $fields  = array( "parent_id" => NULL, "coulonpage" => $coulonpage ) ;
        
    /*    BreadCrumbsGeneration::generate($product_group_id, $fields, "product",  
                                $repo, $breadcrumbs, $router, $locale);
      */  
        foreach ($entities as $entity){
           $deleteProductForm[$entity->getId()] = $this->createDeleteForm($entity->getId())
                            ->createView();
           $changeKodForm[$entity->getId()] = 
                    $this->createChangeKodForm($entity->getKod(), $coulonpage, $page)
                            ->createView();
         /*  $changeKodDownForm[$entity->getId()] = $this->createChangeKodDownForm($entity->getKod())
                            ->createView();
        */}
        return array(
            'entities' => $entities,
            'locale'    => $locale,
            'coulonpage' => $coulonpage,
            'route' => 'product',
            'parent_id' => $product_group_id,
            'delete_product_form' => $deleteProductForm,
            'change_kod_form' => $changeKodForm,
            'ajax_sign' => $ajax_sign,
//            'change_kod_up_form' => $changeKodUpForm,
          //  'change_kod_down_form' => $changeKodDownForm,
        );
    }
    /**
     * Lists all Product\Product entities.
     *
     * @Route("/{coulonpage}/{page}/{product_group_id}", name="product_table",
     * requirements={"product_group_id" = "\d+", "coulonpage" = "\d+", "page" = "\d+"}, 
     * defaults={ "product_group_id" = null, "_format" = "html", "coulonpage" = "100", "page"=1})
     * @Template("ItcAdminBundle:Product\Product:index_table.html.twig")
     */
    public function indexTableAction($product_group_id, $coulonpage, $page)
    {
        
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $deleteProductForm = $changeKodForm = array();
        
        $repo = $em->getRepository('ItcAdminBundle:Product\Product');

        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->orderBy('M.kod', 'ASC');       
        if(null === $product_group_id)
        {
            $qb->where('M.productGroup IS NULL');
        }
        else
        {
            $qb->where('M.productGroup = :productGroup')
               ->setParameter('productGroup', $product_group_id);
        }
        $paginator = $this->get('knp_paginator');
        
        $entities = $paginator->paginate(
            $qb, $page, $coulonpage );      

        foreach ($entities as $entity){
           $deleteProductForm[$entity->getId()] = $this->createDeleteForm($entity->getId())
                            ->createView();
            $changeKodForm[$entity->getId()] = 
                    $this->createChangeKodForm($entity->getKod(), $coulonpage, $page)
                            ->createView();
        }
        return array(
            'entities' => $entities,
            'locale'    => $locale,
            'coulonpage' => $coulonpage,
            'parent_id' => $product_group_id,
            'delete_product_form' => $deleteProductForm,
            'change_kod_form' => $changeKodForm,
        );
    }

    /**
     * Finds and displays a Product\Product entity.
     *
     * @Route("/{id}/show", name="product_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Product\Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Product\Product entity.
     *
     * @Route("/new/{parent_id}", name="product_new",
     * requirements={"parent_id" = "\d+"}, defaults={ "parent_id" = null})
     * @Template()
     */
    public function newAction($parent_id)
    {
        $entity = new Product();
        $languages = $this->getLanguages();
        $locale =  LanguageHelper::getLocale();
        $em = $this->getDoctrine()->getManager();
        
        $entity->setKod($this->getKodeForProduct($parent_id));
        
        $form = $this->createForm( new ProductType( $em, NULL, false ), $entity, 
                array("attr" => array("new" => true)));

        if( null !== $parent_id )
        {
            $parent = 
                $em->getRepository('ItcAdminBundle:Product\ProductGroup')->find($parent_id);
            $entity->setProductGroup($parent);
        }
        

        return array(
            'entity'    => $entity,
            'form'      => $form->createView(),
            'languages' => $languages,
            'locale'    => $locale,            
            'route'     => 'product',
        );
    }

    /**
     * Creates a new Product\Product entity.
     *
     * @Route("/create", name="product_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\Product:new.html.twig")
     */
    public function createAction(Request $request)
    {

        $languages = $this->getLanguages();
        $locale =  LanguageHelper::getLocale();

        $em = $this->getDoctrine()->getManager();
        $entity  = new Product();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ProductType( $em, NULL, true ), $entity, 
                 array("attr" => array("new" => true)));        
        $form->bind($request);
        $data = $form->getData();
        
        if( $form->isValid()) {
            
            $em->persist($entity);
            $em->flush();

            foreach( $this->getTranslits( $data, $entity ) as $lang => $translit ){
                $entity->translate( $lang )->setTranslit( $translit );
            }
            $em->flush();
            $groupId = null;
            if(null !== $entity->getProductGroup())
            {
                $groupId = $entity->getProductGroup()->getId();
            }
            return $this->redirect($this->generateUrl('product_group', array('parent_id' => $groupId)));
        }

        return array(
            'entity'    => $entity,
            'form'      => $form->createView(),
            'languages' => $languages,
            'locale'    => $locale,            
            'route'     => 'product',
        );
    }

    /**
     * Displays a form to edit an existing Product\Product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Template()
     */
    public function editAction($id)
    {
        //$em = $this->getDoctrine()->getManager();

        $languages = $this->getLanguages();
        $locale    =  LanguageHelper::getLocale();

        //$entity = $em->getRepository('ItcAdminBundle:Product\Product')->find($id);

        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository( 'ItcAdminBundle:Product\Product' )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' )
                     ->where('M.id = :id')
                     ->setParameter('id', $id)
                     ->getQuery()
                     ->getOneOrNullResult();
        /*
        $entity = $em->getRepository('ItcAdminBundle:Product\Product')
                    ->createQueryBuilder('M')
                    ->select( "M" )
                    ->where('M.id = :id')
                    ->setParameter('id', $id)->getQuery()->execute();
         * 
         */
        //print_r( array_keys($entity));
         
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\Product entity.');
        }

        $editForm = $this->createForm(new ProductType( $em, $id), $entity,
                array("attr" => array("new" => false)));        

        $imageForm  = $this->createForm( new ProductImageType(), $entity);
        return array(
            'entity'     => $entity,
            'edit_form'  => $editForm->createView(),
            'route'      => 'product',
            'image_form' => $imageForm->createView(),
            'languages'  => $languages,
            'locale'     => $locale,            
            
        );
    }

    /**
     * Edits an existing Product\Product entity.
     *
     * @Route("/{id}/update", name="product_update")
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\Product:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $locale    =  LanguageHelper::getLocale();
        $em = $this->getDoctrine()->getManager();
        $languages  = LanguageHelper::getLanguages();

        $entity = $em->getRepository('ItcAdminBundle:Product\Product')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProductType( $em, $id, true ), $entity,
                array("attr" => array("new" => false))); 
        $editForm->bind($request);
        
       // $l = $editForm->getData();
        //$l->setRelations( '' ) ;
        
        if ($editForm->isValid()) {
            $em->persist($entity);
            foreach( $languages as $lang ){                
                $preTranslit = $entity->translate( $lang )->getTranslit( );
                $count = $this->wasYet( $preTranslit, $entity->getId());  
                if ($count > 0){
                    $translit = $preTranslit."_".$entity->getId();
                    $entity->translate( $lang )->setTranslit( $translit );
                }                
            }
            $em->flush();

            return $this->redirect($this->generateUrl('product_edit', array('id' => $id)));
        }else{
            echo $editForm->getErrorsAsString();
        }
        $imageForm  = $this->createForm( new ProductImageType(), $entity);
        return array(
            
            'entity'      => $entity,
            'route'       => 'product',
            'image_form' => $imageForm->createView(),
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'languages'  => $languages,
            'locale'     => $locale,            
        );
    }
/**
     * Edits an existing Product\Product entity.
     *
     * @Route("/{id}/relative", name="product_relative")
     * @Template("ItcAdminBundle:Product\Product:relative.html.twig")
     */
    public function relativeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->getLanguages();
        $locale =  LanguageHelper::getLocale();

        $entity = $em->getRepository('ItcAdminBundle:Product\Product')->find($id);

        $editForm = $this->createForm(new ProductType(), $entity,
                array("attr" => array("new" => false)));        

        $imageForm  = $this->createForm( new ProductImageType(), $entity);
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'route' => 'product',
            'image_form'   => $imageForm->createView(),
            'languages' => $languages,
            'locale' => $locale,            
            
        );
    }
    /**
     * Edits an existing Product\Product entity.
     *
     * @Route("/relations/{title}.{_format}", name="relations",
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template()
     */
    public function RelatinsAction($title)
    {
        //$em = $this->getDoctrine()->getManager();

       // $entity = $em->getRepository('ItcAdminBundle:Product\Product')->find($id);

        //$entity=  json_decode($entity);
        return array(
            'entity'      => "",
        );
    }

    /**
     * Deletes a Product\Product entity.
     *
     * @Route("/{id}/delete", name="product_delete")
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\Product:deleteProduct.json.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Product\Product')->find($id);
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product\Product entity.');
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

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    protected function getLanguages(){
        return LanguageHelper::getLanguages();
    }
    /**
     * Edits an existing Product\Product entity.
     *
     * @Route("/{id}/change_kod", name="product_change_kod",
     * requirements={"id" = "\d+"}) 
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\Product:index.html.twig")
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
            $qb = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select('M.kod,M.productGroupId')
                        ->where('M.id = :id')
                        ->setParameter('id', $id);
            $entity = $qb->getQuery()->getResult();
            
            $oldKod = $entity[0]['kod'];
            $group_id = $entity[0]['productGroupId'];
            
            $qb = $em->createQueryBuilder('M')
                        ->update('ItcAdminBundle:Product\Product', 'M')
                        ->set('M.kod', $oldKod)
                        ->where('M.kod = :kod')
                        ->setParameter('kod', $newKod);
            if ( !is_null($group_id) )
            {                         
                        $qb->andWhere('M.productGroup = :productGroup')
                        ->setParameter('productGroup', $group_id);
            }
            else
            {
                        $qb->andWhere('M.productGroup IS NULL');
            }                
            $qb->getQuery()->execute();

            $qb = $em->createQueryBuilder('M')
                        ->update('ItcAdminBundle:Product\Product', 'M')
                        ->set('M.kod', $newKod)
                        ->where('M.id = :id')
                        ->setParameter('id', $id);
            if ( !is_null($group_id) )
            {                         
                        $qb->andWhere('M.productGroup = :productGroup')
                        ->setParameter('productGroup', $group_id);
            }
            else
            {
                        $qb->andWhere('M.productGroup IS NULL');
            }
            $qb->getQuery()->execute();  
            /*return $this->redirect($this->generateUrl('product', 
                    array('product_group_id' => $group_id,
                          'coulonpage' => 100,
                          'page' => 1
                        )));
            */
            return $this->indexAction($group_id, $coulonpage, $page, true);
        }else{            
            return false;
//            print_r($form->getErrors());
        }
/*        return array(
            'entity' => $entity,
        );
*/     }
    /**
     * Edits an existing Product entity.
     *
     * @Route("/{id}/product_update_image.{_format}", name="product_update_image",
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template()
     */
    public function updateImageAction(Request $request, $id)
    {
        //если файлик не сейвится надо раздать права
        // на запись в папку куда он должен сейвицца!!!!!!!
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ItcAdminBundle:Product\Product')->find( $id );
        $entity->setIcon("");
        $imageForm = $this->createForm( new ProductImageType(), $entity);
        $imageForm->bind( $request );
        $imageForm['iconImage']->getData()->getClientOriginalName();
        if ( $imageForm->isValid() ) {
            $em->flush();
        }
        return array(
            'entity' => $entity,
        );
    } 
    private function createChangeKodForm( $kod , $coulonpage= null, $page = null ){

       return  $this->createFormBuilder(
                    array('kod' => $kod, 'coulonpage' => $coulonpage, 'page' => $page))
                    ->add( 'kod', 'hidden' )
                    ->add( 'coulonpage', 'hidden' )
                    ->add( 'page', 'hidden' )
                    ->getForm();
    }
    
    private function getKodeForProduct($parent_id)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Product')
                                        ->createQueryBuilder('M')
                                        ->select('max(coalesce(M.kod,0)) + 1 kod');
        if(null === $parent_id)
        {
            $queryBuilder->where("M.productGroup is NULL");
        }
        else
        {
            $queryBuilder->where("M.productGroup = :parent")
                         ->setParameter('parent', $parent_id);
        }
        
        $kod = $queryBuilder->getQuery()->getSingleScalarResult();
        return is_null($kod) ? 1 : $kod ;
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
    private function wasYet($preTranslit, $entity_id = NULL)
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $defaultLocale = $this->container->parameters["locale"];
        if($locale == $defaultLocale)
        {
            $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Product')
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
            $queryBuilder = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
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
    
    
}
