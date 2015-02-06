<p class="lead">
    Order # {order_num} for {total}
</p>
<table class="table">
    <tr>
        <th>Code #</th>
        <th>Item Name</th>
        <th>Unit Price</th>
        <th>Quantity</th>
    </tr>
    {items}
    <tr>
        <td>{code}</td>
        <td>{name}</td>
        <td>{unitprice}</td>
        <td>{quantity}</td>
    </tr>
    {/items}
</table>
<div class="row">
    <button onclick="location.href = '/order/proceed/{order_num}';" class="btn btn-large btn-success" {okornot}>Proceed</button>
    <button onclick="location.href = '/order/display_menu/{order_num}';" class="btn btn-large btn-primary">Keep shopping</button>
    <button onclick="location.href = '/order/cancel/{order_num}';" class="btn btn-large btn-danger">Forget about it</button>
</div>
