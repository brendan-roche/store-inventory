<?php

namespace Inventory;

use Inventory\Interfaces\InventoryInterface;

class Inventory implements InventoryInterface
{
    private $inventory = [
        Products::BROWNIE          => 20,
        Products::LAMINGTON        => 20,
        Products::BLUEBERRY_MUFFIN => 20,
        Products::CROISSANT        => 20,
        Products::CHOCOLATE_CAKE   => 20,
    ];

    private const LOW_STOCK_LEVEL = 10;

    /**
     * @param int $productId
     * @param int $quantity positive quantity
     */
    public function increment(int $productId, int $quantity)
    {
        $this->inventory[$productId] += $quantity;
    }

    /**
     * @param int $productId
     * @param int $quantity positive quantity
     */
    public function decrement(int $productId, int $quantity)
    {
        $this->inventory[$productId] -= $quantity;
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getStockLevel(int $productId): int
    {
        return $this->inventory[$productId];
    }

    /**
     * @return int[]
     */
    public function getLowStockProducts(): array
    {
        return array_filter($this->inventory, function (int $stockLevel) {
            return $stockLevel < self::LOW_STOCK_LEVEL;
        });
    }
}
