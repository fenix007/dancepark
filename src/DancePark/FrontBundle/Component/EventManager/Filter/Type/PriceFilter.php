<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

class PriceFilter extends AbstractFilter
{
    protected $priceTo = null;
    protected $priceFrom = null;

    /**
     * Validate Request parameter data? if exists
     *
     * @param $data mixed
     * @return bool
     */
    public function validateParameter($data)
    {
        $include = false;

        if (isset($data['price_from'])
            && is_numeric($data['price_from'])) {
            $this->priceFrom = $data['price_from'];
            $include = true;
        }
        if (isset($data['price_to'])
            && is_numeric($data['price_to'])) {
            $this->priceTo = $data['price_to'];
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
        $queryBuilder->leftJoin("e.lessonPrices", 'lp', 'WITH');

        if ($this->priceTo !== null) {
            $queryBuilder->andWhere('((e.price IS NOT NULL AND e.price <= :price_to) OR (lp.id IS NOT NULL AND lp.price <= :price_to) OR (lp.id IS NULL AND e.price IS NULL))');
            $queryBuilder->setParameter('price_to', $this->priceTo);
        }

        if ($this->priceFrom !== null) {
            $queryBuilder->andWhere('((e.price IS NOT NULL AND e.price >= :price_from) OR (lp.id IS NOT NULL AND lp.price >= :price_from) OR (lp.id IS NULL AND e.price IS NULL))');
            $queryBuilder->setParameter('price_from', $this->priceFrom);
        }
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
            ->add('price_from', 'number', array(
                'label' => 'label.from',
                'required' => false,
            ))
            ->add('price_to', 'number', array(
                'label' => 'label.to',
                'required' => false
            ));
    }
}