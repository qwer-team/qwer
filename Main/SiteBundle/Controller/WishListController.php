<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Itc\AdminBundle\Tools\ControllerHelper;
use Itc\AdminBundle\Entity\Product\WishList;

/**
 * News controller.
 * Routing registered in routing.yml
 */
class WishListController extends ControllerHelper //Controller
{
    
    const CART_USER = 'cart_user';
   /**
    * @Route("/cart/updatecartmass" ,name="update_wish_mass")
    * @Template()
    */
    public function UpdateCartMassAction(Request $request)
    {
        $params=$request->request;
        
        $cart=$this->getCart();
        
        foreach ($params as $key => $value) {
            $cart[$key]=array(
                'id' => $cart[$key]['id'],
                'title' => $cart[$key]['title'],
                'price' => $cart[$key]['price'],
                'amount' => $value
            );
             $this->setCartSession( $cart );
        }
       return $this->redirect($this->generateUrl('cart')); 
    }
   /**
    * @Route("/addtowish/{id}" ,name="add_to_wish")
    * @Template()
    */
    public function AddToWishAction($id)
    {
       $entity = new WishList();
       $user=  $this->getTokenUser();
       if(is_object($user)){
           $em = $this->getDoctrine()->getManager();
           
           $product=$em->getRepository('ItcAdminBundle:Product\Product')->find($id);
           
           $entity->setProduct($product);
           $entity->setUser($user);
           $em->persist( $entity );
           $em->flush();
       }
       return $this->redirect($this->generateUrl('wish_list')); 
    }
   /**
    * @Route("/removefromwish/{id}" ,name="wish_remove_one")
    * @Template()
    */
    public function RemoveWishAction($id)
    {
           $user=  $this->getTokenUser();
        
           $em = $this->getDoctrine()->getManager();
           $product=$em->getRepository('ItcAdminBundle:Product\WishList')->findOneBy(array('product'=>$id, 'user_prod'=>$user->getId()));
           $em->remove($product);
           $em->flush();
       
       return $this->redirect($this->generateUrl('wish_list')); 
    }
   /**
    * @Route("/wishlist/clear" ,name="wish_list_clear")
    * @Template()
    */
    public function ClearWishAction()
    {      
           $em = $this->getDoctrine()->getManager();
           $user=  $this->getTokenUser();
        if(is_object($user)){
           $entities=$user->getWish();
               foreach ($entities as $value) {
                    $em->remove($value);
                }
        }
         $em->flush();
       return $this->redirect($this->generateUrl('wish_list')); 
    }
   /**
    * @Route("/allinbag/user" ,name="all_in_bag")
    * @Template()
    */
    public function AllInBagAction()
    {
       $user=  $this->getTokenUser();
        
        if(is_object($user)){
           $entities=$user->getWish();
           $cart=$this->getCart();
            
           foreach ($entities as $value) {
               $cart[$value->getProduct()->getId()]=array(
                'id' => $value->getProduct()->getId(),
                'title' => $value->getProduct()->getTitle(),
                'price' => $value->getProduct()->getPrice(),
                'amount' => 1
            );
              
           }
          $this->setCartSession( $cart );
       }
       
       return $this->redirect($this->generateUrl('cart')); 
    }
   /**
    * @Route("/wishlist/user" ,name="wish_list")
    * @Template()
    */
    public function WishListAction()
    {
       $entities=array();
       $p=$total_price=0;
       
       $user=  $this->getTokenUser();
       if(is_object($user)){
           
           $entities=$user->getWish();
           foreach ($entities as $value) {
              $entities[$p]=$value->getProduct();
              $total_price=$value->getProduct()->getPrice();
              $p++;
           }
          
       }
       return array(
            'entities'          => $entities,
            'total_price'       => $total_price,
        );
    }
    
    
    
    
  public function getTokenUser(){
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            return           $user= $securityContext->getToken()->getUser();
        }
        else{
            return $user="";
        }
    }
   private function setCartSession( $newCart ){

        return $this->getRequest()->getSession()->set( self::CART_USER, $newCart );
    }
 private function getCart(){

        return $this->getRequest()->getSession()->get( self::CART_USER );
    }
}
