<?php
//
//namespace AppBundle\Entity;
//
//use AppBundle\Model\Billable;
//use AppBundle\Model\Item;
//use Doctrine\ORM\Mapping as ORM;
//use AppBundle\Entity\Service;
//use Symfony\Component\Validator\Constraints as Assert;
//
///**
// * OfferItem
// *
// * @ORM\Table(name="offer_item")
// * @ORM\Entity(repositoryClass="AppBundle\Repository\OfferItemRepository")
// */
//class OfferItem extends CostItem
//{
//    /**
//     * @var integer
//     * @Assert\GreaterThanOrEqual(0, message="This value should be positive")
//     * @ORM\Column(name="hours", type="integer", options={"unsigned"=true}, nullable=false)
//     */
//    private $hours;
//
//    public function __construct(CostItem $costItem)
//    {
//    }
//
//
//    /**
//     * @return string
//     */
//    public function getHours(): ?int
//    {
//        return $this->hours;
//    }
//
//    /**
//     * @param string $hours
//     */
//    public function setHours(int $hours)
//    {
//        $this->hours = $hours;
//    }
//
//
//    public function getCost($vatIncluded = true)
//    {
//        if ($this->getPriceType() == Billable::BILLABLE_TYPES['SINGLE AMOUNT']) {
//            return $this->getPrice($vatIncluded);
//        } elseif ($this->getPriceType() == Billable::BILLABLE_TYPES['HOURLY AMOUNT']) {
//            return $this->getPrice($vatIncluded) * $this->getHours();
//        } else {
//            throw new \Exception('Undefined cost for this price type');
//        }
//
//    }
//
//}
//
