<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ServiceCategory;

/**
 * ServiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategorySnapshotRepository extends SnapshotRepository
{
    public function getCurrent(ServiceCategory $category, int $version){

        return $this->findOneBy(['category'=>$category, 'version' => $version]);
    }
}
