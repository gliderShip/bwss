<?php

namespace AppBundle\Entity;

use AppBundle\Model\Billable;
use AppBundle\Model\Item;
use AppBundle\Model\Priceable;
use AppBundle\Model\Timestampable;
use AppBundle\Model\Versionable;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Service;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CostItem
 *
 * @ORM\Table(name="cost_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CostItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class CostItem extends Item implements Billable, Versionable
{
    use Priceable, Timestampable;

    public function __construct() {

    }


    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }
}
