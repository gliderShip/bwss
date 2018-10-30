<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\CostItem;

///**
// * Price
// *
// * @ORM\Table(name="price")
// * @ORM\Entity(repositoryClass="AppBundle\Repository\PriceRepository")
// */
abstract class Price
{
    const VAT = 0.21;
    const DEFAULT_CURRENCY = 'EUR';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;




    /**
     * @param int $id
     */
    public function setId(int $id = null): void
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }




    public function __clone()
    {
        $newValueObject = clone $this;
        $newValueObject->setId(null);
    }

    public function __toString()
    {
        return "{$this->getAmount()} {$this->getCurrency()}";
    }
}

