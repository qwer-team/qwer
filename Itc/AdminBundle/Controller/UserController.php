<?php

namespace Itc\AdminBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\User;
use Itc\AdminBundle\Form\UserType;
use Itc\AdminBundle\Form\UserSysType;
use Itc\AdminBundle\Form\Userall;

/**
 * User controller.
 *
 * @Route("/user")
 * @Route("/user/")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ItcAdminBundle:User')->findAll();
        $deleteForm = $this->createDeleteForm($entities);
 
        return array(
            'entities' => $entities,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/show", name="user_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/create", name="user_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new User();
        $form = $this->createForm(new UserType(), $entity);
        $form->bind($request);
        $data = $form->getData();
        $passwd = $data->getPassword();
        
            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $encodedPass = $encoder->encodePassword($passwd, $entity->getSalt());  
            $entity->setPassword($encodedPass);
            $entity->setEnabled(true);
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            
            return $this->redirect($this->generateUrl('user_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $passForm = $this->createForm(new UserSysType(), $entity);
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'pass_form'   => $passForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/update", name="user_update")
     * @Method("POST")
     * @Template("ItcAdminBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UserType(), $entity);
        $editForm->bind($request);
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/user_update_pass", name="user_update_pass")
     * @Method("POST")
     * @Template("ItcAdminBundle:User:edit.html.twig")
     */
    public function updatePassAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $editForm = $this->createForm(new UserSysType(), $entity);
        $editForm1 = $this->createForm(new UserType(), $entity);
        
        $editForm->bind($request);
        $data = $editForm->getData();
        $passwd = $data->getPassword();
        
            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $encodedPass = $encoder->encodePassword($passwd, $entity->getSalt());  
            $entity->setPassword($encodedPass);
            
        if ($editForm->isValid()) {
            $em->flush();
           return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }
        
        return array(
            'entity'      => $entity,
            'pass_form'   => $editForm->createView(),
            'edit_form'   => $editForm1->createView(),
        );
    }
    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="user_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
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
}
