<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\CategorySnapshot;
use AppBundle\Entity\CostItem;
use AppBundle\Entity\ItemSnapshot;
use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceCategory;
use AppBundle\Entity\ServiceSnapshot;
use AppBundle\Model\Timestampable;
use AppBundle\Model\Versionable;
use AppBundle\Repository\CategorySnapshotRepository;
use AppBundle\Repository\CostItemRepository;
use AppBundle\Repository\ItemSnapshotRepository;
use AppBundle\Repository\ServiceSnapshotRepository;
use AppBundle\Repository\SnapshotRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class CategorySnapshotManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CategorySnapshotRepository
     */
    private $repository;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(CategorySnapshot::class);
    }

    public function getCurrentSnapshot(ServiceCategory $category)
    {

        $version = $category->getUpdatedAt()->getTimestamp();
        $currentSnapshot = $this->repository->findOneByVersion($version);

        if (!$currentSnapshot) {
            $currentSnapshot = $this->createSnapshot($category, $version);
        }

        return $currentSnapshot;

    }

    private function createSnapshot(ServiceCategory $category, $version)
    {
            $categorySnapshot = new CategorySnapshot($category, $version);
            $categorySnapshot->setName($category->getName());

            return $categorySnapshot;
    }

}