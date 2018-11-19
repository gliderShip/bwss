<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Model\Billable;

/**
 * Discount
 *
 * @ORM\Table(name="discount")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DiscountRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name")
 * @UniqueEntity("code")
 */
class Discount
{
    const MAX_DISCOUNT = 9999999;
    const CODE_LENGTH = 6;
    const CODE_PATTERN = "/^[A-Z0-9]{6}/";

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=Discount::CODE_LENGTH, max=Discount::CODE_LENGTH)
     * @Assert\Regex(Discount::CODE_PATTERN)
     * @ORM\Column(name="code", type="string", length=Discount::CODE_LENGTH, unique=true)
     */
    private $code;

    /**
     * @var float
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank()
     * @Assert\LessThanOrEqual(Discount::MAX_DISCOUNT, message="This value should be less than or equal to {{ compared_value }}.")
     * @ORM\Column(name="price", type="decimal", precision=Billable::PRICE_PRECISION, scale=Billable::PRICE_SCALE, options={"unsigned"=true})
     */
    private $price;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @Assert\LessThan(propertyPath="endDate")
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @Assert\GreaterThan("now")
     * @Assert\GreaterThan(propertyPath="startDate")
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;

    /**
     * @var string 3-letter ISO 4217 currencies
     * @Assert\Currency()
     * @ORM\Column(name="currency", type="string", length=3)
     */
    private $currency = Billable::DEFAULT_CURRENCY;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\CostItem", inversedBy="discounts")
     * @ORM\JoinTable(name="item_discounts")
     */
    protected $costItems;


    public function __construct()
    {
        $this->code = strtoupper(substr(md5(uniqid(null, true)), 0, Discount::CODE_LENGTH ));
        $this->costItems = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Discount
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Discount
     */
    public function setCode($code)
    {
        $this->code = strtoupper($code);

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * Set startDate.
     *
     * @param \DateTime $startDate
     *
     * @return Discount
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param \DateTime $endDate
     *
     * @return Discount
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Set currency
     * @param string 3-letter ISO 4217 currencies
     */
    public function setCurrency($currency = Billable::DEFAULT_CURRENCY)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return mixed
     */
    public function addCostItem( CostItem $costItem)
    {
        $costItem->addDiscount($this);
        $this->costItems[] = $costItem;
    }

    /**
     * @return mixed
     */
    public function getCostItems()
    {
        return $this->costItems;
    }

    public function setCostItems($costItems): void
    {
        $this->costItems = new ArrayCollection();

        foreach ($costItems as $ci){
            $this->addCostItem($ci);
        }
    }

    public function isExpired(string $time = 'now'){

        $now = new \DateTime($time);
        return $now  > $this->getEndDate();
    }

    public function __toString()
    {
        return $this->code . ' - '. $this->price.$this->currency ;
    }


}
