<?php

namespace DancePark\CommonBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DancePark\CommonBundle\Entity\AddressRegion;
use DancePark\CommonBundle\Form\AddressRegionType;

/**
 * AddressRegion controller.
 *
 * @Route("/address_region")
 */
class AddressRegionController extends Controller
{

    /**
     * Lists all AddressRegion entities.
     *
     * @Route("/", name="admin_address_region")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CommonBundle:AddressRegion')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new AddressRegion entity.
     *
     * @Route("/", name="admin_address_region_create")
     * @Method("POST")
     * @Template("CommonBundle:AddressRegion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new AddressRegion();
        $form = $this->createForm(new AddressRegionType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_address_region_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new AddressRegion entity.
     *
     * @Route("/new", name="admin_address_region_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AddressRegion();
        $form   = $this->createForm(new AddressRegionType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a AddressRegion entity.
     *
     * @Route("/{id}", name="admin_address_region_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:AddressRegion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressRegion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing AddressRegion entity.
     *
     * @Route("/{id}/edit", name="admin_address_region_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:AddressRegion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressRegion entity.');
        }

        $editForm = $this->createForm(new AddressRegionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing AddressRegion entity.
     *
     * @Route("/{id}", name="admin_address_region_update")
     * @Method("PUT")
     * @Template("CommonBundle:AddressRegion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CommonBundle:AddressRegion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressRegion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AddressRegionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_address_region_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a AddressRegion entity.
     *
     * @Route("/{id}", name="admin_address_region_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CommonBundle:AddressRegion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AddressRegion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_address_region'));
    }

    /**
     * Creates a form to delete a AddressRegion entity by id.
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
