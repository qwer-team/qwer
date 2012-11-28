<?php

namespace Itc\AdminBundle\Controller\Translation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\Translation\TranslationNamespace;
use Itc\AdminBundle\Form\Translation\TranslationNamespaceType;
use Itc\AdminBundle\Tools\LanguageHelper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Filesystem\Filesystem;
use \Itc\AdminBundle\ItcAdminBundle;

/**
 * Translation\TranslationNamespace controller.
 *
 * @Route("/translation")
 */
class TranslationNamespaceController extends Controller
{
    /**
     * Lists all Translation\TranslationNamespace entities.
     *
     * @Route("/", name="translation")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ItcAdminBundle:Translation\TranslationNamespace')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Translation\TranslationNamespace entity.
     *
     * @Route("/{id}/show", name="translation_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Translation\TranslationNamespace')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Translation\TranslationNamespace entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        
        list($translations, $props) = 
                                $this->getTranslations($entity->getNamespace(),
                                                       $entity->getBundle());
        return array(
            'entity'        => $entity,
            'delete_form'   => $deleteForm->createView(),
            'translations'  => $translations,
            'langs'         => LanguageHelper::getLanguages(),
            'props'         => $props
        );
    }

    /**
     * Displays a form to create a new Translation\TranslationNamespace entity.
     *
     * @Route("/new", name="translation_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TranslationNamespace();
        $form   = $this->createForm(new TranslationNamespaceType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Translation\TranslationNamespace entity.
     *
     * @Route("/create", name="translation_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:Translation\TranslationNamespace:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new TranslationNamespace();
        $form = $this->createForm(new TranslationNamespaceType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('translation_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Translation\TranslationNamespace entity.
     *
     * @Route("/{id}/edit", name="translation_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Translation\TranslationNamespace')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Translation\TranslationNamespace entity.');
        }
        
        list($translations, $props) = 
                                $this->getTranslations($entity->getNamespace(),
                                                       $entity->getBundle());
        
        $editForm = $this->createForm(new TranslationNamespaceType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'translations'  => $translations,
            'langs'         => LanguageHelper::getLanguages(),
            'props'         => $props
        );
    }

    /**
     * Edits an existing Translation\TranslationNamespace entity.
     *
     * @Route("/{id}/update", name="translation_update")
     * @Method("POST")
     * @Template("ItcAdminBundle:Translation\TranslationNamespace:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Translation\TranslationNamespace')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Translation\TranslationNamespace entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TranslationNamespaceType(), $entity);
        $editForm->bind($request);
        $translations = $this->get('request')->get('translations');
        
        
        
        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $this->setTranslations($entity->getNamespace(), 
                                   $entity->getBundle(),
                                    $translations);
            $em->flush();
            
            return $this->redirect($this->generateUrl('translation_edit', array('id' => $id)));
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Translation\TranslationNamespace entity.
     *
     * @Route("/{id}/delete", name="translation_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Translation\TranslationNamespace')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Translation\TranslationNamespace entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('translation'));
    }
    
    /**
     * 
     * @Route("/add_property/{property}", name="add_property", 
     * options={"expose"=true})
     * @Template()
     */
    public function addPropertyAction($property)
    {
        $langs = LanguageHelper::getLanguages();
        return array(
            "prop"  => $property,
            "langs" => $langs
        );
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    private function getTranslations($namespace, $bundle)
    {
        $parser     = new Parser();
        $fs         = new Filesystem();
        
        $kernel     = $this->get('kernel');
       try{
            $path       = 
                $kernel->locateResource("@{$bundle}/Resources/translations/");
        }
        catch(\Exception $e)
        {
            $this->get('session')->setFlash('warning', 
                                                $e->getMessage());
            return array(array(), array());
        }
        
        $translations = array();
        $langs = LanguageHelper::getLanguages();
        $allKeys = array();
        foreach($langs as $lang)
        {
            $fileName = $namespace.".".$lang.".yml";
            $translations[$lang] = array();
            if($fs->exists($path.$fileName))
            {
                $yaml = $parser->parse(file_get_contents($path.$fileName));
                if($yaml !== null){
                    $keys = array_keys($yaml);
                    foreach($keys as $key)
                    {
                        if(!in_array($key, $allKeys))
                        {
                            $allKeys[] = $key;
                        }
                    }
                }
                $translations[$lang] = $yaml;
            }
        }
        sort($allKeys);
        
        $sortedTranslations = array();
        foreach($translations as $lang => $trans)
        {
            $sortedTranslations[$lang] = array();
            foreach($allKeys as $key)
            {
                if(isset($trans[$key]))
                {
                    $sortedTranslations[$lang][$key] = $trans[$key];
                }
                else
                {
                    $sortedTranslations[$lang][$key] = "";
                }
            }
        }
        
        return array($sortedTranslations, $allKeys);
    }
    
    private function setTranslations($namespace, $bundle,  $translations)
    {
        $dumper     = new Dumper();
        $fs         = new Filesystem();
        $kernel     = $this->get('kernel');
        try{
            $path       = 
                $kernel->locateResource("@{$bundle}/Resources/translations/");
        }
        catch(\Exception $e)
        {
            $this->get('session')->setFlash('warning', 
                                                    $e->getMessage());
            return;
        }
        
        if(count($translations) > 0)
        {
            if(!$fs->exists($path))
            {
                $fs->mkdir($path);
            }
            foreach($translations as $lang => $trans )
            {
                $transWithouEmpty = array();
                foreach($trans as $key => $value)
                {
                    if($value != "")
                    {
                        $transWithouEmpty[$key] = $value;
                    }
                }
                if(count($transWithouEmpty) == 0)
                {
                    $yaml = null;
                }
                else
                {
                    $yaml = $dumper->dump($transWithouEmpty, 2);
                }
                
                $filePath = $path."{$namespace}.{$lang}.yml";
                if(!$fs->exists($filePath))
                {
                    $fs->touch($filePath);
                    $fs->chmod($filePath, 0777);
                }

                file_put_contents($filePath, $yaml);
            }
        }
    }
}
