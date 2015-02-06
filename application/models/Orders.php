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
        // // Retrieve the CodeIgniter instance & load the Orderitems model.
        // $CI = &get_instance();
        // $CI->load->model('orderitems');

        // If a previous order item exists, update it; create a new order item
        // otherwise.
        if($this->orderitems->exists($num, $code))
        {
            // Get and update an old order item.
            $old_order_item = $this->orderitems->get($num, $code);
            $old_order_item->quantity += 1;
            $this->orderitems->update($old_order_item);
        }
        else
        {
            // Create and add a new order item.
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
        // // Retrieve the CodeIgniter instance & load the Orderitems model.
        // $CI = &get_instance();
        // $CI->load->model('orderitems');

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
        // // Retrieve the CodeIgniter instance & load the Orderitems model.
        // $CI = &get_instance();
        // $CI->load->model('orderitems');

        // Retrieve all items associated with the order
        $items = $this->orderitems->some('order', $num);

        // Prepare the items for display; make them usable for the show_order
        // view
        $display_items = array();
        foreach($items as $item)
        {
            $display_item = new StdClass;
            $display_item->code     = $item->item;
            $display_item->quantity = $item->quantity;
            $display_items[] = $display_item;
        }

        return $display_items;
    }

    // cancel an order
    function flush($num)
    {
        // Update the order to cancelled status
        $order = $this->orders->get($num);
        $order->status = 'x';
        $this->orders->update($order);

        // Delete all order items related to the order
        $this->orderitems->delete_some($num);
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num)
    {
        // // Retrieve the CodeIgniter instance & load the Menu model.
        // $CI = &get_instance();
        // $CI->load->model('menu');

        // Set of existing item categories in order
        $order_categories = array();

        // Set of existing item categories in menu
        $menu_categories = array();

        // Populate the order_categories set
        $order_items = $this->orderitems->some('order', $num);
        foreach($order_items as $num)
        {
            $menu_item = $this->menu->get($num->item);
            $order_categories[$menu_item->category] = true;
        }

        // Populate the menu_categories set
        $menu_items = $this->menu->all();
        foreach($menu_items as $menu_item)
        {
            $menu_categories[$menu_item->category] = true;
        }

        // Compare the sets for equality (validate) & return
        return ($order_categories == $menu_categories);
    }

}
