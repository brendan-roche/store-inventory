<?php

namespace Inventory;

class Products
{
    public const BROWNIE = 1;
    public const LAMINGTON = 2;
    public const BLUEBERRY_MUFFIN = 3;
    public const CROISSANT = 4;
    public const CHOCOLATE_CAKE = 5;

    public static function getProductNames()
    {
        return [
            self::BROWNIE          => 'Brownie',
            self::LAMINGTON        => 'Lamington',
            self::BLUEBERRY_MUFFIN => 'Blueberry Muffin',
            self::CROISSANT        => 'Croissant',
            self::CHOCOLATE_CAKE   => 'Chocolate Cake',
        ];
    }
}