<?php

use Inventory\Events\OrderCreatedEvent;
use Inventory\Events\PurchaseOrderCreatedEvent;
use Inventory\Events\PurchaseOrderReceivedEvent;
use Inventory\InventoryChangesListener;
use Inventory\InventoryReporter;
use Inventory\OrderProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcher;

require __DIR__ . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder(new ParameterBag());
$containerBuilder->addCompilerPass(new RegisterListenersPass());

$loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
$loader->load('services.yml');

$dispatcher = $containerBuilder->get('event_dispatcher');;
assert($dispatcher instanceof EventDispatcher);

$changesListener = $containerBuilder->get('inventory_changes_listener');
assert($changesListener instanceof InventoryChangesListener);

$dispatcher->addListener(OrderCreatedEvent::NAME, [$changesListener, 'onOrderCreated']);
$dispatcher->addListener(PurchaseOrderReceivedEvent::NAME, [$changesListener, 'onPurchaseOrderReceived']);

$inventoryReporter = $containerBuilder->get('inventory_reporter');
assert($inventoryReporter instanceof InventoryReporter);

$dispatcher->addListener(OrderCreatedEvent::NAME, [$inventoryReporter, 'onOrderCreated']);
$dispatcher->addListener(PurchaseOrderCreatedEvent::NAME, [$inventoryReporter, 'onPurchaseOrderCreated']);
$dispatcher->addListener(PurchaseOrderReceivedEvent::NAME, [$inventoryReporter, 'onPurchaseOrderReceived']);

$orderProcessor = $containerBuilder->get('order_processor');
assert($orderProcessor instanceof OrderProcessor);

$orderProcessor->processFromJson('orders-sample.json');

$inventoryReporter->displaySummary();
