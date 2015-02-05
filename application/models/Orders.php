<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model
{

    // constructor
    function __construct()
    {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code)
    {
        // if a previous order item exists, update it; create a new order item
        // otherwise.
        if($this->orderitems->exists($num, $code))
        {
            // get and update an old order item.
            $old_order_item = $this->orderitems->get($num, $code);
            $old_order_item->quantity += 1;
            $this->orderitems->update($old_order_item);
        }
        else
        {
            // create and add a new order item.
            $new_order_item = $this->orderitems->create();
            $new_order_item->order    = $num;
            $new_order_item->item     = $code;
            $new_order_item->quantity = 1;
            $this->orderitems->add($new_order_item);
        }
    }

    // calculate the total for an order
    function total($num)
    {
        // Retrieve order items for the order
        $order_items = $this->orderitems->some('order',$num);

        // Iterate over order items, and sum their prices
        $total = 0;
        foreach($order_items as $order_item)
        {
            $menu_item = $this->menu->get($order_item->item);
            $item_quantity = $order_item->quantity;

            $total += $menu_item->price*$item_quantity;
        }

        // Update the total in the database
        $order = $this->get($num);
        $order->total = $total;
        $this->update($order);

        // Return the total as a nicely formatted money string
        return sprintf("$%.2f",$total);
    }

    // retrieve the details for an order
    function details($num)
    {

    }

    // cancel an order
    function flush($num)
    {

    }

    // validate an order
    // it must have at least one item from each category
    function validate($num)
    {
        return false;
    }

}
