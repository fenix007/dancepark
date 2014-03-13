<?php
namespace DancePark\FrontBundle\EventListener\Form;

use DancePark\DancerBundle\Entity\Dancer;
use DancePark\DancerBundle\Entity\DancerDanceType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DancerProfileSubscriber implements EventSubscriberInterface
{
    /** @var $em EntityManager */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_BIND => 'postBind',
            FormEvents::PRE_BIND => 'preBind'
        );
    }

    public function preBind(FormEvent $eventData)
    {
        $data = $eventData->getData();

        $hiddenDanceTypes = $data['danceTypesHidden'];

        $hiddenDanceTypes = json_decode(stripslashes($hiddenDanceTypes));
        $danceTypes = array();
        foreach($hiddenDanceTypes as $info) {
            $danceTypes[$info->id] = $info->id;
        }
        $data['danceTypesHidden'] = stripslashes($data['danceTypesHidden']);
        $data['pro_info'] = stripslashes($data['pro_info']);
        $data['danceType'] = array_keys($danceTypes);
        $eventData->setData($data);
    }

    public function postBind(FormEvent $eventData)
    {
        /** @var $data Dancer */
        $data = $eventData->getData();

        $form = $eventData->getForm();
        $formData = $form->getData();
        $danceTypeIds = array();

        $hiddenDanceTypes = $form->get('danceTypesHidden')->getData();
        $isProInfo = $form->get('pro_info')->getData();
        $isProInfo = json_decode(stripslashes($isProInfo));
        $hiddenDanceTypes = json_decode(stripslashes($hiddenDanceTypes));
        if (count($hiddenDanceTypes) > 0) {
            $typesInfo = $hiddenDanceTypes;
            $hiddenDanceTypes = array();
            $proInfo = array();
            foreach($typesInfo as $info) {
                $hiddenDanceTypes[] = $info->id;
                $proInfo[] = $info->pro;
            }
            $hiddenDanceTypes = $this->em->getRepository('CommonBundle:DanceType')->findby(array('id' => $hiddenDanceTypes));

            /** @var $data Dancer */
            $ddts = array();
            foreach($hiddenDanceTypes as $id => $danceType) {
                $danceTypeIds[] = $danceType->getId();
                $dancerDanceType = new DancerDanceType();
                $dancerDanceType->setDancer($data);
                $dancerDanceType->setDanceType($danceType);
                if (isset($isProInfo->{$danceType->getId()}) && $isProInfo->{$danceType->getId()}) {
                    $dancerDanceType->setIsPro(true);
                } else {
                    $dancerDanceType->setIsPro(false);
                }
                $ddts[] = $dancerDanceType;
            }
            $formData->setDanceType($ddts);
        }
        if ($formData->getId()) {
            $ddtRepo = $this->em->getRepository('DancerBundle:DancerDanceType');
            $forRemove = $ddtRepo->getTypesForEntityExcludeListed($data->getId(), array());
            foreach ($forRemove as $entity) {
                $this->em->remove($entity);
            }
            $this->em->flush();
        }
    }
}