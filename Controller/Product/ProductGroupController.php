<?php

namespace Itc\AdminBundle\Controller\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\Product\ProductGroup;
//use Itc\AdminBundle\Controller\Product\ProductController;
use Itc\AdminBundle\Form\Product\ProductGroupType;
use Itc\AdminBundle\Tools\LanguageHelper;
use Itc\AdminBundle\Tools\BreadCrumbsGeneration;
use Itc\AdminBundle\Form\Product\ProductGroupImageType;
use Itc\AdminBundle\Tools\TranslitGenerator;



/**
 * Product\ProductGroup controller.
 *
 * @Route("/product_group")
 * @Route("/product_group/")
 */
class ProductGroupController extends Controller
{
    /**
     * Lists all Product\ProductGroup entities.
     *
     * @Route("/{coulonpage}/{parent_id}", name="product_group",
     * requirements={"parent_id" = "\d+", "coulonpage" = "\d+"}, 
     * defaults={ "parent_id" = null, "coulonpage" = "100"})    
     * @Template()
     */
    public function indexAction($parent_id, $coulonpage)
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        
        $products = $deleteForm = array();
        $deleteProductForm = $products = array();
        if (!is_null($parent_id)){
            $entity = $em->getRepository('ItcAdminBundle:Product\ProductGroup')->find($parent_id);
            if (is_null($entity)) $parent_id = null;
        }
        $repo = $em->getRepository('ItcAdminBundle:Product\ProductGroup');

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
        $entities = $qb->getQuery()->execute();
        
