<?php
namespace Inventory\Events;

use Symfony\Component\EventDispatcher\Event;
use Inventory\Order;

/**
 * The order.placed event is dispatched each time an order is created
 * in the system.
 */
class OrderCreatedEvent extends Event
{
    const NAME = 'order.created';

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}