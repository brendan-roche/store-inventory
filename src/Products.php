<?php

namespace Inventory;

use Exception;

class Products
{
    public const BROWNIE = 1;
    public const LAMINGTON = 2;
    public const BLUEBERRY_MUFFIN = 3;
    public const CROISSANT = 4;
    public const CHOCOLATE_CAKE = 5;

    /**
     * @return string[]
     */
    public static function getProductNames(): array
    {
        return [
            self::BROWNIE          => 'Brownie',
            self::LAMINGTON        => 'Lamington',
            self::BLUEBERRY_MUFFIN => 'Blueberry Muffin',
            self::CROISSANT        => 'Croissant',
            self::CHOCOLATE_CAKE   => 'Chocolate Cake',
        ];
    }

    /**
     * @param int $id
     * @return string
     * @throws Exception
     */
    public static function getProductName(int $id): string
    {
        $productNames = self::getProductNames();
        if (!array_key_exists($id, $productNames)) {
            throw new Exception("Product with id $id does not exist");
        }

        return $productNames[$id];
    }

    /**
     * @param int $id
     * @return string
     * @throws Exception
     */
    public static function getProductInfo(int $id): string
    {
        return "($id) " .  self::getProductName($id);
    }
}