        foreach ($entities as $entity){
            $deleteForm[$entity->getId()] = $this->createDeleteForm($entity->getId())
                            ->createView();
        }
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        
        $router = $this->get('router');
        $fields  = array( "parent_id" => NULL, "coulonpage" => $coulonpage ) ;
        BreadCrumbsGeneration::generate($parent_id, $fields, "product_group",  
                                $repo, $breadcrumbs, $router, $locale);
        $page = $this->get('request')->query->get('page', 1);
/*        
        $repo = $em->getRepository('ItcAdminBundle:Product\Product');
        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->orderBy('M.kod', 'ASC');        
        if(null === $parent_id)
        {
            $qb->where('M.productGroup IS NULL');
        }
        else
        {
            $qb->where('M.productGroup = :productGroup')
               ->setParameter('productGroup', $parent_id);
        }        
  
        $paginator = $this->get('knp_paginator');
        $products = $paginator->paginate(
            $qb,
            $this->get('request')->query->get('page', 1)/*page number*///,
//           $coulonpage/*limit per page*/
/*        );
        foreach ($products as $entity){
            $deleteProductForm[$entity->getId()] = ProductGroupController::createDeleteForm($entity->getId())
                            ->createView();
        }
*/        return array(
            'entities' => $entities,
            'locale'    => $locale,
            'coulonpage' => $coulonpage,
            'parent_id' => $parent_id,
            'products' => $products,
            'route' => 'product_group',
            'delete_form' => $deleteForm,
            'page' => $page,
//            'delete_product_form' => $deleteProductForm,
        );
    }

    /**
     * Finds and displays a Product\ProductGroup entity.
     *
     * @Route("/{id}/show", name="product_group_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Product\ProductGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\ProductGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Product\ProductGroup entity.
     *
     * @Route("/new/{parent_id}", name="product_group_new",
     * requirements={"parent_id" = "\d+"}, defaults={ "parent_id" = null})
     * @Template()
     */
    public function newAction($parent_id)
    {
        $entity = new ProductGroup();
        $em = $this->getDoctrine()->getManager();
        $languages = $this->getLanguages();
        $locale =  LanguageHelper::getLocale();
        
        if( null !== $parent_id )
        {
            $parent = 
                $em->getRepository('ItcAdminBundle:Product\ProductGroup')->find($parent_id);
            $entity->setParent( $parent );            
        }

        $form   = $this->createForm(new ProductGroupType(), $entity,
                array("attr" => array("new" => true)));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'languages' => $languages,
            'locale' => $locale,
            'route' => 'product_group',
        );
    }

    /**
     * Creates a new Product\ProductGroup entity.
     *
     * @Route("/create", name="product_group_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\ProductGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new ProductGroup();
        $form = $this->createForm(new ProductGroupType(), $entity,
                array("attr" => array("new" => true)));        
        $form->bind($request);
        
        $data = $form->getData();
        $locale =  LanguageHelper::getLocale();
        $parent_id = $data->getParentId();
        $entity_id = $data->getId();

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setKod( $this->getKodForProductGroup( $parent_id ) );
            $em->persist($entity);
            $em->flush();
            foreach( $this->getTranslits( $data, $entity ) as $lang => $translit ){
                $entity->translate( $lang )->setTranslit( $translit );
            }
            $em->flush();
            return $this->redirect($this->generateUrl('product_group', array('parent_id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product\ProductGroup entity.
     *
     * @Route("/{id}/edit", name="product_group_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->getLanguages();
        $locale =  LanguageHelper::getLocale();

        $entity = $em->getRepository('ItcAdminBundle:Product\ProductGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\ProductGroup entity.');
        }

        $editForm = $this->createForm(new ProductGroupType(), $entity,
                array("attr" => array("new" => false)));
        
        $imageForm  = $this->createForm( new ProductGroupImageType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'image_form' =>   $imageForm->createView(),
            'languages' => $languages,
            'locale' => $locale,
        );
    }

    /**
     * Edits an existing Product\ProductGroup entity.
     *
     * @Route("/{id}/update", name="product_group_update")
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\ProductGroup:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $languages  = LanguageHelper::getLanguages();        
        $locale =  LanguageHelper::getLocale();

        $entity = $em->getRepository('ItcAdminBundle:Product\ProductGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\ProductGroup entity.');
        }

       // $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProductGroupType(), $entity,
                array("attr" => array("new" => false)));
        $editForm->bind($request);
        
        $imageForm  = $this->createForm( new ProductGroupImageType(), $entity);
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

            return $this->redirect($this->generateUrl('product_group_edit', array('id' => $id)));
        }else{
            echo $editForm->getErrorsAsString();
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'image_form'  => $imageForm->createView(),
         //   'delete_form' => $deleteForm->createView(),
            'languages' => $languages,
            'locale' => $locale,
        );
    }

    /**
     * Deletes a Product\ProductGroup entity.
     *
     * @Route("/{id}/delete", name="product_group_delete")
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\ProductGroup:deleteProductGroup.json.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Product\ProductGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product\ProductGroup entity.');
            }

            $em->remove($entity);
            $em->flush();
        }else{            
            print_r($form->getErrors());
        }
        return array(
            'entity' => $entity,
        );

//        return $this->redirect($this->generateUrl('product_group'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    /**
     * Edits an existing ProductGroup entity.
     *
     * @Route("/{id}/product_group_update_image.{_format}", name="product_group_update_image",
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template()
     */
    public function updateImageAction(Request $request, $id)
    {
        //если файлик не сейвится надо раздать права
        // на запись в папку куда он должен сейвицца!!!!!!!
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ItcAdminBundle:Product\ProductGroup')->find( $id );
        $entity->setIcon("");
        $imageForm = $this->createForm( new ProductgroupImageType(), $entity);
        $imageForm->bind( $request );
        $imageForm['iconImage']->getData()->getClientOriginalName();
        if ( $imageForm->isValid() ) {
            $em->flush();
        }
        return array(
            'entity' => $entity,
        );
    }    
    
    private function getKodForProductGroup($parent_id)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
                                        ->createQueryBuilder('M')
                                        ->select('max(coalesce(M.kod,0)) + 1 kod');
        if(null === $parent_id)
        {
            $queryBuilder->where("M.parent is NULL");
        }
        else
        {
            $queryBuilder->where("M.parent = :parent")
                         ->setParameter('parent', $parent_id);
        }
        
        $kod = $queryBuilder->getQuery()->getSingleScalarResult();
        return is_null($kod) ? 1 : $kod["kod"] ;
    }
    protected function getLanguages(){
        return LanguageHelper::getLanguages();
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
            $queryBuilder = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
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
            $queryBuilder = $em->getRepository('ItcAdminBundle:Product\ProductGroupTranslation')
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
