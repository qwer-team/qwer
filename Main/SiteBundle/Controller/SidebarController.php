<?php
namespace Main\SiteBundle\Controller;

use \Main\SiteBundle\Tools\ControllerHelper;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Main\SiteBundle\Form\SendMailType;
use Itc\AdminBundle\Tools\TranslitGenerator;

class SidebarController extends ControllerHelper{
    
    public function __construct() {
        ;
    }
    
    /**
     * @Template()
     */
    public function SidebarAction(){
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ItcAdminBundle:Menu\Menu')->findBy(array('parent'=>null));
        
        foreach($entities as $entity){
            $ens[$entity->getRouting()] = $entity;
        }
        
        return array(
            'entities' => $ens,
            "locale" => LanguageHelper::getLocale(),
        );
    }
    
    /**
     * @Template()
     */
    public function Sidebar2Action($entity, $routing=null){

        return array(
            "entity"  => $entity,
            "locale"  => LanguageHelper::getLocale(),
            "routing" => $routing
        );
    }

    /**
     * @Route("/photo_video_block", name="photo_video_block")
     * @Template()
     */
    public function PhotoVideoBlockAction(){

        return array(
            "entity" => $this->getEntityRouting($this->menu, self::PHOTO_VIDEO),
            "locale" => LanguageHelper::getLocale(),
        );
    }

    /**
     * @Route("/photo_video_block", name="photo_video_block")
     * @Template()
     */
    public function QuestionAnswerBlockAction(){

        return array(
            "entity" => $this->getEntityRouting($this->menu, self::QUESTION_ANSWER),
            "locale" => LanguageHelper::getLocale(),
        );
    }
}

?>
