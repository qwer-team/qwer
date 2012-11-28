<?php

namespace Itc\AdminBundle\Controller\Units;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\Units\Units;
use Itc\AdminBundle\Form\Units\UnitsType;
use Itc\AdminBundle\Tools\LanguageHelper;
use Itc\AdminBundle\Tools\TranslitGenerator;

/**
 * Units\Units controller.
 *
 * @Route("/units")
 */
class UnitsController extends Controller
{
    /**
     * Lists all Units\Units entities.
     *
     * @Route("/", name="units")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $deleteForm = array();
        $repo = $em->getRepository('ItcAdminBundle:Units\Units');
        
        $qb = $repo->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale);
        $entities = $qb->getQuery()->execute();
                
        foreach ($entities as $entity)
            $deleteForm[$entity->getId()] = $this->createDeleteForm($entity->getId())
                            ->createView();

        return array(
            'entities' => $entities,
            'locale' => $locale,
            'delete_form' => $deleteForm,
        );
    }

    /**
     * Finds and displays a Units\Units entity.
     *
     * @Route("/{id}/show", name="units_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Units\Units')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Units\Units entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Units\Units entity.
     *
     * @Route("/new", name="units_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Units();
        $languages = $this->getLanguages();
        $locale =  LanguageHelper::getLocale();
        
        $form   = $this->createForm(new UnitsType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'languages' => $languages,
            'locale' => $locale,
        );
    }

    /**
     * Creates a new Units\Units entity.
     *
     * @Route("/create", name="units_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:Units\Units:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Units();
        $form = $this->createForm(new UnitsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('units'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Units\Units entity.
     *
     * @Route("/{id}/edit", name="units_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $this->getLanguages();
        $locale =  LanguageHelper::getLocale();

        $entity = $em->getRepository('ItcAdminBundle:Units\Units')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Units\Units entity.');
        }

        $editForm = $this->createForm(new UnitsType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'languages' => $languages,
            'locale' => $locale,
        );
    }

    /**
     * Edits an existing Units\Units entity.
     *
     * @Route("/{id}/update", name="units_update")
     * @Method("POST")
     * @Template("ItcAdminBundle:Units\Units:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Units\Units')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Units\Units entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UnitsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('units_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Units\Units entity.
     *
     * @Route("/{id}/delete", name="units_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Units\Units')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Units\Units entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('units'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    protected function getLanguages(){
        return LanguageHelper::getLanguages();
    }
    
}
