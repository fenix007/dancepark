<?php

namespace DancePark\DancerBundle\Controller;

use DancePark\CommonBundle\EventListener\Event\CommonEvents;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\DancerBundle\Entity\Digest;
use DancePark\DancerBundle\Form\DigestType;

/**
 * Digest controller.
 *
 * @Route("/digest")
 */
class DigestController extends Controller
{

    /**
     * Lists all Digest entities.
     *
     * @Route("/", name="admin_digest")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DancerBundle:Digest')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Digest entity.
     *
     * @Route("/", name="admin_digest_create")
     * @Method("POST")
     * @Template("DancerBundle:Digest:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Digest();
        $form = $this->createForm(new DigestType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setCheckFields(array('dancer'));
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch(CommonEvents::PRE_PERSIST, $entityEvent);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_digest_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Digest entity.
     *
     * @Route("/new", name="admin_digest_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Digest();
        $form   = $this->createForm(new DigestType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Digest entity.
     *
     * @Route("/{id}", name="admin_digest_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:Digest')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Digest entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Digest entity.
     *
     * @Route("/{id}/edit", name="admin_digest_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:Digest')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Digest entity.');
        }

        $editForm = $this->createForm(new DigestType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Digest entity.
     *
     * @Route("/{id}", name="admin_digest_update")
     * @Method("PUT")
     * @Template("DancerBundle:Digest:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DancerBundle:Digest')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Digest entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DigestType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            /** @var $eventDispatcher ContainerAwareEventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $entityEvent = new EntityEvent();
            $entityEvent->setEntity($entity);
            $entityEvent->setCheckFields(array('dancer'));
            $entityEvent->setDispatcher($eventDispatcher);

            $eventDispatcher->dispatch(CommonEvents::PRE_PERSIST, $entityEvent);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_digest_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Digest entity.
     *
     * @Route("/{id}", name="admin_digest_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DancerBundle:Digest')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Digest entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_digest'));
    }

    /**
     * Creates a form to delete a Digest entity by id.
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
