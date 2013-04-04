<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \Doctrine\Common\Collections\ArrayCollection;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Main\SiteBundle\Form\SendMailType;
use Itc\DocumentsBundle\Entity\Pd\Trans;
use Itc\DocumentsBundle\Entity\Pd\Pd;
use Itc\DocumentsBundle\Entity\Pd\Pdl;
use Itc\DocumentsBundle\Entity\PdOrder\PdOrder;
use Itc\AdminBundle\Tools\ControllerHelper;
use Main\SiteBundle\Form\AcceptOrderType;

class CartController extends ControllerHelper {

    protected $product = 'Itc\AdminBundle\Entity\Product\Product';
    protected $menu    = 'ItcAdminBundle:Menu\Menu';
    protected $pdtype  = 'ItcDocumentsBundle:Pd\Pdtype';

    const CART   = 'cart_user';
    const PDTYPE = 1;
 

    public function __construct($container=NULL)
    {
        $this->container = $container;
    }
    
    public function get($service)
    {
        return $this->container->get($service);
    }
    /**
     * ХТМЛ страничка
     * @Route("/cart/checkout" ,name="cart")
     * @Template()
     */
    public function indexAction($accept=null){
        
        $em = $this->getDoctrine()->getManager();
        $ordering = $this->getEntityRouting($this->menu, 'ordering');
        $ids="";
        $products="";
        $sum=0;
        $auth=0;
        if($this->getCartSession()!=""){
        foreach($this->getCartSession() as $product){
            $ids[]=$product['id'];
            $sum=$sum+($product['price']*$product['amount']);
        }}
        if($ids!=''){
            $products = $em->getRepository($this->product)->findBy(
                array('id' => $ids));
        }
        
        $securityContext = $this->container->get('security.context');
        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')){$auth=1;}
        return array(
            'cart'        => $this->getCartSession(),
            'products'    => $products,
            'total_price' => $sum,
            'auth'        => $auth,
            'accept'      => $accept,
            'ordering'    => $ordering,
            'locale'      => \Itc\AdminBundle\Tools\LanguageHelper::getLocale(),
        );
    }

    /**
     * Добавить товар в корзину
     * ид, количество
     * @Route("/cart/add/{id}/{amount}", defaults={ "amount"=1 }, name="add_to_cart")
     * @Template()
     */
    public function addAction($id, $amount, Request $request){

        $entity = $this->getEntity($this->product)->find($id);

        if($entity && $amount > 0){

            $cart = $this->getCartSession();

            $cart[$entity->getId()] = array(
                'id' => $entity->getId(),
                'title' => $entity->getTitle(),
                'price' => $entity->getPrice(),
                'amount' => $amount
            );

            $this->setCartSession($cart);

        } elseif($amount == 0){
            
            $this->removeProduct($id);
        }
        
        return $this->redirectToCart();
    }

    /**
     * удалить товар из корзины по ид
     * @Route("/cart/remove/{id}", name="remove_cart_item")
     * @Template()
     */
    public function removeAction($id){

        $this->removeProduct($id);
        return $this->redirectToCart();
    }

    private function removeProduct($id){

        $cart = $this->getCartSession();
        unset($cart[$id]);

        $this->setCartSession($cart);
    }

    /**
     * очистить корзину
     * @Route("/cart/clear")
     * @Template()
     */
    public function clearAction(){

        $this->setCartSession(NULL);
        return $this->redirectToCart();
    }

    /**
     * поддтвердить покупку
     * @Route("/cart/accept")
     * @Template()
     */
    public function acceptAction($user = NULL)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = $this->getCartSession();

        if(! $cart) return $this->redirectToCart();
        
