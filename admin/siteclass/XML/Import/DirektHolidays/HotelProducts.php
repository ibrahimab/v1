<?php
namespace Chalet\XML\Import\DirektHolidays;

/**
 * @author  Ibrahim Abdullah
 * @package Chalet
 */
class HotelProducts
{
    /**
     * @var array
     */
    private $products;

    /**
     * @var boolean
     */
    private $test;

    /**
     * @var FeedFetcher
     */
    private $feedFetcher;

    /**
     * Constructor
     *
     * @param FeedFetcher $feedFetcher
     * @param boolean $test
     */
    public function __construct(FeedFetcher $feedFetcher, $test)
    {
        $this->test        = $test;
        $this->feedFetcher = $feedFetcher;
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
        return $this->feedFetcher->fetch(FeedFetcher::TYPE_PRODUCTS);
    }
}
