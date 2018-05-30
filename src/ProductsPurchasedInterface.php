<?php

interface ProductsPurchasedInterface
{
    /**
     * @param int $productId
     * @return int
     */
    public function getPurchasedTotal(int $productId): int;
}
