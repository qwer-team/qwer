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
use Main\SiteBundle\Tools\ControllerHelper;

class CartController extends ControllerHelper {

    private $product = 'Itc\AdminBundle\Entity\Product\Product';
    private $menu    = array( 
        'ItcAdminBundle:Menu\Menu',
        'ItcAdminBundle:Menu\MenuTranslation'
    );
    private $pdtype = "ItcDocumentsBundle:Pd\Pdtype";

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
        if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){$auth=1;}
        return array(
            'cart'        => $this->getCartSession(),
            'products'    => $products,
            'total_price' => $sum,
            'auth'        => $auth,
            'accept'      => $accept,
            'ordering'    => $ordering,
        );
    }

    /**
     * Добавить товар в корзину
     * ид, количество
     * @Route("/cart/add/{id}/{amount}", defaults={ "amount"=1 }, name="add_to_cart" )
     * @Template()
     */
    public function addAction( $id, $amount, Request $request ){

        $entity = $this->getEntity( $this->product )->find( $id );

        if( $entity && $amount > 0 ){

            $cart = $this->getCartSession();

            $cart[$entity->getId()] = array(
                'id' => $entity->getId(),
                'title' => $entity->getTitle(),
                'price' => $entity->getPrice(),
                'amount' => $amount
            );

            $this->setCartSession( $cart );

        } elseif( $amount == 0 ){
            
            $this->removeProduct( $id );
        }
        
        return $this->redirectToCart();
    }

    /**
     * удалить товар из корзины по ид
     * @Route("/cart/remove/{id}", name="remove_cart_item")
     * @Template()
     */
    public function removeAction( $id ){

        $this->removeProduct( $id );
        return $this->redirectToCart();
    }

    private function removeProduct( $id ){

        $cart = $this->getCartSession();
        unset( $cart[$id] );

        $this->setCartSession( $cart );
    }

    /**
     * очистить корзину
     * @Route("/cart/clear")
     * @Template()
     */
    public function clearAction(){

        $this->setCartSession( NULL );
        return $this->redirectToCart();
    }

    /**
     * поддтвердить покупку
     * @Route("/cart/accept")
     * @Template()
     */
    public function acceptAction(){

        $cart = $this->getCartSession();

        if( ! $cart ) return $this->redirectToCart();
        
        $securityContext = $this->container->get('security.context');
        
        if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')){
             $user= $securityContext->getToken()->getUser();
        }
        $summa1 = $summa2 = 0;
        $pd = new PdOrder();
        $pdlines = new ArrayCollection();
       
        $mainproducts="Пользователь {$user->getFIO()} c номером телефона {$user->getTel()} проживающий по адресу: {$user->getAddress()}";
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
        $mainproducts.="<tr><td></td><td></td><td>{$summa2}</td><td>Общая сумма: {$summa1}</td></tr></table><br/> Поступил в: ".date( 'Y-m-d H:i:s' );
        $pd->setPdtypeId( self::PDTYPE );
        $pd->setN( 'cart' );
        $pd->setPdlines( $pdlines );
        $pd->setDate( date( "Y-m-d H:i:s" ) );
        $pd->setSumma1( $summa1 );
        $pd->setSumma2( $summa2 );
        $pd->setDtcor( date( "Y-m-d H:i:s" ) );
        
        if( is_object($user) ){
             
             $pd->setUser($user);
                    
                    $transaction= new Trans();
                    $transaction->setPd($pd);
                    $transaction->setSumma($summa1);
                    $transaction->setIL2($user->getId());
                    $transaction->setOL2($user->getId());
             $pd->addTransaction($transaction);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist( $pd );
        $em->flush();

        $email="lenkov.alex@itcompany.kiev.ua";
        //$this->sendMailAction($mainproducts, $user, $user->getEmail(), $email);
        //$this->sendMailAction($mainproducts, $user, $email, $user->getEmail());
        return $this->clearAction();
    }

    public function sendMailAction($body, $user, $from, $to){
         
        $sendMailType = new SendMailType( LanguageHelper::getLocale() );
        $body = $this->renderView( 'MainSiteBundle:Default:OrderMail.html.twig', 
                                array( 'text' => $body) );

            $message = \Swift_Message::newInstance()
                        ->setSubject( 'Новый заказ' )
                        ->setFrom( $from )
                        ->setTo( $to )
                        ->setBody( $body , 'text/html');
            $this->get( 'mailer' )->send( $message );

    }
    /**
     * Показать карзину
     * @param type $respons
     * @return type
     */
    private function redirectIndex( $respons = true ){
        
        $arr = array( 'respons' => $respons );
        $httpKernel = $this->container->get('http_kernel');
        return $httpKernel->forward( "MainSiteBundle:Cart\Cart:index", $arr );
    }
    private function redirectToCart( $respons = true ){
        
        $arr = array( 'respons' => $respons );
        $httpKernel = $this->container->get('http_kernel');
        return $httpKernel->forward( "MainSiteBundle:Cart:index" );
    }
    /**
     * Добавить в сессию
     * @param type $newCart
     * @return type
     */
    private function setCartSession( $newCart ){

        return $this->getRequest()->getSession()->set( self::CART, $newCart );
    }

    /**
     * Получить из сессии
     * @return type
     */
    private function getCartSession(){

        return $this->getRequest()->getSession()->get( self::CART );
    }

    /**
     * что-то такое...
     * @param type $entityName
     * @return type
     */
    private function getEntity( $entityName ){

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository( $entityName );
        return $qb;
    }

    /**
     * @Route("/small_cart", name="small_cart")
     * @Template()
     */
   public function SmallCartAction($html="", $auth=NULL){

        $col=0;
        $sum=0;
        $cart="";
        if($this->getCartSession()!=''){
            $cart=$this->getCartSession();
            foreach($this->getCartSession() as $product){
                $col=$col+1;
                $sum=$sum+$product['price'];
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


