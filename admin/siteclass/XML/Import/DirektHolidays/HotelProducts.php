<?php
namespace Chalet\XML\Import\DirektHolidays;

/**
 * @author  Ibrahim Abdullah
 * @package Chalet
 */
class HotelProducts
{
    /**
     * @const string
     */
    const HOTEL_PRODUCT_XML_FEED_URL           = 'https://www.direktholidays.at/OTA/OTA_HotelProductRQ?agencyId=ota_chaletnl&agencyPin=2840579';

    /**
     * @const string
     */
    const HOTEL_PRODUCT_XML_FEED_TEST_LOCATION = 'tmp/direktholidays/producten.xml';

    /**
     * @var boolean
     */
    private $test;

    /**
     * @var array
     */
    private $products;

    /**
     * Constructor
     */
    public function __construct($test)
    {
        $this->test = $test;
    }

    /**
     * @return array
     */
    public function all()
    {
        if (null !== $this->products) {
            return $this->products;
        }

        $results    = [];
        $attributes = [];
        $feed       = $this->getXMLFeed();
        $products   = $feed->HotelProducts->HotelProduct;

        foreach ($products as $product) {

            $attributes['product'] = $product->attributes();
            $productId             = (string)$attributes['product']['HotelCode'];
            $plans                 = $product->RatePlans->RatePlan;
            $results[$productId]   = [];

            foreach ($plans as $plan) {

                $reference          = $plan->RatePlanRefs->RatePlanRef;
                $attributes['plan'] = $reference->attributes();
                $planId             = (string)$attributes['plan']['RatePlanCode'];
                $results[$productId][] = $planId;
            }
        }

        return $results;
    }

    /**
     * @return SimpleXMLElement
     */
    public function getXMLFeed()
    {
        return simplexml_load_file((true === $this->test ? self::HOTEL_PRODUCT_XML_FEED_TEST_LOCATION : self::HOTEL_PRODUCT_XML_FEED_URL));
    }
}