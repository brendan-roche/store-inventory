<?php

namespace Inventory;

use Inventory\Events\PurchaseOrderCreatedEvent;
use Inventory\Events\PurchaseOrderReceivedEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PurchaseOrderManager
{
    private const REORDER_QUANTITY = 20;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * All pending Purchase orders
     *
     * @var PurchaseOrder[]
     */
    private $purchaseOrders = [];

    /**
     * @var Inventory
     */
    private $inventory;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(EventDispatcher $dispatcher, Inventory $inventory, Logger $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->inventory = $inventory;
        $this->logger = $logger;
    }

    /**
     * @param int $day
     */
    public function receivePurchaseOrder(int $day): void
    {
        // Check if a purchase order is due to be received today and notify listeners that it has been received
        if (isset($this->purchaseOrders[$day])) {
            $purchaseOrder = $this->purchaseOrders[$day];
            $event = new PurchaseOrderReceivedEvent($purchaseOrder);
            $this->dispatcher->dispatch(PurchaseOrderReceivedEvent::NAME, $event);
            // we remove from list of pending purchaseOrders once it has been received
            unset($this->purchaseOrders[$day]);
        } else {
            $this->logger->info("No stock received for today");
        }
    }

    public function getLowStockAndNotReceivingProducts(): array
    {
        $lowStockProducts = array_keys($this->inventory->getLowStockProducts());

        // iterate through all pending purchase orders and filter out products already in pending purchase orders
        foreach ($this->purchaseOrders as $purchaseOrder) {
            $poItems = array_keys($purchaseOrder->getProductQuantities());
            $alreadyInPO = array_intersect($lowStockProducts, $poItems);
            if (count($alreadyInPO) > 0) {
                $this->logger->info('The following low stock items were not ordered as they are in a pending PO: ' . implode(', ', $alreadyInPO));
                $lowStockProducts = array_diff($lowStockProducts, $poItems);
            }
        }

        return $lowStockProducts;
    }

    /**
     * @param int $day
     */
    public function createPurchaseOrder(int $day): void
    {
        $lowStockProducts = $this->getLowStockAndNotReceivingProducts();

        // Only create purchase order if there are low stock items
        if (count($lowStockProducts) > 0) {
            $orderProductsQuantity = [];

            foreach ($lowStockProducts as $productId) {
                $orderProductsQuantity[$productId] = self::REORDER_QUANTITY;
            }
            $purchaseOrder = new PurchaseOrder($orderProductsQuantity);
            // We store the purchase order at the index of the day it is to be received: 2 days from now
            $this->purchaseOrders[$day + 2] = $purchaseOrder;
            $event = new PurchaseOrderCreatedEvent($purchaseOrder);
            $this->dispatcher->dispatch(PurchaseOrderCreatedEvent::NAME, $event);
        } else {
            $this->logger->info('There were no low stock items for today');
        }
    }
}