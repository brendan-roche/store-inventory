<?php

namespace Inventory;


use Inventory\Events\OrderCreatedEvent;
use Inventory\Events\PurchaseOrderReceivedEvent;

class InventoryChangesListener
{
    /**
     * @var Inventory
     */
    private $inventory;

    /**
     * InventoryChangesListener constructor.
     * @param Inventory $inventory
     */
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

        // Decrement stock levels
        foreach ($order->getOrderData() as $productId => $quantity) {
            $this->inventory->decrement($productId, $quantity);
        }
    }


    /**
     * @param PurchaseOrderReceivedEvent $event
     */
    public function onPurchaseOrderReceived(PurchaseOrderReceivedEvent $event)
    {
        $purchaseOrder = $event->getPurchaseOrder();
        $productQuantities = $purchaseOrder->getProductQuantities();
        foreach ($productQuantities as $productId => $quantity) {
            $this->inventory->increment($productId, $quantity);
        }
    }
}
