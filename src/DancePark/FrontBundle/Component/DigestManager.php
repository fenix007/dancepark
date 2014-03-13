<?php
namespace DancePark\FrontBundle\Component;

use DancePark\CommonBundle\Entity\Article;
use DancePark\CommonBundle\Entity\DanceType;
use DancePark\DancerBundle\Entity\Digest;
use DancePark\EventBundle\Entity\DateRegularWeek;
use DancePark\EventBundle\Entity\Event;
use DancePark\FrontBundle\Component\EventManager\Filter\FilterInterface;
use DancePark\FrontBundle\Component\EventManager\Filter\Type\AbstractFilter;
use DancePark\FrontBundle\Component\EventManager\Filter\Type\DanceTypeFilter;
use DancePark\FrontBundle\Component\EventManager\Filter\Type\DateFilter;
use DancePark\FrontBundle\Component\EventManager\Filter\Type\EventTypeFilter;
use DancePark\FrontBundle\Component\EventManager\Filter\Type\TimeFilter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;

class DigestManager
{
    /** @var $manager EntityManager */
    protected $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    protected function applyFilters(QueryBuilder $qb, Digest $digest)
    {
        if ($digest->getDateFrom() || $digest->getDateTo()) {
            $dateFrom = null;
            $dateTo = null;

            if ($digest->getDateFrom()) {
                $dateFrom = $digest->getDateFrom();
            }
            if ($digest->getDateTo()) {
                $dateTo = $digest->getDateTo();
            }

            DateFilter::changeQueryBuilder($qb, $dateTo, $dateFrom);
        }

        if ($digest->getStartTime() || $digest->getEndTime()) {
            $startTime = null;
            $endTime = null;
            if ($digest->getStartTime()) {
                $startTime = $digest->getStartTime();
            }
            if ($digest->getEndTime()) {
                $endTime = $digest->getEndTime();
            }

            TimeFilter::changeQueryBuilder($qb, $endTime, $startTime);
        }

        if ($eventTypes = $digest->getEventType()) {
            if (count($eventTypes) > 0) {
                $typeIds = array();
                foreach($eventTypes as $type) {
                    $typeIds[] = $type->getId();
                }
                EventTypeFilter::changeQueryBuilder($qb, $typeIds);
            }
        }

        if ($danceTypes = $digest->getDanceType()) {
            if (count($danceTypes) > 0) {
                $typeIds = array();
                foreach($danceTypes as $type) {
                    $typeIds[] = $type->getId();
                }
                DanceTypeFilter::changeQueryBuilder($qb, $typeIds);
            }
        }
    }

    /**
     * Find all events
     */
    public function getAllEvents(Digest $digest, &$isNext, $page = 0)
    {
        $isNext = false;
        $qb = $this->manager->createQueryBuilder();
        $qb
            ->select('e')
            ->from('EventBundle:Event', 'e')
            ->leftJoin('EventBundle:EventClosing', 'ec', 'WITH', 'ec.event = e.id');

        $this->applyFilters($qb, $digest);

        $results = $qb
            ->setMaxResults(FilterInterface::PAGE_COUNT_RESULT + 1)
            ->setFirstResult($page * FilterInterface::PAGE_COUNT_RESULT)
            ->getQuery()
            ->getResult();
        if (count($results) > FilterInterface::PAGE_COUNT_RESULT) {
            $isNext = true;
        }
        return $results;
    }

    public function getAllDigests(Article $article) {
        $qb = $this->manager->createQueryBuilder();
        $qb ->select('d')
            ->from('DancerBundle:Digest', 'd')
            ->leftJoin('d.danceType', 'dt' , 'WITH')
            ->leftJoin('d.eventType', 'et', 'WITH')
            ->leftJoin('d.event', 'e', 'WITH')
            ->leftJoin('d.place', 'p', 'WITH');

        if ($danceType = $article->getDanceType()) {
            $qb->andWhere('(dt.id IS NULL OR dt.id = :dance_type)');
            $qb->setParameter('dance_type', $article->getDanceType()->getId());
        }
        if ($organization = $article->getOrganization()) {
            $qb->andWhere('(d.organization IS NULL OR d.organization = :organization)');
            $qb->setParameter('organization', $organization->getId());
        }
        if ($place = $article->getPlace()) {
            $qb->andWhere('(p.id IS NULL OR p.id = :place)');
            $qb->setParameter('place', $place->getId());
        }
        if ($event = $article->getEvent()) {
            $qb->andWhere('(e.id IS NULL OR e.id = :event)');
            $qb->setParameter(':event', $event->getId());

            if (!$place) {
                $this->filterByEvent('', $event, $qb);
            } else {
                $this->filterByEvent($place->getAddress(), $event, $qb);
            }
        }

        $results = $qb->getQuery();
        $results = $results->getResult();
        return $results;
    }

    /**
     * Post load check digests
     */
    public function postLoadCheckDigests($digests, Article $article)
    {
        $event = $article->getEvent();
        $resultArray = array();
        if (is_null($event)) {
            return $resultArray;
        }
        $resultArray = $this->checkDigestsByEvent($digests, $event, $resultArray);
        return $resultArray;
    }

    public function timeCompare(\DateTime $a, \DateTime $b) {
        $date1 = (int)($a->format('Hi'));
        $date2 = (int)($b->format('Hi'));
        return $date1 > $date2 ? 1 : ($date2 > $date1 ? -1 : 0);
    }

    public function inEntityArray($arrayEntities, $id)
    {
        foreach($arrayEntities as $entity) {
            if (is_object($entity) && method_exists($entity, 'getId')) {
                if ($entity->getId() == $id) {
                    return true;
                }
            } else {
                throw new \RuntimeException('Incorrect entity pass to inEntityArray function');
            }
        }
        return false;
    }

