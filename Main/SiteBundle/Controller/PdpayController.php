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

class PdpayController extends ControllerHelper //Controller
{
     /**
     * @Route("{id}/", name="pdpay")
     * @Template()
     */
    public function pdpayAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ItcDocumentsBundle:Pd\Pd');
        
        $pd = $repo->createQueryBuilder('P')
                        ->select('P')
                        ->where('P.oa1 =:id')
                        ->setParameter('id', $id)
                        ->getQuery()
                        ->execute();
        return array('pds'=>$pd);
    }

}
