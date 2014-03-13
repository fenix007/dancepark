<?php
namespace DancePark\CommonBundle\EventListener\Doctrine;

use DancePark\CommonBundle\Entity\Article;
use DancePark\DancerBundle\Entity\Digest;
use DancePark\FrontBundle\Component\DigestManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArticleEventSubscriber implements EventSubscriber
{
    protected $container;
    protected  $mailer;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $transport = \Swift_SmtpTransport::newInstance();

        $this->mailer = \Swift_Mailer::newInstance($transport);
    }


    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    function getSubscribedEvents()
    {
        return array(
            'prePersist' => 'postPersist',
            'postLoad' => 'postLoad'
        );
    }

    public function postLoad(LifecycleEventArgs $args) {
        if ($args->getEntity() instanceof Article) {
            $args->getEntity()->setBody(str_replace("\\", '', $args->getEntity()->getBody()));
        }
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Article) {
            $digestManager = new DigestManager($event->getEntityManager());
            $digests = $digestManager->getAllDigests($entity);
            foreach ($digests as $digest) {
                /** @var $digest Digest */
                $email = '';
                if ($digest->getEmail()) {
                    $email = $digest->getEmail();
                } else if($digest->getDancer()) {
                    $email = $digest->getDancer()->getEmail();
                }
                if ($email){
                    $digestManager->sendMailArticleTo($this->container, $email, $entity, $digest);
                }
            }
        }
    }
}