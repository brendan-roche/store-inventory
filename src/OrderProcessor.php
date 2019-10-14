<?php

namespace Inventory;

use Error;
use Exception;
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

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EventDispatcher $dispatcher,
        Inventory $inventory,
        PurchaseOrderManager $purchaseOrderManager,
        Logger $logger
    )
    {
        $this->dispatcher = $dispatcher;
        $this->purchaseOrderManager = $purchaseOrderManager;
        $this->inventory = $inventory;
        $this->logger = $logger;
    }

    /*
     * This function receives the path of the json for all the orders of the week,
     * processes all orders for the week,
     * while keeping track of stock levels, units sold and purchased
     * See `orders-sample.json` for example
     *
     * @param string $filePath
     */
    /**
     * @param string $filePath
     */
    public function processFromJson(string $filePath): void
    {
        $json = file_get_contents($filePath);

        $this->dailyOrders = json_decode($json, true);

        foreach ($this->dailyOrders as $day => $orders) {
            try {
                $this->processOrdersForDay($day, $orders);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * @param int $day
     * @param array $orders
     * @throws Exception
     */
    protected function processOrdersForDay(int $day, array $orders): void
    {
        $this->logger->info("Day " . ($day + 1) . " \n");
        $this->purchaseOrderManager->receivePurchaseOrder($day);
        $this->logger->info("");
        foreach ($orders as $index => $orderData) {
            $order = new Order($orderData);
            // If there are any products that don't have enough inventory, reject whole order and don't update stock levels
            try {
                $this->checkOrderQuantitiesOrFail($order);
            } catch (Error $e) {
                $this->logger->info("Order " . ($index + 1) . " rejected due to insufficient inventory: " . $e->getMessage());
                continue;
            }

            $event = new OrderCreatedEvent($order);
            $this->dispatcher->dispatch(OrderCreatedEvent::NAME, $event);
        }

        $this->logger->info("");
        $this->purchaseOrderManager->createPurchaseOrder($day);
        $this->logger->info("");
    }

    /**
     * @param Order $order
     * @throws Exception
     */
    protected function checkOrderQuantitiesOrFail(Order $order)
    {
        foreach ($order->getOrderData() as $productId => $quantity) {
            $stockLevel = $this->inventory->getStockLevel($productId);
            if ($stockLevel < $quantity) {
                throw new Error("$quantity x " . Products::getProductInfo($productId) . " > $stockLevel inventory");
            }
        }
    }
}
