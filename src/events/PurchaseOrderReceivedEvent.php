<?php
namespace Inventory\Events;

use Inventory\PurchaseOrder;
use Symfony\Component\EventDispatcher\Event;

/**
 * The purchase_order.received event is dispatched each time a purchase order has been received
 */
class PurchaseOrderReceivedEvent extends Event
{
    const NAME = 'purchase_order.received';

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
