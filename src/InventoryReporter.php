<?php

namespace Inventory;

use Exception;
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

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Inventory $inventory, Logger $logger)
    {
        $this->inventory = $inventory;
        $this->logger = $logger;
    }

    /**
     * @param OrderCreatedEvent $event
     * @throws Exception
     */
    public function onOrderCreated(OrderCreatedEvent $event)
    {
        $order = $event->getOrder();
        foreach ($order->getOrderData() as $productId => $quantity) {
            $this->sold[$productId] = ($this->sold[$productId] ?? 0) + $quantity;
            $newInventory = $this->inventory->decrement($productId, $quantity);
            $this->logger->info("Sold $quantity x " . Products::getProductInfo($productId) . ", new stock level: " . $newInventory);
        }
    }


    /**
     * @param PurchaseOrderCreatedEvent $event
     */
    public function onPurchaseOrderCreated(PurchaseOrderCreatedEvent $event)
    {
        $purchaseOrder = $event->getPurchaseOrder();
        $productQuantities = $purchaseOrder->getProductQuantities();
        $this->logger->info('Created PO for the following low stock items: ' . implode(', ', array_keys($productQuantities)));
        foreach ($productQuantities as $productId => $quantity) {
            $this->pending[$productId] = ($this->pending[$productId] ?? 0) + $quantity;
        }
    }

    /**
     * @param PurchaseOrderReceivedEvent $event
     * @throws Exception
     */
    public function onPurchaseOrderReceived(PurchaseOrderReceivedEvent $event)
    {
        $purchaseOrder = $event->getPurchaseOrder();
        foreach ($purchaseOrder->getProductQuantities() as $productId => $quantity) {
            $this->pending[$productId] -= $quantity;
            $this->received[$productId] = ($this->received[$productId] ?? 0) + $quantity;
            $this->inventory->increment($productId, $quantity);
            $this->logger->info("Received $quantity x " . Products::getProductInfo($productId) . ", new stock level: " . $this->inventory->getStockLevel($productId));
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
        echo "Weekly Summary

+----------------------------------------------------------+
| Product              | Sold | Received | Pending | Stock |
| -------------------- | ---- | -------- | ------- | ----- |
";

        foreach (Products::getProductNames() as $productId => $name) {
            echo sprintf(
                "| (%s) %-16s | %4s | %8s | %7s | %5s |\n",
                $productId,
                $name,
                $this->sold[$productId] ?? 0,
                $this->received[$productId] ?? 0,
                $this->pending[$productId] ?? 0,
                $this->inventory->getStockLevel($productId)
            );
        }
        echo "+----------------------------------------------------------+\n\n";
    }
}
