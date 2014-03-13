<?php

namespace DancePark\CommonBundle\Controller;

use DancePark\CommonBundle\EventListener\Event\CommonEvents;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\CommonBundle\Entity\AddressLevel;
use DancePark\CommonBundle\Form\AddressLevelType;

/**
 * AddressLevel controller.
 *
 * @Route("/address_level")
 */
class AddressLevelController extends Controller
{

    /**
     * Lists all AddressLevel entities.
     *
     * @Route("/", name="admin_address_level")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CommonBundle:AddressLevel')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new AddressLevel entity.
     *
     * @Route("/", name="admin_address_level_create")
     * @Method("POST")
     * @Template("CommonBundle:AddressLevel:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new AddressLevel();
        $form = $this->createForm(new AddressLevelType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_address_level_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new AddressLevel entity.
     *
     * @Route("/new", name="admin_address_level_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AddressLevel();
        $form   = $this->createForm(new AddressLevelType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a AddressLevel entity.
     *
     * @Route("/{id}", name="admin_address_level_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:AddressLevel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressLevel entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing AddressLevel entity.
     *
     * @Route("/{id}/edit", name="admin_address_level_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:AddressLevel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressLevel entity.');
        }

        $editForm = $this->createForm(new AddressLevelType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing AddressLevel entity.
     *
     * @Route("/{id}", name="admin_address_level_update")
     * @Method("PUT")
     * @Template("CommonBundle:AddressLevel:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:AddressLevel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressLevel entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AddressLevelType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_address_level_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a AddressLevel entity.
     *
     * @Route("/{id}", name="admin_address_level_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CommonBundle:AddressLevel')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AddressLevel entity.');
            }

            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch(CommonEvents::ADDRESS_LEVEL_PRE_REMOVE, $entityEvent);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_address_level'));
    }

    /**
     * Creates a form to delete a AddressLevel entity by id.
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
