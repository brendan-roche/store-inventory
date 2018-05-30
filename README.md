# Store Inventory

You are managing the inventory of a small sweet store that sells 5 products:
brownie, lamington, blueberry muffin, croissant, slice of chocolate cake 

```php
class Products
{
	public const BROWNIE = 1;
	public const LAMINGTON = 2;
	public const BLUEBERRY_MUFFIN = 3;
	public const CROISSANT = 4;
	public const CHOCOLATE_CAKE = 5;
}
```

For each product you will be required to track:
 
  - the total units sold
  - the current stock levels 
  - the number of units to purchase to top up the stock

Initially each product will start with a stock level of 20 units.
Every day for one week you will receive a list of orders with quantities to order for each product.
If any orders cannot be fulfilled because there is no stock, the whole order shall be rejected.

A sample json file of orders for each day is provided as `orders-sample.json`, 
and you'll need to use this as a basis for receiving and processing daily orders. 

It is in the following format:

```javascript
[
	// Monday orders
	[ 
		// Brownie: 2, Lamington: 1
		{"1": 2, "2": 1},
		// Blueberry Muffin: 1, Chocolate cake: 1
		{"3": 1, "5": 1}
	],
	// Tuesday orders
	[ 
		// Croissant: 3
		{"4": 3},
		// Brownie: 3, Blueberry Muffin: 2, Chocolate cake: 2
		{"1": 3, "3": 2, "5": 2}
	]
	// Other days orders...
]
```

At the end of each days orders, if stock level for any product fall below 10, a purchase order will need to be created to replenish the low stock items. 
For each low stock item top up the supply back to 20 units for the next days supply. So if there are 3 croissants at the end of one day, the purchase order should be for 17 units. 

After 7 days of trading the program needs to output a nice summary for each product:

 - The total units sold
 - The total units purchased
 - The current stock level
 
## Provided Interfaces

For your solution there are four Php interfaces you must implement
 
`src/OrderProcessorInterface.php` Class to process orders and output a summary

`src/InventoryInterface.php` Class to manage inventory and track stock levels

`src/ProductsSoldInterface.php` Track total products sold for whole week

`src/ProductsPurchasedInterface.php` Track total products purchased for whole week

You may add methods to the interfaces as you see fit, but please do not change the provided methods

## Notes

Please clone this repository and provide your solution preferably under your own github account.

Minimum of PHP 7.1

Please consider relevant error handling and validation, documention for your code, and adding comments where helpful. 

Also add any relevant unit tests using whatever Php unit test framework you prefer.

To keep things simple you should not use any Php frameworks, or databases to store stock levels, everything should be stored in memory at runtime.
