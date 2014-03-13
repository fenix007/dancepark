<?php

namespace DancePark\CommonBundle\Controller;

use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\CommonBundle\Entity\DanceType;
use DancePark\CommonBundle\Form\DanceTypeType;

/**
 * DanceType controller.
 *
 * @Route("/dance_type")
 */
class DanceTypeController extends Controller
{

    /**
     * Lists all DanceType entities.
     *
     * @Route("/", name="dance_type")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CommonBundle:DanceType')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new DanceType entity.
     *
     * @Route("/", name="dance_type_create")
     * @Method("POST")
     * @Template("CommonBundle:DanceType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new DanceType();
        $form = $this->createForm(new DanceTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('dance_type_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new DanceType entity.
     *
     * @Route("/new", name="dance_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new DanceType();
        $form   = $this->createForm(new DanceTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a DanceType entity.
     *
     * @Route("/{id}", name="dance_type_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:DanceType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DanceType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing DanceType entity.
     *
     * @Route("/{id}/edit", name="dance_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:DanceType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DanceType entity.');
        }

        $editForm = $this->createForm(new DanceTypeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing DanceType entity.
     *
     * @Route("/{id}", name="dance_type_update")
     * @Method("PUT")
     * @Template("CommonBundle:DanceType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:DanceType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DanceType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DanceTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('dance_type_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a DanceType entity.
     *
     * @Route("/{id}", name="dance_type_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CommonBundle:DanceType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DanceType entity.');
            }

            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch('dance_type.pre_remove', $entityEvent);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('dance_type'));
    }

    /**
     * Creates a form to delete a DanceType entity by id.
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
