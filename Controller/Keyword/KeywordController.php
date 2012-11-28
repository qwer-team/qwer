<?php

namespace Itc\AdminBundle\Controller\Keyword;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\Keyword\Keyword;
use Itc\AdminBundle\Form\Keyword\KeywordType;

/**
 * Keyword\Keyword controller.
 *
 * @Route("/keyword")
 */
class KeywordController extends Controller
{
    /**
     * Lists all Keyword\Keyword entities.
     *
     * @Route("/", name="keyword")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ItcAdminBundle:Keyword\Keyword')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Keyword\Keyword entity.
     *
     * @Route("/{id}/show", name="keyword_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Keyword\Keyword')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Keyword\Keyword entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Keyword\Keyword entity.
     *
     * @Route("/new", name="keyword_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Keyword();
        $form   = $this->createForm(new KeywordType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Keyword\Keyword entity.
     *
     * @Route("/create", name="keyword_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:Keyword\Keyword:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Keyword();
        $form = $this->createForm(new KeywordType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('keyword_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Keyword\Keyword entity.
     *
     * @Route("/{id}/edit", name="keyword_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Keyword\Keyword')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Keyword\Keyword entity.');
        }

        $editForm = $this->createForm(new KeywordType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Keyword\Keyword entity.
     *
     * @Route("/{id}/update", name="keyword_update")
     * @Method("POST")
     * @Template("ItcAdminBundle:Keyword\Keyword:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:Keyword\Keyword')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Keyword\Keyword entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new KeywordType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('keyword_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Keyword\Keyword entity.
     *
     * @Route("/{id}/delete", name="keyword_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcAdminBundle:Keyword\Keyword')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Keyword\Keyword entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('keyword'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
