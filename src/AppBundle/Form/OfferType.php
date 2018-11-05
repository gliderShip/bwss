<?php

namespace AppBundle\Form;

use AppBundle\Entity\CostItem;
use AppBundle\Entity\ItemSnapshot;
use AppBundle\Entity\Offer;
use AppBundle\Entity\OfferItem;
use AppBundle\Model\Billable;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class OfferType extends AbstractType
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var Offer $offer
         */
        $offer = $builder->getData();
//        dump($offer);

        foreach ($offer->getItems() as $offerItem) {
            if($offerItem->getItemSnapshot()->getPriceType() == Billable::BILLABLE_TYPES['HOURLY AMOUNT']){
                $itemSnapshot = $offerItem->getItemSnapshot();
                $builder->add($itemSnapshot->getName(), IntegerType::class, [
                        'attr' => [
                            'offerItem' => $offerItem->getId() ?? null,
                            'itemSnapshot' => $itemSnapshot->getId() ?? null,
                            'costItem' => $itemSnapshot->getCostItem()->getId() ?? null,
                        ],
                        'mapped' => false,
                        'label' => false
                    ]
                );
            }
        }

        $builder->add('offer', HiddenType::class,['data' => $offer->getId(), 'mapped' => false]);
        $builder->add('serviceSnapshot', HiddenType::class,['data' => $offer->getServiceSnapshot()->getId(), 'mapped' => false]);
        $builder->setAction($this->router->generate('offer_create', ['serviceId' => $offer->getServiceSnapshot()->getService()->getId()]));


        $builder->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));

//        $resolver->setRequired('items');

    }

}
