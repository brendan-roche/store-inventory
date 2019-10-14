<?php

namespace Inventory;

class PurchaseOrder
{
    /**
     * @var array
     */
    private $productQuantities = [];

    /**
     * @param array $productQuantities
     */
    public function __construct(array $productQuantities)
    {
        $this->productQuantities = $productQuantities;
    }

    /**
     * @return array
     */
    public function getProductQuantities(): array
    {
        return $this->productQuantities;
    }
}