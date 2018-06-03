<?php

namespace Inventory\Events;

use Inventory\PurchaseOrder;
use Symfony\Component\EventDispatcher\Event;

/**
 * The purchase_order.created event is dispatched each time a purchase order is created
 * in the system.
 */
class PurchaseOrderCreatedEvent extends Event
{
    const NAME = 'purchase_order.created';

    protected $purchaseOrder;

    public function __construct(PurchaseOrder $order)
    {
        $this->purchaseOrder = $order;
    }

    public function getPurchaseOrder(): PurchaseOrder
    {
        return $this->purchaseOrder;
    }
}
