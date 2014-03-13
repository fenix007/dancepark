<?php
namespace DancePark\CommonBundle\EventListener\Form;

use DancePark\CommonBundle\EventListener\Event\CommonEvents;
use DancePark\CommonBundle\EventListener\Event\EntityEvent;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DancerAutocompleteSubscriber implements  EventSubscriberInterface
{
    /** @var $em UserManager */
    protected  $um;

    /**
     * Set object defaults
     *
     * @param $em
     */
    public function __construct(UserManager $um)
    {
        $this->um = $um;
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            CommonEvents::PRE_PERSIST => 'preSave',
            CommonEvents::PRE_UPDATE => 'preSave'
        );
    }

    /**
     * Pre save callback get user by user email
     */
    public function preSave(EntityEvent $event)
    {
        $fields = $event->getCheckFields();
        $entity = $event->getEntity();
        foreach ($fields as $field) {
            $getter = 'get' . ucfirst($field);
            $setter = 'set' . ucfirst($field);
            if (method_exists($entity, $getter)) {
                $email = $entity->$getter();
                $user = $this->um->findUserByEmail($email);
                $entity->$setter($user);
            }
        }
    }
}