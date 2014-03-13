<?php

namespace DancePark\CommonBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\CommonBundle\Entity\AddressGroup;
use DancePark\CommonBundle\Form\AddressGroupType;

/**
 * AddressGroup controller.
 *
 * @Route("/address_group")
 */
class AddressGroupController extends Controller
{

    /**
     * Lists all AddressGroup entities.
     *
     * @Route("/", name="admin_address_group")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CommonBundle:AddressGroup')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new AddressGroup entity.
     *
     * @Route("/", name="admin_address_group_create")
     * @Method("POST")
     * @Template("CommonBundle:AddressGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new AddressGroup();
        $form = $this->createForm(new AddressGroupType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_address_group_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new AddressGroup entity.
     *
     * @Route("/new", name="admin_address_group_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AddressGroup();
        $form   = $this->createForm(new AddressGroupType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a AddressGroup entity.
     *
     * @Route("/{id}", name="admin_address_group_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:AddressGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing AddressGroup entity.
     *
     * @Route("/{id}/edit", name="admin_address_group_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:AddressGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressGroup entity.');
        }

        $editForm = $this->createForm(new AddressGroupType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing AddressGroup entity.
     *
     * @Route("/{id}", name="admin_address_group_update")
     * @Method("PUT")
     * @Template("CommonBundle:AddressGroup:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:AddressGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AddressGroupType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_address_group_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a AddressGroup entity.
     *
     * @Route("/{id}", name="admin_address_group_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CommonBundle:AddressGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AddressGroup entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_address_group'));
    }

    /**
     * Creates a form to delete a AddressGroup entity by id.
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
