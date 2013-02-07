<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \Doctrine\Common\Collections\ArrayCollection;
use \Symfony\Component\HttpFoundation\Request;

//use Itc\DocumentsBundle\Entity\Pd\Pd;
//use Itc\DocumentsBundle\Entity\Pd\Pdl;

class CartController extends Controller {

    private $product = 'Itc\AdminBundle\Entity\Product\Product';

    const CART = 'cart_user';
    const PDTYPE = 1;

    /**
* ХТМЛ страничка
* @Route("/cart/checkout" ,name="cart")
* @Template()
*/
    public function indexAction(){
        
        $em = $this->getDoctrine()->getManager();
        
        $ids="";
        $products="";
        $sum=0;
        if($this->getCartSession()!=""){
        foreach($this->getCartSession() as $product){
            $ids[]=$product['id'];
            $sum=$sum+($product['price']*$product['amount']);
        }}
        if($ids!=''){
            $products = $em->getRepository($this->product)->findBy(
                array('id' => $ids));
        }
        
        return array(
            'cart'          => $this->getCartSession(),
            'products'      => $products,
            'total_price'   => $sum
            );
    }

    /**
* Добавить товар в корзину
* ид, количество
* @Route("/cart/add/{id}/{amount}", defaults={ "amount" = 1 } )
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
* @Route("/cart/remove/{id}")
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

        $summa1 = $summa2 = 0;

        $pd = new Pd();
        $pdlines = new ArrayCollection();

        foreach( $cart as $key => $product ){

            $priceOne = $product['price'];
            $amount = $product['amount'];
            $price = $priceOne * $amount;

            $summa1 += $price;
            $summa2 += $amount;

            $pdline = new Pdl();
            $pdline->setPd( $pd );
            $pdline->setSumma1( $price );
            $pdline->setSumma2( $amount );

            $pdlines->set( $key, $pdline );
        }

        $pd->setPdtypeId( self::PDTYPE );
        $pd->setN( 'cart' );
        $pd->setPdlines( $pdlines );
        $pd->setDate( date( "Y-m-d H:i:s" ) );
        $pd->setSumma1( $summa1 );
        $pd->setSumma2( $summa2 );
        $pd->setDtcor( date( "Y-m-d H:i:s" ) );

        $em = $this->getDoctrine()->getManager();
        $em->persist( $pd );
        $em->flush();

        $this->setCartSession( NULL );
        return $this->redirectIndex();
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
}

?>


