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

    // constructor
    function __construct()
    {
        parent::__construct();
    }

    /**
     * creates a new order record in the database. the new order has no order
     *   items associated with it, and it is automatically assigned the next
     *   available order number.
     *
     * @return  redirects the user to the display_menu page.
     */
    function neworder()
    {

        // Get highest existing order number
        $order_num = $this->orders->highest();

        // Create a new order
        $new_order = $this->orders->create();
        $new_order->num    = $order_num+1;
        $new_order->date   = date('Y-m-d H:i:s');
        $new_order->status = 'a';

        // Save the new order to the database
        $this->orders->add($new_order);

        redirect('/order/display_menu/' . $order_num);
    }

    /**
     * shows the menu page in the context of an order; the menu page will
     *   display the order's order number, and the current total.
     *
     * @param  {Number} $order_num order number of the order to display this
     *   menu in the context of. if it is null, a new order will be started.
     *
     * @return  shows the dusplay_menu page in the context of the order
     *   associated with the $order_num parameter.
     */
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
        $this->data['title']     = 'order #'.$order_num.', '
            .$this->orders->total($order_num);

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

    /**
     * returns all the menu item records associated with the $category
     *   parameter.
     *
     * @param  {Character} $category character specifying which category of menu
     *   items to return.
     *
     * @return all menu item records associated with the $category parameter.
     */
    function make_column($category)
    {
        return $this->menu->some('category',$category);
    }

    /**
     * adds the menu item (with id $item) to the order (with id $order_num).
     *
     * @param {Number} $order_num number of the order to add the menu item to.
     * @param {Number} $item id of the menu item to add to the order.
     */
    function add($order_num, $item)
    {
        $this->orders->add_item($order_num, $item);
        redirect('/order/display_menu/' . $order_num);
    }

    /**
     * loads the checkout page in the context of the order associated with the
     *   parameter $order_num.
     *
     * @param  {Number} $order_num number of the order to display the checkout
     *   page in the context of.
     *
     * @return shows the checkout page in the context of the specified order.
     */
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

    /**
     * proceed with checkout, and buy the food. updates the status of the order
     *   to complete, and updates the time stamp to when the order was checked
     *   out.
     *
     * @param  {Number} $order_num number of the order that is being checked
     *   out.
     *
     * @return redirects the user to the home page.
     */
    function proceed($order_num)
    {
        // Update the order to complete status
        $order = $this->orders->get($order_num);
        $order->date   = date('Y-m-d H:i:s');
        $order->status = 'c';
        $this->orders->update($order);

        redirect('/');
    }

    /**
     * cancels the order; updates the status of the order to canceled, and
     *   removes all menu items associated with it.
     *
     * @param  {Number} $order_num number of the order that is being canceled.
     *
     * @return redirects the user to the home page.
     */
    function cancel($order_num)
    {
        // Cancel the order
        $this->orders->flush($order_num);

        redirect('/');
    }

}
