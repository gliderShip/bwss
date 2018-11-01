<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\CostItem;
use AppBundle\Entity\ItemSnapshot;
use AppBundle\Model\Timestampable;
use AppBundle\Model\Versionable;
use AppBundle\Repository\CostItemRepository;
use AppBundle\Repository\ItemSnapshotRepository;
use AppBundle\Repository\SnapshotRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class SnapshotManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getCurrentSnapshot(Versionable $entity){

        /**
         * @var SnapshotRepository $repository
         */
        $repository = $this->em->getRepository(get_class($entity));
        $version = $entity->getUpdatedAt()->getTimestamp();
        $currentSnapshot = $repository->findOneByVersion($version);

        if(!$currentSnapshot){
            throw new Exception('TODO');
        }

        return $currentSnapshot;

    }
}