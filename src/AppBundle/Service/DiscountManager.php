<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 11/1/2018
 * Time: 5:45 PM
 */

namespace AppBundle\Service;

use AppBundle\Entity\CostItem;

class DiscountManager
{
    public function getValidDisounts(CostItem $ci){

        $validDiscounts = array();
        $discounts = $ci->getDiscounts();
        if($ci->isDiscountable()){
            foreach ($discounts as $discount){
                if(!$discount->isExpired()){
                    $validDiscounts[] = $discount;
                }
            }
        }

        return $validDiscounts;
    }

}