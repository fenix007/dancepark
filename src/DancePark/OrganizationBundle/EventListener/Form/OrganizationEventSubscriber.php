<?php
namespace DancePark\OrganizationBundle\EventListener\Form;

use DancePark\OrganizationBundle\Entity\DateRegularWeek;
use DancePark\OrganizationBundle\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class OrganizationEventSubscriber implements EventSubscriberInterface
{
    protected $manager;

    public function __construct(Registry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    /**
     * {@innheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_BIND => 'postBind',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function postBind(FormEvent $event)
    {
        $form = $event->getForm();

        /** @var $data Organization */
        $data = $form->getData();

        /**@var $dateRegular DateRegularWeek*/
        if (null != $data->getDateRegular()) {
            foreach ($data->getDateRegular() as $dateRegular) {
                if (!$dateRegular->getId()) {
                    $dateRegular->setOrganization($data);
                    $this->manager->persist($dateRegular);
                }
            }
        }

        if ($form->has('oldLogo')) {
            $logo = $data->getLogo();
            if ($logo == null) {
                $logo = $form->get('oldLogo')->getData();
                $data->setLogo($logo);
            }
        }
    }
}