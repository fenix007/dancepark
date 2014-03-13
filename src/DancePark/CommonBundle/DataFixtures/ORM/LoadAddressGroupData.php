<?php
namespace DancePark\CommonBundle\DataFixtures\ORM;

use DancePark\CommonBundle\Entity\AddressGroup;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class LoadAddressGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    /** @var $em EntityManager */
    protected $em;

    protected $levels;

    protected $addressRepo;

    protected $city;

    protected $count;
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $this->count = 0;
        $this->em = $manager;
        $path = dirname(__FILE__) . '/../../../../../web/files/public/images/address_group.csv';

        $levelRepo = $manager->getRepository('CommonBundle:AddressLevel');

        $this->addressRepo = $manager->getRepository('CommonBundle:AddressGroup');

        $this->city =  $this->addressRepo->find(2);

        $levels = $levelRepo->findAll();
        $this->levels['city'] = $this->getReference('addressLevel:city');
        $this->levels['street'] = $this->getReference('addressLevel:street');

        if (file_exists($path)) {
            $file = fopen($path, 'r');
            while(!feof($file)) {
                $this->saveRow(fgets($file));
                gc_collect_cycles();
                if ($this->count > 100) {
                    break;
                }
            }
            fclose($file);
        }
    }

    /**
     *
     * @param $line
     */
    protected function saveRow($line)
    {
        $line = str_replace('"', '', $line);
        $pieces = explode(',', $line);

        if (count($pieces) == 9 && $pieces[0] != 1 && $pieces[0] != 3 && $pieces[5] != 1 && $pieces[5] != 3) {
            $this->count++;
            $ag = new AddressGroup();

            $ag->setId($pieces[0]);
            $ag->setAddressLevel($this->levels[$pieces[1]]);
            $ag->setName($pieces[2]);
            $ag->setPrefix($pieces[3]);
            $ag->setRoot($pieces[4]);
            if ($pieces[0] != 2) {
                $ag->setParent($this->city);
            } else {
                $ag->setParent(null);
                $this->city = $ag;
            }

            $this->em->persist($ag);
            $this->em->persist($this->levels[$pieces[1]]);

            $this->em->flush();
            $this->em->clear();
            unset($ag);
            unset($line);
        } else {
            //print '<pre>' . htmlspecialchars(var_dump($pieces, 1)) . '</pre>';
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 1;
    }
}