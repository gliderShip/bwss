<?php

namespace AppBundle\Repository;

/**
 * CostItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
abstract class SnapshotRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param int $version
     * @return mixed
     */
    public function findOneByVersion(int $version)
    {
        return parent::findOneByVersion($version);
    }
}