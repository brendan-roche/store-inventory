<?php

namespace Inventory;

class Order
{
    /**
     * @var array
     */
    private $orderData = [];

    /**
     * @param array $orderData
     */
    public function __construct(array $orderData)
    {
        $this->orderData = $orderData;
    }

    /**
     * @return array
     */
    public function getOrderData(): array
    {
        return $this->orderData;
    }
}
