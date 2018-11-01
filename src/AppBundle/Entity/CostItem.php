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
 */
class CostItem extends Item implements Billable, Versionable
{
    use Priceable, Timestampable;

    public function __construct() {

        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
    }


}

