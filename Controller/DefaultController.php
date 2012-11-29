<?php

namespace Main\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;

/**
 * Default controller.
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", defaults={ "_locale" = "ru"}, name="index")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();
        $entity = $entities[0];
        
        $galleries = $entity->getGalleries();
        $images    = $galleries[0]->getImages();
        
        return array( 
            'entities'  => $entities,
            'entity'    => $entity,
            'images'    => $images,
        );
    }
    /**     
     * Lists all Menu entities.
     * @Template()
     */
    public function menuAction(){

        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $languages  = LanguageHelper::getLanguages();
        
        $request = $this->container->get('request')->getPathInfo();
//$routeName = $request->get('_route');
        
        $queryBuilder = $em->getRepository('ItcAdminBundle:Menu\Menu')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $arr = $queryBuilder->getQuery()->execute();
        
        $entities = $child_entities = array();

        foreach($arr as $v)
            if (is_null($v->getParentId()) )
                $entities[] = $v;
                
        return array( 
            "entities"  => $entities,
            "locale"    => $locale,
            'locale'    => $locale,
            'languages' => $languages,
            'route'     => $request,
        );
    }
    
}
