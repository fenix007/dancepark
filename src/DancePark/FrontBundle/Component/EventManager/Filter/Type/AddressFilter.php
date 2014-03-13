<?php
namespace DancePark\FrontBundle\Component\EventManager\Filter\Type;

use DancePark\CommonBundle\Entity\MetroStation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;

class AddressFilter extends AbstractFilter
{
    protected $addrInfo;
    protected $bounds;
    protected $stationIds;
    /**
     * {@inheritDoc}
     */
    public function validateParameter($data)
    {
        return isset($data['address']) && is_string($data['address']);
    }

    /**
     * {@inheritDoc}
     */
    public function applyFilter(QueryBuilder $queryBuilder, $data, Request $request = null)
    {
        $joined = static::checkJoin($queryBuilder, 'p');
        if (!$joined) {
            $queryBuilder->innerJoin('e.place', 'p', 'WITH');
        }

        $address = $data['address'];
        if (strpos($address, 'м.') === false) {
            if ($data['correct_address']) {
                $address = $data['correct_address'];
            }
            $address = str_replace(' ', '+', $address);
            $url = 'http://maps.google.com/maps/api/geocode/json?address=' . $address . '&components=country:Russia&sensor=false&language=ru';
            $response = file_get_contents($url);
            $this->addrInfo = json_decode($response);

            if (isset($this->addrInfo->status) && $this->addrInfo->status == 'OK') {
                foreach($this->addrInfo->results as $result) {
                    if (count($result) > 0 && isset($result->geometry->bounds)) {
                        $lat = ($result->geometry->bounds->northeast->lat + $result->geometry->bounds->southwest->lat) / 2;
                        $lng = ($result->geometry->bounds->northeast->lng + $result->geometry->bounds->southwest->lng) / 2;
                        $queryBuilder->addSelect('SQRT((p.latitude - :lat)*(p.latitude - :lat) + (p.longtitude - :lng)*(p.longtitude - :lng)) AS HIDDEN distance');
                        $queryBuilder->setParameter('lat', $lat);
                        $queryBuilder->setParameter('lng', $lng);
                        $queryBuilder->orderBy('distance', 'ASC');
                        $this->bounds = $result->geometry->bounds;
                        return true;
                    }
                }
            }
        } else {
            $address = str_replace('м.', '', $address);
            $metroRepo = $this->em->getRepository('CommonBundle:MetroStation');
            $stations = $metroRepo->findByNameLike($address);

            foreach ($stations as $station) {
                /** @var $station MetroStation */
                $queryBuilder->addSelect('SQRT((p.latitude - :lat)*(p.latitude - :lat) + (p.longtitude - :lng)*(p.longtitude - :lng)) AS HIDDEN distance');
                $queryBuilder->setParameter('lat', $station->getLatitude());
                $queryBuilder->setParameter('lng', $station->getLongtitude());
                $queryBuilder->orderBy('distance', 'ASC');
                $this->stationIds[] = array('lat' => $station->getLatitude(), 'lng' => $station->getLongtitude());
            }
            return true;
        }
    }

    public function getBounds($data) {
        if (strpos($data['address'], 'м.') !== false) {
            if (!empty($this->stationIds)) {
                return $this->stationIds;
            }
            str_replace('м.','',$data['address']);
            return $this->filterByStations($data);
        } else {
            return $this->filterByAddressLike($data);
        }
    }

    /**
     * Filter by stations
     */
    protected function filterByStations($data)
    {
        $metroRepo = $this->em->getRepository('CommonBundle:MetroStation');
        $stations = $metroRepo->findByNameLike($data['address']);
        $stationIds = array();

        foreach ($stations as $station) {
            /** @var $station MetroStation */
            $stationIds[] = array('lat' => $station->getLatitude(), 'lng' => $station->getLongtitude());
        }
        return $stationIds;
    }

    /**
     * Filter by address like
     */
    protected function filterByAddressLike($data)
    {
        $address = $data['address'];
        if ($this->bounds) {
            return $this->bounds;
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function editFilterForm(FormBuilder $formBuilder, Request $request = null)
    {
        $formBuilder
            ->add('address', 'text', array(
                'label' => 'label.address',
                'required' => false
            ))
            ->add('correct_address', 'hidden', array(
            ))
            ->add('addr_group', 'hidden', array(
                'data' => null,
            ));
    }
}