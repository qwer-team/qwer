<?php

namespace Itc\AdminBundle\Controller\Gallery;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\Gallery\Image;
use Itc\AdminBundle\Form\Gallery\ImageType;
use Itc\AdminBundle\Form\Gallery\SmallImageType;
use Imagine\Gd\Imagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Itc\AdminBundle\Tools\LanguageHelper;


/**
 * Gallery\Image controller.
 *
 * @Route("/image")
 */
class ImageController extends Controller
{
    /**
     * Lists all Gallery\Image entities.
     *
     * @Route("/{gallery_id}", name="image",
     * requirements={"gallery_id" = "\d+"})
     * @Template()
     */
    public function indexAction($gallery_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ItcAdminBundle:Gallery\Image')
                        ->findByGallery($gallery_id);
/*
        $entity = new Image();
        $entity->setGallery($gallery);        
        $form   = $this->createForm(new ImageType(), $entity, 
                                        array("attr" => array("new" => true)));        
        */
        $updateForm = $deleteForm = array();    
        
        foreach ($entities as $entity){
            $updateForm[$entity->getId()] = 
                            $this->createForm(new ImageType(), $entity,
                                    array("attr" => array("new" => false)))
                            ->createView();
            $deleteForm[$entity->getId()] = $this->createDeleteForm($entity->getId())
                            ->createView();
            
        }
        return array(
            'entities' => $entities,
            'update_form' => $updateForm,
            'delete_form' => $deleteForm,            
            //form'   => $form->createView(),
            'gallery_id' => $gallery_id,
        );
    }

    /**
     * Finds and displays a Gallery\Image entity.
     *
     * @Route("/{id}/show", name="image_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Gallery\Image')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gallery\Image entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Gallery\Image entity.
     *
     * @Route("/new/{gallery_id}", name="image_new",
     * requirements={"gallery_id" = "\d+"})
     * @Template()
     */
    public function newAction($gallery_id)
    {
        $entity = new Image();
        $em = $this->getDoctrine()->getManager();
        
        $gallery = $em->getRepository('ItcAdminBundle:Gallery\Gallery')
                      ->find($gallery_id);
        $entity->setGallery($gallery);
        
        $form = $this->createForm(new ImageType(), $entity, 
                                        array("attr" => array("new" => true)));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'gallery_id' => $gallery_id,            
        );
    }

    /**
     * Creates a new Gallery\Image entity.
     *
     * @Route("/create", name="image_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:Gallery\Image:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Image();
        $form = $this->createForm(new ImageType(), $entity, 
                                    array("attr" => array("new" => true)));
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl(
                        'gallery', 
                        array('menu_id' => $entity->getGallery()->getMenuId(),
                              'gallery_id' =>  $entity->getGallery()->getId()                            
                             )
                                                    )
                                  );
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Gallery\Image entity.
     *
     * @Route("/{id}/edit", name="image_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $languages  = LanguageHelper::getLanguages();
        $locale  = LanguageHelper::getlocale();

        $entity = $em->getRepository('ItcAdminBundle:Gallery\Image')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gallery\Image entity.');
        }

        $editForm = $this->createForm(new ImageType(), $entity, 
                                       array("attr" => array("new" => false)));
        $smallImage = $this->createForm(new SmallImageType());
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'small_image_form' => $smallImage->createView(),
            'languages' => $languages,
            'locale' => $locale,            
        );
    }

    /**
     * Edits an existing Gallery\Image entity.
     *
     * @Route("/{id}/update", name="image_update")
     * @Method("POST")
     * @Template("ItcAdminBundle:Gallery\Image:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Gallery\Image')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gallery\Image entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ImageType(), $entity, 
                                        array("attr" => array("new" => false)));
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('image_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Gallery\Image entity.
     *
     * @Route("/{id}/delete", name="image_delete")
     * @Method("POST")
     * @Template("ItcAdminBundle:Gallery\Gallery:update.json.twig")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Gallery\Image')->find($id);
            $gallery_id = $entity->getGallery()->getId(); 
            $menu_id = $entity->getGallery()->getMenuId();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Gallery\Image entity.');
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
                                    array( 'menu_id' =>  $menu_id,
                                           'gallery_id' => $gallery_id )
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
    /**
     * Edits an existing Gallery\Image entity.
     *
     * @Route("/{id}/small_update", name="small_image_update")
     * @Method("POST")
     */
    public function smallImageUpdate($id, Request $request)
    {
        $form = $this->createForm(new SmallImageType());
        $form->bindRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = 
                $em->getRepository('ItcAdminBundle:Gallery\Image')->find($id);
            $data = $form->getData();
            $rootDir =  $this->container->get('kernel')->getRootDir();
            $helper = $this->container
                       ->get('vich_uploader.templating.helper.uploader_helper');
            $imagePath = $rootDir."/../web".$helper->asset($entity, 'image');
            $smallImagePath = 
                    $rootDir."/../web".$helper->asset($entity, 'smallImage');
            $imagine = new Imagine();
            $mode    = ImageInterface::THUMBNAIL_INSET;
            $imagine->open($imagePath)
                    ->crop(
                            new Point($data["x"], $data["y"]),
                            new Box($data["w"], $data["h"])
                           )
                    ->thumbnail(new Box(100, 100), $mode)
                    ->save($smallImagePath);
        }
        return 
        $this->redirect($this->generateUrl('image_edit', array( 'id' =>  $id)));
    }
}
