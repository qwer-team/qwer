<?php

namespace Main\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Itc\AdminBundle\Tools\LanguageHelper;
use Main\SiteBundle\Tools\ControllerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\User;
use Main\SiteBundle\Form\UserType;
use Main\SiteBundle\Form\UserSysType;
/**
 * @Route("/usercabinet", name="usercabinet_con")
 */
class UsercabinetController extends ControllerHelper //Controller
{
    /**
     * @Route("/", name="usercabinet")
     * @Template()
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
         if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
                    $user= $securityContext->getToken()->getUser();
                    $editForm = $this->createForm(new UserType(), $user, array("attr" => array("new" => false)));
                    $passForm = $this->createForm(new UserSysType(), $user, array("attr" => array("new" => false)));
                    $arr=array("user" => $user, 'entity' =>"", 'form' => $editForm->createView(),
                               'pass_form'=> $passForm->createView());
                    
                   
                    return $arr;
                
                    
                }
        return array( 
            'user' => "",
            'entity' =>"",
            'edit_form'   => "",
            'pass_form'   => ""
        );
    }

     /**
     * Edits an existing User entity.
     *
     * @Route("{id}/update", name="update_usercab")
     * @Method("POST")
     * @Template("")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $editForm = $this->createForm(new UserType(), $entity, array("attr" => array("new" => false)));
        $passForm = $this->createForm(new UserSysType(), $entity, array("attr" => array("new" => false)));
        $editForm->bind($request);
        $data = $editForm->getData();
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('usercabinet'));
        }
        return array(
            'user'        => $entity,
            'entity'      => '',
            'pass_form'   => $passForm->createView(),
            'edit_form'   => $editForm->createView()
        );
    }
      /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/usercab_pass", name="usercab_update_pass")
     * @Method("POST")
     * @Template("")
     */
    public function updatePassAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $editForm = $this->createForm(new UserSysType(), $entity);
        $editForm1 = $this->createForm(new UserType(), $entity, array("attr" => array("new" => false)));
        
        $editForm->bind($request);
        $data = $editForm->getData();
        $passwd = $data->getPassword();
        
            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $encodedPass = $encoder->encodePassword($passwd, $entity->getSalt());  
            $entity->setPassword($encodedPass);
            
        if ($editForm->isValid()) {
            $em->flush();
           return $this->redirect($this->generateUrl('usercabinet', array('id' => $id)));
        }
        
        return array(
            'user'        => $entity,
            'entity'      => '',
            'pass_form'   => $editForm->createView(),
            'edit_form'   => $editForm1->createView(),
        );
    }
      /**
     * Displays a form to create a new User entity.
     *
     * @Route("/registration", name="registration")
     * @Template()
     */
    public function registrationAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity, array("attr" => array("new" => true)));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/registration/create", name="registrations_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('ItcAdminBundle:Group')->findBy(array('role'=>'ROLE_USER'));
        foreach($group as $g)
        {
            $group=$g;
        }
        $entity = new User();
        $form = $this->createForm(new UserType(), $entity, array("attr" => array("new" => true)));
        $form->bind($request);
        $data = $form->getData();
        $passwd = $data->getPassword();
            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $encodedPass = $encoder->encodePassword($passwd, $entity->getSalt());  
            $entity->setPassword($encodedPass);
            $entity->setGroup($group);
            $entity->setEnabled(true);
            $entity->setFIO($data->getSurname()." ".$data->getName()." ".$data->getPatronymic());
        if ($form->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            
            return $this->redirect($this->generateUrl('index'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
}
