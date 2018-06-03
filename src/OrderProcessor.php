<?php

namespace Inventory;

use Inventory\Events\OrderCreatedEvent;
use Inventory\Interfaces\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class OrderProcessor implements OrderProcessorInterface
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var Inventory
     */
    private $inventory;

    /**
     * @var PurchaseOrderManager
     */
    private $purchaseOrderManager;

    /**
     * @var array
     */
    private $dailyOrders = [];

    public function __construct(
        EventDispatcher $dispatcher,
        Inventory $inventory,
        PurchaseOrderManager $purchaseOrderManager
    ) {
        $this->dispatcher = $dispatcher;
        $this->purchaseOrderManager = $purchaseOrderManager;
        $this->inventory = $inventory;
    }

    /*
     * This function receives the path of the json for all the orders of the week,
     * processes all orders for the week,
     * while keeping track of stock levels, units sold and purchased
     * See `orders-sample.json` for example
     *
     * @param string $filePath
     */
    public function processFromJson(string $filePath): void
    {
        $json = file_get_contents($filePath);

        $this->dailyOrders = json_decode($json, true);

        foreach ($this->dailyOrders as $day => $orders) {
            $this->processOrdersForDay($day, $orders);
        }
    }

    /**
     * @param int $day
     * @param array $orders
     */
    protected function processOrdersForDay(int $day, array $orders): void
    {
        $this->purchaseOrderManager->receivePurchaseOrder($day);
        foreach ($orders as $orderData) {
            $order = new Order($orderData);
            // If there are any products that don't have enough inventory, reject whole order and don't update stock levels
            if (!$this->checkOrderQuantities($order)) {
                continue;
            }

            $event = new OrderCreatedEvent($order);
            $this->dispatcher->dispatch(OrderCreatedEvent::NAME, $event);
        }

        $this->purchaseOrderManager->createPurchaseOrder($day);
    }

    /**
     * @param array $order
     * @return bool
     */
    protected function checkOrderQuantities(Order $order): bool
    {
        foreach ($order->getOrderData() as $productId => $quantity) {
            if ($this->inventory->getStockLevel($productId) < $quantity) {
                return false;
            }
        }

        return true;
    }
}
