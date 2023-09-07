<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 5px;
        }

        .table-wrapper {
            display: inline-block;
            width: 49%;
            vertical-align: top;
        }
        .no-border{
            border: none;
        }
    </style>
</head>
<body>
<h2 style="text-align: center">Petshop</h2>
<p style="text-align: right">Invoice No: {{$order->payment->uuid}}</p>
<p style="text-align: right">{{$created}}</p>
<br>
<div class="table-wrapper">
            <p>Customer Name: {{$order->user->fullName}}</p>
            <p>Email: {{$order->user->email}}</p>
            <p>Phone No: {{$order->user->phone_number}}</p>
            <p>Address: {{$order->user->address}}</p>
</div>

<div class="table-wrapper">
    <p>Billing: {{$address->billing}}</p>
    <p>Shipping: {{$address->shipping}}</p>
    <br>
    <p>Payment Method: {{$paymentType}}</p>
</div>
<div>
    <table>
        <thead>
        <tr>
            <th>Product UUID</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
        </tr>
        </thead>
        <tbody>
        @foreach($productsAndQuantity as $single)

            <tr>
                <td>{{ $single['product']->uuid }} </td>
                <td>{{ $single['product']->title }} </td>
                <td>{{ $single['product']->price }} </td>
                <td>{{ $single['quantity'] }} </td>
                <td>{{ number_format($single['product']->price * $single['quantity'],2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" class="no-border"></td>
            <td class="subtotal">Subtotal:</td>
            <td>{{$subtotal}}</td>
        </tr>
        <tr>
            <td colspan="3" class="no-border"></td>
            <td>Delivery Fee:</td>
            <td>{{$order->delivery_fee}}</td>
        </tr>
        <tr>
            <td colspan="3" class="no-border"></td>
            <td class="subtotal">Total:</td>
            <td>{{$total}}</td>
        </tr>
        </tfoot>

    </table>
</div>
</body>
</html>
