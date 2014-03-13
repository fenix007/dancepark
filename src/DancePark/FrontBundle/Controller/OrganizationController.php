<?php
namespace DancePark\FrontBundle\Controller;

use DancePark\FrontBundle\EventListener\Form\FeedbackEventSubscriber;
use DancePark\OrganizationBundle\Entity\Organization;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use DancePark\FrontBundle\Form\Type\FeedbackType;

/**
 * Class OrganizationController
 * @package DancePark\FrontBundle\Controller
 * @Route("/org")
 */
class OrganizationController extends Controller
{
    /**
     * @Route("/{id}", name="organization_view")
     * @ParamConverter("organization", class="OrganizationBundle:Organization")
     * @Template()
     * @param Organization $organization
     */
    public function viewAction(Request $request, Organization $organization)
    {
        $form = null;
        if ($this->getUser()) {
            $form = $this->createForm(new FeedbackType(new FeedbackEventSubscriber(
                $this->get('doctrine'),
                $this->getUser(),
                $organization,
                'organization'
            )));

            if ($request->getMethod() == 'POST') {
                $form->bind($request);

                if ($form->isValid()) {
                    $manager = $this->get('doctrine')->getManager();
                    $manager->persist($form->getData());
                    $manager->flush();

                    $form = $this->createForm(new FeedbackType(new FeedbackEventSubscriber(
                        $this->get('doctrine'),
                        $this->getUser(),
                        $organization,
                        'organization'
                    )));
                }
            }
            $form = $form->createView();
        }

        return array(
            'organization' => $organization,
            'feedback_form' => $form,
            'user' => $this->getUser(),
        );
    }
}