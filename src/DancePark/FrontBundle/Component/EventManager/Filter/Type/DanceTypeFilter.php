<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use DancePark\CommonBundle\Entity\DanceType;
use DancePark\FrontBundle\Component\EventManager\EventManager;
use DancePark\FrontBundle\Component\EventManager\Filter\FilterInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

class DanceTypeFilter extends AbstractFilter
{
    protected $types;

    /**
     * @param QueryBuilder $queryBuilder
     * @param $types
     */
    public static function changeQueryBuilder(QueryBuilder $queryBuilder, $types)
    {
        $queryBuilder->leftJoin("e.danceType", 'dt', 'WITH');
        $queryBuilder->andWhere('dt.id IN (:dance_types)');
        $queryBuilder->setParameter('dance_types', $types);
    }

    /**
     * Validate Request parameter data? if exists
     *
     * @param $data mixed
     * @return bool
     */
    public function validateParameter($data)
    {
        if (isset($data['dance_types']) && is_array($data['dance_types']) && !empty($data['dance_types'])) {
            $this->types = $data['dance_types'];
            return true;
        }
        return false;
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
        $types = $this->types;

        self::changeQueryBuilder($queryBuilder, $types);
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
        $danceTypesRepo = $this->em->getRepository('CommonBundle:DanceType');

        $danceTypesOptions = array();
        $danceTypes = $danceTypesRepo->findAll();

        foreach ($danceTypes as $eType) {
            /** @var $eType DanceType */
            $danceTypesOptions[$eType->getKindLabel()][$eType->getId()] = $eType->getName() ;
        }

        $formBuilder
            ->add('dance_types', 'choice', array(
                'label' => 'label.dance_types',
                'choices' => $danceTypesOptions,
                'multiple' => true,
                'expanded' => true
            ));
    }
}