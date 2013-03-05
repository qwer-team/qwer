<?php

namespace Main\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Itc\AdminBundle\Tools\LanguageHelper;
use Itc\AdminBundle\Tools\ControllerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\User;
use Main\SiteBundle\Form\UserType;
use Main\SiteBundle\Form\UserSysType;
use Main\SiteBundle\Form\UserRememberPasswordType;
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
        
        if(! $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            return $this->forward($this->getController("login_page"));
        }

        $user = $securityContext->getToken()->getUser();
        $arr = array("attr" => array("new" => false));

        $form = $this->createForm(new UserType(), $user, $arr);
        $passForm = $this->createForm(new UserSysType(), $user, $arr);

        return array(
            "user" => $user, 
            'entity' =>"", 
            'edit_form' => $form->createView(),
            'pass_form'=> $passForm->createView()
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
            //$em->persist($entity);
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
     * @Template("MainSiteBundle:Usercabinet:index.html.twig")
     */
    public function updatePassAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $passForm = $this->createForm(new UserSysType(), $entity);
        $form = $this->createForm(new UserType(), $entity, array("attr" => array("new" => false)));
        
        $passForm->bind($request);
        $data = $passForm->getData();
        $passwd = $data->getPassword();

        if ($passForm->isValid()) {

            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $encodedPass = $encoder->encodePassword($passwd, $entity->getSalt());  
            $entity->setPassword($encodedPass);
        
            $em->flush();

            return $this->redirect($this->generateUrl('usercabinet', array('id' => $id)));
        }
        
        return array(
            'user'        => $entity,
            'entity'      => '',
            'pass_form'   => $passForm->createView(),
            'edit_form'        => $form->createView(),
        );
    }

    /**
     * @Route("/remember_password", name="remember_password")
     * @Template()
     */
    public function RememberPasswordPageAction(){

        return $this->RememberPasswordBlockAction();
    }
    /**
     * @Route("/remember_password_block", name="remember_password_block")
     * @Template()
     */
    public function RememberPasswordBlockAction(){

        $entity = new User();
        $form   = $this->createForm(new UserRememberPasswordType(), $entity, array("attr" => array("new" => true)));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/generate_remember_password", name="generate_remember_password")
     * @Template()
     */
    public function GenerateRememberPasswordUrlAction(Request $request){
        
        $em = $this->getDoctrine()->getManager();
        //print_r($request);
        $userRemPass = new UserRememberPasswordType();

        $param = $request->get($userRemPass->getName());
        $entity = $em->getRepository('ItcAdminBundle:User')
                     ->findOneBy(array('email'=>$param['email']));

        $form = $this->createForm($userRemPass, $entity, array("attr" => array("new" => true)));
        $form->bind($request);

        if($form->isValid()){
            
            $generateKey = $entity->getEmail().$entity->getUserName().mt_rand(0.001, 2.001);
            $encodedPass = md5($generateKey);
            $entity->setConfirmationToken($encodedPass);

            $em->flush();
            
            echo $this->generateUrl("change_password", array('token'=>$encodedPass), true);
            return array();
            //генерим ссылку отсылаем почту ждем ссылку возвращаем эту страницу
        }
        
        return $this->forward($this->getController("remember_password"));
    }

    
    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/change_password?token={token}", name="change_password")
     * @Template()
     */
    public function ChangePasswordAction($token){

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')
                     ->findOneBy(array('confirmationToken' => $token));

        if(!$entity) 
            return $this->forward($this->getController ("remember_password"));

        $form = $this->createForm(new UserSysType(), $entity);
        $entity->setConfirmationToken(NULL);
        
        return array(
            'form' => $form->createView(),
            'user' => $entity,
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/update_password?token={token}", name="update_password")
     * @Template()
     */
    public function UpdatePasswordAction($token){
        
        if($form->isValid()){
            $entity->setConfirmationToken(NULL);
        }

        return array();
    }
    var $newUser = array("attr" => array("new" => true));
    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/login", name="login_page")
     * @Template()
     */
    public function LoginPageAction(){

        $securityContext = $this->container->get('security.context');

        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            return $this->redirect($this->generateUrl("usercabinet"));
        }

         return $this->loginHelp();
    }
    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/login_block", name="login_block")
     * @Template()
     */
    public function LoginBlockAction()
    {
        if($this->getRequest()->getMethod() == "GET") 
            return $this->forward($this->getController("login_page"));

        return $this->loginHelp();
    }
    
    private function loginHelp(){

        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity, $this->newUser);

        $csrfToken = $this->container->get('form.csrf_provider')
                          ->generateCsrfToken('authenticate');
        
        return array(
            'entity'     => $entity,
            'form'       => $form->createView(),
            'csrf_token' => $csrfToken
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
        $request = $this->getRequest();
        if($request->getMethod() == "GET") return $this->forward($this->getController("registrations_new"));
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity, $this->newUser);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/registration/new", name="registrations_new")
     * @Template()
     */
    public function registrationPageAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity, $this->newUser);

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
     * @Template("MainSiteBundle:Usercabinet:registrationPage.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('ItcAdminBundle:Group')->findOneBy(array('role'=>'ROLE_USER'));

        $entity = new User();
        $form = $this->createForm(new UserType(), $entity, $this->newUser);
        $form->bind($request);
        $data = $form->getData();
        $passwd = $data->getPassword();
        $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
        $encodedPass = $encoder->encodePassword($passwd, $entity->getSalt());  
        $entity->setPassword($encodedPass);

        if(isset($group)){
            $entity->setGroup($group);
        }
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
