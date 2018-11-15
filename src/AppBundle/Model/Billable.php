<?php
/**
 * Created by PhpStorm.
 * User: Glider
 * Date: 10/30/2018
 * Time: 1:49 PM
 */

namespace AppBundle\Model;


interface Billable
{
    const VAT = 0.21;
    const DEFAULT_CURRENCY = 'EUR';

    const BILLABLE_TYPES = [
        'SINGLE AMOUNT' => 'SINGLE AMOUNT',
        'HOURLY AMOUNT' => 'HOURLY AMOUNT'
    ];

    const PRICE_PRECISION = 9;
    const PRICE_SCALE = 2;

    const VAT_PRECISION = 3;
    const VAT_SCALE = 2;

    const MAX_PRICE = 9999999;

    public function getPrice($vatIncluded = true);
    public function getPriceType();
    public function getGrossPrice();
    public function getNetPrice();

//    public function getCost($vatIncluded = true);
//    public function getNetCost();
//    public function getGrossCost();

    public function getCurrency();
    public function getVat();
    public function getVatAmount();
    public function priceEquals($billable);
    public function priceEqualsNet($billable);
    public function priceEqualsGross($billable);

    public function isRentable();


}