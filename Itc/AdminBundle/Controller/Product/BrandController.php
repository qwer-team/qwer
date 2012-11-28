<?php

namespace Itc\AdminBundle\Controller\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Form\Product\BrandType;
use Itc\AdminBundle\Form\Product\BrandImageType;
use Itc\AdminBundle\Entity\Product\Brand;
use Itc\AdminBundle\Tools\LanguageHelper;
use Itc\AdminBundle\Tools\TranslitGenerator;



/**
 * Product\Brand controller.
 *
 * @Route("/brand")
 */
class BrandController extends Controller
{
    /**
     * Lists all Product\Brand entities.
     *
     * @Route("/{coulonpage}", name="brand",
     * requirements={"coulonpage" = "\d+"}, 
     * defaults={"coulonpage" = "100"})
     * @Template()
     */
    public function indexAction($coulonpage = 100)
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();

        $repo = $em->getRepository('ItcAdminBundle:Product\Brand');
        
        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->orderBy('M.title', 'ASC');                
                
        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $qb,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $coulonpage/*limit per page*/
        );      
        foreach ($entities as $entity)
            $deleteForm[$entity->getId()] = $this->createDeleteForm($entity->getId())
                            ->createView();
        /*
        $router = $this->get("router");
        $route = $router->match($this->getRequest()->getPathInfo());
        */
        return array(
            'entities' => $entities,
            'coulonpage' => $coulonpage,
            'locale' => $locale,
            'parent_id' => NULL,
            'route' => 'brand',
            'delete_form' => $deleteForm,
        );
    }

    /**
     * Finds and displays a Product\Brand entity.
     *
     * @Route("/{id}/show", name="brand_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Product\Brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\Brand entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Product\Brand entity.
     *
     * @Route("/new", name="brand_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Brand();
        $languages = $this->getLanguages();
        $locale =  LanguageHelper::getLocale();

        $form   = $this->createForm(new BrandType(), $entity,
                    array("attr" => array("new" => true)));


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'languages' => $languages,
            'locale' => $locale,
        );
    }

    /**
     * Creates a new Product\Brand entity.
     *
     * @Route("/create", name="brand_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\Brand:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $locale =  LanguageHelper::getLocale();
        $entity  = new Brand();
        $form = $this->createForm(new BrandType(), $entity, 
                    array("attr" => array("new" => true)) );
        $form->bind($request);
        $data = $form->getData();
                
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist( $entity );
            $em->flush();
            foreach( $this->getTranslits( $data, $entity ) as $lang => $translit ){
                $entity->translate( $lang )->setTranslit( $translit );
            }
            $em->flush();
            
            return $this->redirect($this->generateUrl('brand', 
                                    array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product\Brand entity.
     *
     * @Route("/{id}/edit", name="brand_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->getLanguages();
        $locale =  LanguageHelper::getLocale();

        $entity = $em->getRepository('ItcAdminBundle:Product\Brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\Brand entity.');
        }

        $editForm = $this->createForm(new BrandType(), $entity, 
                        array("attr" => array("new" => false)));
        
        $imageForm  = $this->createForm( new BrandImageType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'image_form' =>   $imageForm->createView(),
            'languages' => $languages,
            'locale' => $locale,
        );
    }

    /**
     * Edits an existing Product\Brand entity.
     *
     * @Route("/{id}/update", name="brand_update")
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\Brand:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $languages  = LanguageHelper::getLanguages();
        
        $entity = $em->getRepository('ItcAdminBundle:Product\Brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product\Brand entity.');
        }

        $editForm = $this->createForm(new BrandType(), $entity, 
                array("attr" => array("new" => false)));
        $editForm->bind($request);

        if ($editForm->isValid()) {

            foreach( $languages as $lang ){                
                $preTranslit = $entity->translate( $lang )->getTranslit( );
                $count = $this->wasYet( $preTranslit, $entity->getId());  
                if ($count > 0){
                    $translit = $preTranslit."_".$entity->getId();
                    $entity->translate( $lang )->setTranslit( $translit );
                }                
            }
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('brand_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Product\Brand entity.
     *
     * @Route("/{id}/delete", name="brand_delete")
     * @Method("POST")
     * @Template("ItcAdminBundle:Product\Product:deleteProduct.json.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Product\Brand')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product\Brand entity.');
            }
            $em->remove($entity);
            $em->flush();
        }else{            
            print_r($form->getErrors());
        }
        return array(
            'entity' => $entity,
        );

//        return $this->redirect($this->generateUrl('brand'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    /**
     * Edits an existing Brand entity.
     *
     * @Route("/{id}/brand_update_image.{_format}", name="brand_update_image",
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template()
     */
    public function updateImageAction(Request $request, $id)
    {
        //если файлик не сейвится надо раздать права
        // на запись в папку куда он должен сейвицца!!!!!!!
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ItcAdminBundle:Product\Brand')->find( $id );
        $entity->setIcon("");
        $imageForm = $this->createForm( new BrandImageType(), $entity);
        $imageForm->bind( $request );
        $imageForm['iconImage']->getData()->getClientOriginalName();
        if ( $imageForm->isValid() ) {
            $em->flush();
        }
        return array(
            'entity' => $entity,
        );
    }    
    
    protected function getLanguages(){
        return LanguageHelper::getLanguages();
    }
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
            $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Brand')
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
            $queryBuilder = $em->getRepository('ItcAdminBundle:Product\BrandTranslation')
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
