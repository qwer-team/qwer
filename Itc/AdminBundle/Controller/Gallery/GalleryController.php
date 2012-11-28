<?php

namespace Itc\AdminBundle\Controller\Gallery;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\Gallery\Gallery;
use Itc\AdminBundle\Form\Gallery\GalleryType;

/**
 * Gallery\Gallery controller.
 *
 * @Route("/gallery")
 */
class GalleryController extends Controller
{
    /**
     * Lists all Gallery\Gallery entities.
     *
     * @Route("/{menu_id}/{gallery_id}", name="gallery",
     * requirements={"gallery_id" = "\d+", "menu_id" = "\d+"}, defaults={ "gallery_id" = null})
     * @Template()
     */
    public function indexAction($menu_id, $gallery_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = 
            $em->getRepository('ItcAdminBundle:Gallery\Gallery')
               ->findByMenuId($menu_id);
        
        $updateForm = $deleteForm = array();        
        foreach ($entities as $entity){
            $updateForm[$entity->getId()] = 
                            $this->createForm(new GalleryType(), $entity)
                            ->createView();
            $deleteForm[$entity->getId()] = $this->createDeleteForm($entity->getId())
                            ->createView();
        }

        return array(
            'menu_id' => $menu_id,
            'forms' => $updateForm,
            'delete_form' => $deleteForm,
            'gallery_id' => $gallery_id
        );
    }

    /**
     * Finds and displays a Gallery\Gallery entity.
     *
     * @Route("/{id}/show", name="gallery_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Gallery\Gallery')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gallery\Gallery entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Gallery\Gallery entity.
     *
     * @Route("/new/{menu_id}", name="gallery_new",
     * requirements={"menu_id" = "\d+"})
     * @Template()
     */
    public function newAction($menu_id)
    {
        $entity = new Gallery();
        
        
        $em = $this->getDoctrine()->getManager();
        $menu = $em->getRepository('ItcAdminBundle:Menu\Menu')
                   ->findOneById($menu_id);
        $entity->setMenu($menu);
        $entity->setMenuId($menu_id);
        $form   = $this->createForm(new GalleryType(), $entity);
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'menu_id' => $menu_id,
        );
    }

    /**
     * Creates a new Gallery\Gallery entity.
     *
     * @Route("/create", name="gallery_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:Gallery\Gallery:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Gallery();
        $form = $this->createForm(new GalleryType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $menu = $em->getRepository('ItcAdminBundle:Menu\Menu')
                       ->findOneById($entity->getMenuId());
            $entity->setMenu($menu);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gallery', array('menu_id' => $entity->getMenuId())));
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Gallery\Gallery entity.
     *
     * @Route("/{id}/edit", name="gallery_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Gallery\Gallery')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gallery\Gallery entity.');
        }

        $editForm = $this->createForm(new GalleryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Edits an existing Gallery\Gallery entity.
     *
     * @Route("/{id}/update.{_format}", name="gallery_update",
     * defaults={"_format" = "json"})
     * @Method("POST")
     * @Template()
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();        
        $entity = $em->getRepository('ItcAdminBundle:Gallery\Gallery')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gallery\Gallery entity.');
        }

        $deleteForm = $this->createDeleteForm($id);        
        $updateForm = $this->createForm(new GalleryType(), $entity);
        $updateForm->bind($request);
        
        if ($updateForm->isValid()) {
            //$em->persist($entity);
            $em->flush();

//            return $this->redirect($this->generateUrl('gallery_edit', array('id' => $id)));
        return array(
            'entity'      => $entity,
            );
            
        }
/*
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );*/
        return array();
    }

    /**
     * Deletes a Gallery\Gallery entity.
     *
     * @Route("/{id}/delete", name="gallery_delete")
     * @Method("POST")
     * @Template("ItcAdminBundle:Gallery\Gallery:update.json.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Gallery\Gallery')->find($id);
            $menu_id = $entity->getMenu()->getId();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Gallery\Gallery entity.');
            }

            $em->remove($entity);
            $em->flush();
        }else{            
            print_r($form->getErrors());
        }
        return array(
            'entity' => $entity,
        );

/*        return $this->redirect($this->generateUrl('gallery', 
                                            array( 'menu_id' => $menu_id )
                                                   )
                               );
  */  }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