        $securityContext = $this->container->get('security.context');
        
        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            $user = $securityContext->getToken()->getUser();
            $userInfo = array(
                'id'      => $user->getId(),
                'fio'     => $user->getFIO(),
                'telefon' => $user->getTel(),
                'address' => $user->getAddress(),
                'email'   => $user->getEmail(),
            );
        } else {
            $form = new AcceptOrderType;
            $userInfo = $this->getRequest()->get($form->getName());
        }
        

        $summa1 = $summa2 = 0;
        $pd = new Pd();
        $pdtype = $em->getRepository($this->pdtype)->find(self::PDTYPE);
        $pd->setPdtype($pdtype);

        $pdlines = new ArrayCollection();
       
        $mainproducts="Пользователь {$userInfo['fio']} c номером телефона {$userInfo['telefon']} проживающий по адресу: {$userInfo['address']}";
        $mainproducts.="<table border='1'><tr><td>Товар</td><td>Цена за шт.</td><td>Количество</td><td>Итого</td></tr>";

        foreach($cart as $key => $product){

            $priceOne = $product['price'];
            $amount   = $product['amount'];
            $price    = $priceOne * $amount;
            $summa1  += $price;
            $summa2  += $amount;
            $mainproducts .= "<tr><td>{$product['title']}</td><td>{$product['price']}</td><td>{$product['amount']}</td><td>".$product['amount']*$product['price']."</td></tr>";

            $pdline = new Pdl();
            $pdline->setPd($pd);
            $pdline->setSumma1($price);
            $pdline->setSumma2($amount);

            $pdlines->set($key, $pdline);
        }
        $mainproducts.="<tr><td></td><td></td><td>{$summa2}</td><td>Общая сумма: {$summa1}</td></tr></table><br/> Поступил в: ".date('Y-m-d H:i:s');
        
        $pd->setN('cart');
        $pd->setPdlines($pdlines);
        $pd->setDate(date("Y-m-d H:i:s"));
        $pd->setSumma1($summa1);
        $pd->setSumma2($summa2);
        $pd->setDtcor(date("Y-m-d H:i:s"));
        $pd->setStatus(1);
        
        if(isset($userInfo['id'])){
            $pd->setOa1($userInfo['id']);
            //$transaction= new Trans();
            //$transaction->setPd($pd);
            //$transaction->setSumma($summa1);
            //$transaction->setIL2($userInfo['id']);
            //$transaction->setOL2($userInfo['id']);
            //$pd->addTransaction($transaction);
        }

        $em->persist($pd);
        $em->flush();

        $email = "neversmoke@i.ua";

        $sendmail = $this->container->get("sendmail.service");
        $sendmail->from($userInfo['email'])
                 ->to($email)
                 ->subject('Order')
                 ->body($mainproducts)
                 ->send();

        $sendmail->from($email)
                 ->to($userInfo['email'])
                 ->subject('Order')
                 ->body($mainproducts)
                 ->send();

        $this->setCartSession(NULL);
        $this->redirectToCart(NULL);
    }

    private function redirectToCart($respons = true)
    {
        $httpKernel = $this->container->get('http_kernel');
        return $httpKernel->forward("MainSiteBundle:Cart:index");
    }
    /**
     * Добавить в сессию
     * @param type $newCart
     * @return type
     */
    private function setCartSession($newCart)
    {
        return $this->getRequest()->getSession()->set(self::CART, $newCart);
    }

    /**
     * Получить из сессии
     * @return type
     */
    private function getCartSession()
    {
        return $this->getRequest()->getSession()->get(self::CART);
    }

    /**
     * что-то такое...
     * @param type $entityName
     * @return type
     */
    private function getEntity($entityName)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository($entityName);
        return $qb;
    }

    /**
     * @Route("/small_cart", name="small_cart")
     * @Template()
     */
    public function SmallCartAction($html="", $auth=NULL)
    {
        $col=0;
        $sum=0;
        $cart="";
        if($this->getCartSession()!=''){
            $cart=$this->getCartSession();
            
            foreach($this->getCartSession() as $product){
                $col=$col+1;
                $sum=$sum+$product['price']*$product['amount'];
            }
        }
        return array(
            'cart' => $cart, 
            'sum'  => $sum, 
            'col'  => $col,
            'html' => $html,
            'auth' => $auth,
       );
    }
}

?>


