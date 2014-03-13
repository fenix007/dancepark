<?php

namespace DancePark\DancerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\DancerBundle\Entity\DancerEvent;
use DancePark\DancerBundle\Form\DancerEventType;

/**
 * DancerEvent controller.
 *
 * @Route("/dancer_event")
 */
class DancerEventController extends Controller
{

    /**
     * Lists all DancerEvent entities.
     *
     * @Route("/", name="admin_dancer_event")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DancerBundle:DancerEvent')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new DancerEvent entity.
     *
     * @Route("/", name="admin_dancer_event_create")
     * @Method("POST")
     * @Template("DancerBundle:DancerEvent:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new DancerEvent();
        $form = $this->createForm(new DancerEventType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_dancer_event_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new DancerEvent entity.
     *
     * @Route("/new", name="admin_dancer_event_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new DancerEvent();
        $form   = $this->createForm(new DancerEventType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a DancerEvent entity.
     *
     * @Route("/{id}", name="admin_dancer_event_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:DancerEvent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DancerEvent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing DancerEvent entity.
     *
     * @Route("/{id}/edit", name="admin_dancer_event_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:DancerEvent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DancerEvent entity.');
        }

        $editForm = $this->createForm(new DancerEventType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing DancerEvent entity.
     *
     * @Route("/{id}", name="admin_dancer_event_update")
     * @Method("PUT")
     * @Template("DancerBundle:DancerEvent:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:DancerEvent')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DancerEvent entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DancerEventType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_dancer_event_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a DancerEvent entity.
     *
     * @Route("/{id}", name="admin_dancer_event_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DancerBundle:DancerEvent')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DancerEvent entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_dancer_event'));
    }

    /**
     * Creates a form to delete a DancerEvent entity by id.
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
