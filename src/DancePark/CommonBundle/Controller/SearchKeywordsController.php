<?php

namespace DancePark\CommonBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\CommonBundle\Entity\SearchKeywords;
use DancePark\CommonBundle\Form\SearchKeywordsType;

/**
 * SearchKeywords controller.
 *
 * @Route("/admin/search_keywords")
 */
class SearchKeywordsController extends Controller
{

    /**
     * Lists all SearchKeywords entities.
     *
     * @Route("/", name="search_keywords")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CommonBundle:SearchKeywords')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new SearchKeywords entity.
     *
     * @Route("/", name="search_keywords_create")
     * @Method("POST")
     * @Template("CommonBundle:SearchKeywords:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new SearchKeywords();
        $form = $this->createForm(new SearchKeywordsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('search_keywords_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new SearchKeywords entity.
     *
     * @Route("/new", name="search_keywords_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new SearchKeywords();
        $form   = $this->createForm(new SearchKeywordsType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a SearchKeywords entity.
     *
     * @Route("/{id}", name="search_keywords_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:SearchKeywords')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SearchKeywords entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing SearchKeywords entity.
     *
     * @Route("/{id}/edit", name="search_keywords_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:SearchKeywords')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SearchKeywords entity.');
        }

        $editForm = $this->createForm(new SearchKeywordsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing SearchKeywords entity.
     *
     * @Route("/{id}", name="search_keywords_update")
     * @Method("PUT")
     * @Template("CommonBundle:SearchKeywords:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:SearchKeywords')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SearchKeywords entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SearchKeywordsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('search_keywords_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a SearchKeywords entity.
     *
     * @Route("/{id}", name="search_keywords_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CommonBundle:SearchKeywords')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SearchKeywords entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('search_keywords'));
    }

    /**
     * Creates a form to delete a SearchKeywords entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
