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
                    $editForm = $this->createForm(new UserType(), $user);
                    $passForm = $this->createForm(new UserSysType(), $user);
                    $arr=array("user" => $user, 'entity' =>"", 'edit_form' => $editForm->createView(),
                               'pass_form'=> $passForm->createView());
                    
                    $template = sprintf('MainSiteBundle:Usercabinet:index.html.%s', $this->container->getParameter('fos_user.template.engine'));

                    return $this->container->get('templating')->renderResponse($template, $arr);
                
                    
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
     * @Template("HOfficeSiteBundle:Usercabinet:index.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $editForm = $this->createForm(new UserType(), $entity);
        $editForm->bind($request);
        $data = $editForm->getData();
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('usercabinet'));
        }
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        );
    }
      /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/usercab_pass", name="usercab_update_pass")
     * @Method("POST")
     * @Template("HOfficeSiteBundle:Usercabinet:index.html.twig")
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
            'entity'      => $entity,
            'pass_form'   => $editForm->createView(),
            'edit_form'   => $editForm1->createView(),
        );
    }
}
