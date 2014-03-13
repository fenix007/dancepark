<?php

namespace DancePark\EventBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\EventBundle\Entity\EventClosing;
use DancePark\EventBundle\Form\EventClosingType;

/**
 * EventClosing controller.
 *
 * @Route("/event_closing")
 */
class EventClosingController extends Controller
{

    /**
     * Lists all EventClosing entities.
     *
     * @Route("/", name="admin_event_closing")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EventBundle:EventClosing')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new EventClosing entity.
     *
     * @Route("/", name="admin_event_closing_create")
     * @Method("POST")
     * @Template("EventBundle:EventClosing:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new EventClosing();
        $form = $this->createForm(new EventClosingType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_closing_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new EventClosing entity.
     *
     * @Route("/new", name="admin_event_closing_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EventClosing();
        $form   = $this->createForm(new EventClosingType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a EventClosing entity.
     *
     * @Route("/{id}", name="admin_event_closing_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:EventClosing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventClosing entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing EventClosing entity.
     *
     * @Route("/{id}/edit", name="admin_event_closing_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:EventClosing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventClosing entity.');
        }

        $editForm = $this->createForm(new EventClosingType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing EventClosing entity.
     *
     * @Route("/{id}", name="admin_event_closing_update")
     * @Method("PUT")
     * @Template("EventBundle:EventClosing:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:EventClosing')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventClosing entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EventClosingType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event_closing_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a EventClosing entity.
     *
     * @Route("/{id}", name="admin_event_closing_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EventBundle:EventClosing')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EventClosing entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_event_closing'));
    }

    /**
     * Creates a form to delete a EventClosing entity by id.
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
