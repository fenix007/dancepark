<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use DancePark\EventBundle\Entity\Event;
use DancePark\OrganizationBundle\Entity\DateRegularWeek;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

class TimeFilter extends AbstractFilter
{

    protected $timeFrom = null;
    protected $timeTo = null;

    protected $dateRegular = null;

    /**
     * Validate Request parameter data? if exists
     *
     * @param $data mixed
     * @return bool
     */
    public function validateParameter($data)
    {
        $include = false;

        if (isset($data['time_from']) && is_string($data['time_from'])) {
            if (strlen($data['time_from']) == 5) {
                $data['time_from'] = \DateTime::createFromFormat('G:i', $data['time_from']);
            } else {
                $data['time_from'] = \DateTime::createFromFormat('H_i_s', $data['time_from']);
            }
        }
        if (isset($data['time_to']) && is_string($data['time_to'])) {
            if (strlen($data['time_to']) == 5) {
                $data['time_to'] = \DateTime::createFromFormat('G:i', $data['time_to']);
            } else {
                $data['time_to'] = \DateTime::createFromFormat('H_i_s', $data['time_to']);
            }
        }

        if (isset($data['time_from'])
            && is_object($data['time_from'])
            && $data['time_from'] instanceof \DateTime
        ){
            $this->timeFrom = $data['time_from'];
            $include = true;
        }
        if (isset($data['time_to'])
            && is_object($data['time_to'])
            && $data['time_to'] instanceof \DateTime
        ) {
            $this->timeTo = $data['time_to'];
            $include = true;
        }
        return $include;
    }

    /**
     * Provider filter action
     *
     * @param QueryBuilder $queryBuilder
     * @param mixed $data
     * @param Request $request
     *
     * @return mixed
     */
    public function applyFilter(QueryBuilder $queryBuilder, $data, Request $request = null)
    {
        $timeFrom = $this->timeFrom;
        $timeTo = $this->timeTo;
        $this->changeQueryBuilder($queryBuilder, $this->em, $timeTo, $timeFrom, $this->dateRegular);
    }

    /**
     * Add filter widget to the form while form building
     *
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return Form
     */
    public function editFilterForm(FormBuilder $formBuilder, Request $request = null)
    {
        $formBuilder
            ->add('time', 'text', array(
                'label' => 'label.time',
                'required' => false,
            ))
            ->add('time_from', 'text', array(
                'label' => 'label.from',
                'required' => false
            ))
            ->add('time_to', 'text', array(
                'label' => 'label.to',
                'required' => false,
            ));
    }

    public function filterDateRegular(QueryBuilder $qb, EntityManager $em, &$first = true)
    {
        if ($first) {
            $this->dateRegular = $qb;
        }
        $first = false;

        if ($this->timeFrom) {
            $qb->setParameter('time_from', $this->timeFrom->format('G:i'))
                ->andWhere('date.startTime > :time_from')
                ->andWhere('date.endTime > :time_from');
        }
        if ($this->timeTo) {
            $qb->setParameter('time_to', $this->timeTo->format('G:i'))
                ->andWhere('date.endTime < :time_to')
                ->andWhere('date.startTime < :time_to');
        }
    }
    /**
     * @param QueryBuilder $queryBuilder
     * @param $timeTo
     * @param $timeFrom
     */
    public static function changeQueryBuilder(QueryBuilder $queryBuilder, EntityManager $em, $timeTo = null, $timeFrom = null, $dateRegular = null)
    {
        $joined = static::checkJoin($queryBuilder, 'dr');
        if (!$joined) {
            $queryBuilder->leftJoin('e.dateRegular', 'dr', 'WITH');
        }
        if ($timeFrom && $timeTo) {
            $or = $queryBuilder->expr()->orX();
            $or->add('e.kind = :kind_1 AND :time_from <= e.startTime AND e.endTime <= :time_to');
            if ($dateRegular) {
                $and = $queryBuilder->expr()->andX();
                $and->add('e.kind = :kind_2');
                $and->add($queryBuilder->expr()->in(
                    'dr.id',
                    $dateRegular
                        ->getDQL()
                ));
                $or->add($and);
            } else {
                $or->add('e.kind = :kind_2');
            }
            $queryBuilder->andWhere($or);
            $queryBuilder->setParameter('time_from', $timeFrom->format('G:i'));
            $queryBuilder->setParameter('time_to', $timeTo->format('G:i'));
        } else {
            if ($timeTo) {
                $queryBuilder->andWhere('((e.kind = :kind_1 AND e.endTime <= CAST(:time_to AS TIME)) OR (e.kind = :kind_2 AND dr.endTime <= :time_to))');
                $queryBuilder->setParameter('time_to', $timeTo->format('G:i'));
            }

            if ($timeFrom) {
                $queryBuilder->andWhere('((e.kind = :kind_1 AND e.startTime > CAST(:time_from AS TIME)) OR (e.kind = :kind_2 AND dr.startTime > :time_from))');
                $queryBuilder->setParameter('time_from', $timeFrom->format('G:i'));
            }
        }
        $queryBuilder->setParameter('kind_1', Event::EVENT_KIND_SINGLE);
        $queryBuilder->setParameter('kind_2', Event::EVENT_KIND_REGULAR);
    }
}