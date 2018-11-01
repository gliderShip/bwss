<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Service
 *
 * @ORM\Table(name="category_snapshot")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategorySnapshotRepository")
 */
class CategorySnapshot extends ServiceCategory
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ServiceCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $category;

    static function create(ServiceCategory $serviceCategory){

        $categorySnapshot = new CategorySnapshot();
        $categorySnapshot->category = $serviceCategory;
        $categorySnapshot->setName($serviceCategory->getName());

        return $categorySnapshot;
    }

    /**
     * @return ServiceCategory|null
     */
    public function getCategory(): ?ServiceCategory
    {
        return $this->category;
    }


}

