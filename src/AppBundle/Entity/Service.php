<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Service
 *
 * @ORM\Table(name="service")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServiceRepository")
 */
class Service
{
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
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Service", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Service", mappedBy="parent")
     */
    private $children;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $id = $this->getId();
        if( $id!=null ){
            //editing existing object
            $parent = $this->getParent();
            if ($parent) {
                if ($parent == $this) {
                    $context->buildViolation('It can not be its own parent mate!')
                        ->atPath('parent')
                        ->addViolation();
                        return;
                }

                $ancestor = $parent;
                while ($ancestor){
                    if($ancestor->getId() == $this->getId()){
                        $context->buildViolation(
                            "$this can not be a child of [{$parent}] because is its ancestor! Try updating [{$parent}] parent if that's what you're up to."
                        )
                            ->atPath('parent')
                            ->addViolation();
                            return;
                    }

                    $ancestor = $ancestor->getParent();
                }
            }

        }

    }


    public function __construct() {
        $this->children = new ArrayCollection();
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Service
     */
    public function setName($name): Service
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param $parent
     * @return $this
     */
    public function setParent(Service $parent = null): Service
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Service|null
     */
    public function getParent(): ?Service
    {
        return $this->parent;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children): void
    {
        $this->children = $children;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }


}

