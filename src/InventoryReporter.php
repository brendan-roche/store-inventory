<?php

namespace Inventory;

use Inventory\Events\OrderCreatedEvent;
use Inventory\Events\PurchaseOrderCreatedEvent;
use Inventory\Events\PurchaseOrderReceivedEvent;
use Inventory\Interfaces\ProductsPurchasedInterface;
use Inventory\Interfaces\ProductsSoldInterface;

class InventoryReporter implements ProductsSoldInterface, ProductsPurchasedInterface
{
    /**
     * @var Inventory
     */
    private $inventory;

    /**
     * @var int[]
     */
    private $sold = [];

    /**
     * @var int[]
     */
    private $received = [];

    /**
     * @var int[]
     */
    private $pending = [];

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * @param OrderCreatedEvent $event
     */
    public function onOrderCreated(OrderCreatedEvent $event)
    {
        $order = $event->getOrder();
        foreach ($order->getOrderData() as $productId => $quantity) {
            $this->sold[$productId] = ($this->sold[$productId] ?? 0) + $quantity;
        }
    }


    /**
     * @param PurchaseOrderCreatedEvent $event
     */
    public function onPurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $purchaseOrder = $event->getPurchaseOrder();
        foreach ($purchaseOrder->getProductQuantities() as $productId => $quantity) {
            $this->pending[$productId] = ($this->pending[$productId] ?? 0) + $quantity;
        }
    }

    /**
     * @param PurchaseOrderReceivedEvent $event
     */
    public function onPurchaseOrderReceived(PurchaseOrderReceivedEvent $event)
    {
        $purchaseOrder = $event->getPurchaseOrder();
        foreach ($purchaseOrder->getProductQuantities() as $productId => $quantity) {
            $this->pending[$productId] -= $quantity;
            $this->received[$productId] = ($this->received[$productId] ?? 0) + $quantity;
        }
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getSoldTotal(int $productId): int
    {
        return $this->sold[$productId];
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getPurchasedReceivedTotal(int $productId): int
    {
        return $this->received[$productId];
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getPurchasedPendingTotal(int $productId): int
    {
        return $this->pending[$productId];
    }

    public function displaySummary()
    {
        foreach (Products::getProductNames() as $productId => $name) {
            echo "($productId) $name Totals\n";
            echo 'Sold: ' . ($this->sold[$productId] ?? 0) . "\n";
            echo 'Received Purchases: ' . ($this->received[$productId] ?? 0) . "\n";
            echo 'Pending Purchases: ' . ($this->pending[$productId] ?? 0) . "\n";
            echo 'Stock Level: ' . $this->inventory->getStockLevel($productId) . "\n\n";
        }
    }
}