    /**
     * @param $event Event
     */
    public function findByAddressLike($pattern = '', $event)
    {
        $result = $this->manager->createQueryBuilder()
            ->select('d')
            ->from('DancerBundle:Digest', 'd');

        $this->filterByEvent($pattern, $event, $result);

        $result = $result->getQuery()
            ->getResult();
        return $result;
    }

    public function sendMailArticleTo($container, $email, $article, $digest)
    {
        $headers   = array();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        $headers[] = 'X-Mailer: PHP/'.phpversion();
        $headers[] = 'From: DancePark@gmail.com';
        $status = mail(
            $email,
            'Новое событие DancePark',
            $container
                ->get('templating')
                ->render(
                    'CommonBundle:Mail:article_mail.txt.twig',
                    array('article' => $article, 'digest' => $digest)
                ),
            implode($headers, "\r\n")
        );
    }



    public function sendMailEventTo($container, $email, $event, $digest)
    {
        $headers   = array();
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        $headers[] = 'X-Mailer: PHP/'.phpversion();
        $headers[] = 'From: DancePark@gmail.com';
        $status = mail(
            $email,
            'Новое событие DancePark',
            $container
                ->get('templating')
                ->render(
                    'CommonBundle:Mail:event_mail.txt.twig',
                    array('event' => $event, 'digest' => $digest)
                ),
            implode($headers, "\r\n")
        );
    }

    /**
     * @param $pattern
     * @param $event
     * @param $queryBuilder
     */
    protected function filterByEvent($pattern, $event, $queryBuilder)
    {
        if (strlen($pattern) > 0) {
            $queryBuilder
                ->where(":pattern LIKE CONCAT(CONCAT('%', d.address), '%')")
                ->setParameter('pattern', $pattern);
        }

        $danceTypes = $event->getDanceType();
        if (count($danceTypes)) {
            $typeIds = array();
            foreach ($danceTypes as $type) {
                /** @var $type DanceType */
                $typeIds[] = $type->getId();
            }
            if (!AbstractFilter::checkJoin($queryBuilder, 'dt')) {
                $queryBuilder->leftJoin('d.danceType', 'dt', 'WITH');
            }
            $queryBuilder->andWhere('dt.id IS NULL OR dt.id IN (:dance_types)');
            $queryBuilder->setParameter('dance_types', implode(',', $typeIds));
        }

        if ($eventType = $event->getType() != null) {
            if (!AbstractFilter::checkJoin($queryBuilder, 'et')) {
                $queryBuilder->leftJoin('d.eventType', 'et', 'WITH');
            }
            $queryBuilder->andWhere('(et.id IS NULL OR et.id = :event_type)');
            $queryBuilder->setParameter('event_type', $eventType);
        }

        if ($event->getKind() == $event::EVENT_KIND_SINGLE) {
            if ($date = $event->getDate() != null) {
                $queryBuilder->andWhere('(d.dateFrom IS NULL OR d.dateFrom < :date) AND (d.dateTo IS NULL OR d.dateTo > :date)');
                $queryBuilder->setParameter('date', $date);
            }
            $timeFrom = $event->getStartTime();
            if ($timeFrom->format('His') != 0) {
                $queryBuilder->andWhere('(d.startTime IS NULL OR d.startTime < :time_from) AND (d.endTime IS NULL OR d.endTime > :time_from)');
                $queryBuilder->setParameter('time_from', $timeFrom);
            }
            $timeTo = $event->getEndTime();
            if ($timeTo->format('His') != 0) {
                $queryBuilder->andWhere('(d.startTime IS NULL OR d.startTime < :time_to) AND (d.endTime IS NULL OR d.endTime > :time_to)');
                $queryBuilder->setParameter('time_to', $timeTo);
            }
        } else {
            $orDate = $queryBuilder->expr()->orX();
            $dateDef = false;
            $orTime = $queryBuilder->expr()->orX();
            $timeDef = false;

            foreach ($event->getDateRegular() as $regular) {
                /** @var $regular DateRegularWeek */
                $date = $regular->getDate();
                if ($date != null) {
                    if (!$dateDef) {
                        $dateDef = true;
                    }
                    $orDate->add('(d.dateFrom IS NULL OR d.dateFrom < :date_' . $regular->getId() . ') AND (d.dateTo IS NULL OR d.dateTo > :date_' . $regular->getId() . ')');
                    $queryBuilder->setParameter('date_' . $regular->getId(), $date);
                }
                $timeFrom = $regular->getStartTime();
                if ($timeFrom->format('His') != 0) {
                    if (!$timeDef) {
                        $timeDef = true;
                    }
                    $orTime->add('(d.startTime IS NULL OR d.startTime < :time_from_' . $regular->getId() . ') AND (d.endTime IS NULL OR d.endTime > :time_from_' . $regular->getId() . ')');
                    $queryBuilder->setParameter('time_from_' . $regular->getId(), $timeFrom);
                }
                $timeTo = $regular->getEndTime();
                if ($timeTo->format('His') != 0) {
                    if (!$timeDef) {
                        $timeDef = true;
                    }
                    $orTime->add('(d.startTime IS NULL OR d.startTime < :time_to_' . $regular->getId() . ') AND (d.endTime IS NULL OR d.endTime > :time_to_' . $regular->getId() . ')');
                    $queryBuilder->setParameter('time_to_' . $regular->getId(), $timeTo);
                }

                if ($dateDef) {
                    $queryBuilder->andWhere($orDate);
                }
                if ($timeDef) {
                    $queryBuilder->andWhere($orTime);
                }
            }
        }
    }
}