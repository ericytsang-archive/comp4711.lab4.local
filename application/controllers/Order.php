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
class Order extends Application {

    function __construct() {
        parent::__construct();
    }

    // start a new order
    function neworder() {

        // Get highest existing order number
        $order_num = $this->orders->highest();

        // Create a new order
        $new_order = $this->orders->create();
        $new_order->num    = $order_num+1;    // new order; highest order number plus 1
        $new_order->date   = date('Y-m-d');
        $new_order->status = 'a';

        // Save the new order to the database
        $this->orders->add($new_order);

        redirect('/order/display_menu/' . $order_num);
    }

    // add to an order
    function display_menu($order_num = null) {
        if ($order_num == null)
            redirect('/order/neworder');

        // Retrieve the order record from the database
        $order = $this->orders->get($order_num);

        // Fill in page parameters
        $this->data['pagebody']  = 'show_menu';
        $this->data['order_num'] = $order_num;
        $this->data['title']     = 'order #'.$order_num.', $'.$this->orders->total($order_num);

        // Make the columns
        $this->data['meals']  = $this->make_column('m');
        $this->data['drinks'] = $this->make_column('d');
        $this->data['sweets'] = $this->make_column('s');

        $this->render();
    }

    // make a menu ordering column
    function make_column($category) {
        return $this->menu->some('category',$category);
    }

    // add an item to an order
    function add($order_num, $item) {
        //FIXME
        redirect('/order/display_menu/' . $order_num);
    }

    // checkout
    function checkout($order_num) {
        $this->data['title'] = 'Checking Out';
        $this->data['pagebody'] = 'show_order';
        $this->data['order_num'] = $order_num;
        //FIXME

        $this->render();
    }

    // proceed with checkout
    function proceed($order_num) {
        //FIXME
        redirect('/');
    }

    // cancel the order
    function cancel($order_num) {
        //FIXME
        redirect('/');
    }

}
