<?php
namespace DancePark\CommonBundle\Command;

use DancePark\CommonBundle\Entity\AddressGroup;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddressRegionImportCommand extends ContainerAwareCommand
{
    /** @var $em EntityManager */
    protected $em;

    protected $levels;

    protected $addressRepo;
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this
            ->setName('import:ag')
            ->setDescription('Import address group.');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getEntityManager();
        $path = dirname(__FILE__) . '/../../../../web/files/public/images/address_group.csv';

        $levelRepo = $this->em->getRepository('CommonBundle:AddressLevel');

        $this->addressRepo = $this->em->getRepository('CommonBundle:AddressGroup');

        $levels = $levelRepo->findAll();
        $this->levels['city'] = $levels[0];
        $this->levels['street'] = $levels[1];

        if (file_exists($path)) {
            $file = fopen($path, 'r');
            while(!feof($file)) {
                $this->saveRow(fgets($file));
                gc_collect_cycles();
            }
            fclose($path);
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
            $ag = new AddressGroup();

            $ag->setId($pieces[0]);
            $ag->setAddressLevel($this->levels[$pieces[1]]);
            $ag->setName($pieces[2]);
            $ag->setPrefix($pieces[3]);
            $ag->setRoot($pieces[4]);
            $parent = $this->addressRepo->find($pieces[5]);
            $ag->setParent($parent);

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
}