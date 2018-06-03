<?php

namespace Inventory\Interfaces;

interface ProductsSoldInterface
{
    /**
     * @param int $productId
     * @return int
     */
    public function getSoldTotal(int $productId): int;
}
