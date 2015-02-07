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

    /**
     * adds the menu item associated with $code to the order associated with
     *   $num.
     *
     * @param {Number} $num primary key of the order to associate the new order
     *   item with.
     * @param {Number} $code primary key of the menu item to add to the order.
     */
    function add_item($num, $code)
    {
        // Retrieve the CodeIgniter instance & load the appropriate models
        $CI = &get_instance();
        $CI->load->model('orderitems');

        // If a previous order item exists, update it; create a new order item
        // otherwise.
        if($CI->orderitems->exists($num, $code))
        {
            // Get and update an old order item.
            $old_order_item = $CI->orderitems->get($num, $code);
            $old_order_item->quantity += 1;
            $CI->orderitems->update($old_order_item);
        }
        else
        {
            // Create and add a new order item.
            $new_order_item = $CI->orderitems->create();
            $new_order_item->order    = $num;
            $new_order_item->item     = $code;
            $new_order_item->quantity = 1;
            $CI->orderitems->add($new_order_item);
        }
    }

    /**
     * calculates the total of an order, and updates it in the database. returns
     *   a nicely formatted money string.
     *
     * @param  {Number} $num primary key of the order to associate the new order
     *   item with.
     *
     * @return a nicely formatted money string of the total.
     */
    function total($num)
    {
        // Retrieve the CodeIgniter instance & load the appropriate models
        $CI = &get_instance();
        $CI->load->model('menu');
        $CI->load->model('orderitems');

        // Retrieve order items for the order
        $order_items = $CI->orderitems->some('order',$num);

        // Iterate over order items, and sum their unit prices * quantity
        $total = 0;
        foreach($order_items as $order_item)
        {
            $menu_item = $CI->menu->get($order_item->item);
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

    /**
     * retrieve the the order items of the order as an array.
     *
     * @param  {Number} $num primary key of the order to associate the new order
     *   item with.
     *
     * @return {Array} array of order items associated with the order.
     */
    function details($num)
    {
        // Retrieve the CodeIgniter instance & load the appropriate models
        $CI = &get_instance();
        $CI->load->model('menu');
        $CI->load->model('orderitems');

        // Retrieve all items associated with the order
        $items = $CI->orderitems->some('order', $num);

        // Prepare the items for display; make them usable for the show_order
        // view
        $display_items = array();
        foreach($items as $item)
        {
            $menu_item = $CI->menu->get($item->item);

            $display_item = new StdClass;
            $display_item->code      = $item->item;
            $display_item->name      = $menu_item->name;
            $display_item->unitprice = '$'.$menu_item->price;
            $display_item->quantity  = $item->quantity;
            $display_items[] = $display_item;
        }

        return $display_items;
    }

    /**
     * cancels the order; updates the status of the order to canceled, and
     *   removes all menu items associated with it.
     *
     * @param  {Number} $num number of the order that is being canceled.
     *
     * @return redirects the user to the home page.
     */
    function flush($num)
    {
        // Update the order to canceled status
        $order = $this->get($num);
        $order->status = 'x';
        $this->update($order);

        // Delete all order items related to the order
        $this->orderitems->delete_some($num);
    }

    /**
     * validate an order; it must have at least one item from each category.
     *   returns true if the order is valid and may be purchased; false
     *   otherwise.
     *
     * @param  {Number} $num primary key of the order to associate the new order
     *   item with.
     *
     * @return true if the order is valid and may be purchased; false otherwise.
     */
    function validate($num)
    {
        // Retrieve the CodeIgniter instance & load the Menu model.
        $CI = &get_instance();
        $CI->load->model('menu');
        $CI->load->model('orderitems');

        // Set of existing item categories in order
        $order_categories = array();

        // Set of existing item categories in menu
        $menu_categories = array();

        // Populate the order_categories set
        $order_items = $CI->orderitems->some('order', $num);
        foreach($order_items as $order_item)
        {
            $menu_item = $CI->menu->get($order_item->item);
            $order_categories[$menu_item->category] = true;
        }

        // Populate the menu_categories set
        $menu_items = $CI->menu->all();
        foreach($menu_items as $menu_item)
        {
            $menu_categories[$menu_item->category] = true;
        }

        // Compare the sets for equality (validate) & return
        return ($order_categories == $menu_categories);
    }

}
