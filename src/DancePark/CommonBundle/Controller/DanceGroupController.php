<?php

namespace DancePark\CommonBundle\Controller;

use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\CommonBundle\Entity\DanceGroup;
use DancePark\CommonBundle\Form\DanceGroupType;

/**
 * DanceGroup controller.
 *
 * @Route("/dance_group")
 */
class DanceGroupController extends Controller
{

    /**
     * Lists all DanceGroup entities.
     *
     * @Route("/", name="admin_dance_group")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CommonBundle:DanceGroup')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new DanceGroup entity.
     *
     * @Route("/", name="admin_dance_group_create")
     * @Method("POST")
     * @Template("CommonBundle:DanceGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new DanceGroup();
        $form = $this->createForm(new DanceGroupType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_dance_group_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new DanceGroup entity.
     *
     * @Route("/new", name="admin_dance_group_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new DanceGroup();
        $form   = $this->createForm(new DanceGroupType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a DanceGroup entity.
     *
     * @Route("/{id}", name="admin_dance_group_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:DanceGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DanceGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing DanceGroup entity.
     *
     * @Route("/{id}/edit", name="admin_dance_group_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:DanceGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DanceGroup entity.');
        }

        $editForm = $this->createForm(new DanceGroupType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing DanceGroup entity.
     *
     * @Route("/{id}", name="admin_dance_group_update")
     * @Method("PUT")
     * @Template("CommonBundle:DanceGroup:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:DanceGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DanceGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DanceGroupType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_dance_group_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a DanceGroup entity.
     *
     * @Route("/{id}", name="admin_dance_group_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CommonBundle:DanceGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DanceGroup entity.');
            }

            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch('dance_group.pre_remove', $entityEvent);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_dance_group'));
    }

    /**
     * Creates a form to delete a DanceGroup entity by id.
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
