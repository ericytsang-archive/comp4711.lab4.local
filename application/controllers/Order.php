<?php

/**
 * Order handler
 *
 * Implement the different order handling usecases.
 *
 * controllers/welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Order extends Application
{

    function __construct()
    {
        parent::__construct();
    }

    // start a new order
    function neworder()
    {

        // Get highest existing order number
        $order_num = $this->orders->highest();

        // Create a new order
        $new_order = $this->orders->create();
        $new_order->num    = $order_num+1;    // new order; highest order number plus 1
        $new_order->date   = date('Y-m-d H:i:s');
        $new_order->status = 'a';

        // Save the new order to the database
        $this->orders->add($new_order);

        redirect('/order/display_menu/' . $order_num);
    }

    // add to an order
    function display_menu($order_num = null)
    {
        if ($order_num == null)
            redirect('/order/neworder');

        // Load the array helper
        $this->load->helper('array');

        // Retrieve the order record
        $order = $this->orders->get($order_num);

        // Pass template parameters
        $this->data['pagebody']  = 'show_menu';
        $this->data['order_num'] = $order_num;
        $this->data['title']     = 'order #'.$order_num.', '.$this->orders->total($order_num);

        // Make the columns
        $this->data['meals']  = $this->make_column('m');
        $this->data['drinks'] = $this->make_column('d');
        $this->data['sweets'] = $this->make_column('s');

        // Inject extra order_num property into arrays
        inject_property($this->data['meals'],  'order_num', $order_num);
        inject_property($this->data['drinks'], 'order_num', $order_num);
        inject_property($this->data['sweets'], 'order_num', $order_num);

        $this->render();
    }

    // make a menu ordering column
    function make_column($category)
    {
        return $this->menu->some('category',$category);
    }

    // add an item to an order
    function add($order_num, $item)
    {
        $this->orders->add_item($order_num, $item);
        redirect('/order/display_menu/' . $order_num);
    }

    // checkout
    function checkout($order_num)
    {
        // Pass template parameters
        $this->data['pagebody']  = 'show_order';
        $this->data['order_num'] = $order_num;
        $this->data['title']     = 'Checking Out';

        // Pass page content parameters
        $this->data['items']   = $this->orders->details($order_num);
        $this->data['total']   = $this->orders->total($order_num);
        $this->data['okornot'] = $this->orders->validate($order_num) ?
            '' : 'disabled';

        $this->render();
    }

    // proceed with checkout
    function proceed($order_num)
    {
        // Update the order to complete status
        $order = $this->orders->get($order_num);
        $order->date   = date('Y-m-d H:i:s');
        $order->status = 'c';
        $this->orders->update($order);

        redirect('/');
    }

    // cancel the order
    function cancel($order_num)
    {
        // Cancel the order
        $this->orders->flush($order_num);

        redirect('/');
    }

}
