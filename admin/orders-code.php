<?php
include('../config/function.php');

if(!isset($_SESSION['productItems'])){
    $_SESSION['productItems'] = [];
}
if(!isset($_SESSION['productItemIds'])){
    $_SESSION['productItemIds'] = [];
}

if(isset($_POST['addItem']))
{
    $productId = validate($_POST['product_id']);
    $quantity = validate($_POST['quantity']);
    $sellingPrice = validate($_POST['selling']);

    $checkProduct = mysqli_query($conn, "SELECT * FROM products WHERE id='$productId' LIMIT 1");
    if($checkProduct){
        if(mysqli_num_rows($checkProduct) > 0){
            $row = mysqli_fetch_assoc($checkProduct);
            if($row['quantity'] < $quantity){
                redirect('orders-create.php','Only '.$row['quantity'].' quantity available!');
            }   

            $productData = [
                'product_id' => $row['id'],
                'item_name' => $row['item_name'],
                'image' => $row['image'],
                'price' => $sellingPrice,
                'quantity' => $quantity,
            ];

            if(!in_array($row['id'], $_SESSION['productItemIds'])){
                array_push($_SESSION['productItemIds'],$row['id']);
                array_push($_SESSION['productItems'],$productData);
            }else {
                foreach($_SESSION['productItems'] as $key => $prodSessionItem){
                    if($prodSessionItem['product_id'] == $row['id']){
                        $newQuantity = $prodSessionItem['quantity'] + $quantity;

                        $productData = [
                            'product_id' => $row['id'],
                            'item_name' => $row['name'],
                            'image' => $row['image'],
                            'price' => $sellingPrice,
                            'quantity' => $newQuantity,
                        ];

                        $_SESSION['productItems'][$key] =$productData;
                    }
                }
            }
            redirect('orders-create.php','Item Added '.$row['name']);
        }
        else {
            redirect('orders-create.php','No such product found');
        }
    }
    else{
        redirect('order-create.php','Something Went Wrong!');
    }
}

if(isset($_POST['productIncDec']))
{
    $productId = validate($_POST['product_id']);
    $quantity = validate($_POST['quantity']);

    $flag = false;
    foreach($_SESSION['productItems'] as $key => $item){
        if($item['product_id'] == $productId){
            $flag = true;
            $_SESSION['productItems'][$key]['quantity'] = $quantity;
        }
    }

    if($flag){
        jsonResponse(200, 'Success', 'Quantity Updated');
    }else{
        jsonResponse(500, 'Error', 'Something Went Wrong! please refresh.');
    }
}

if(isset($_POST['proceedToPlaceBtn']))
{
    $phone = validate($_POST['cphone']);
    $payment_mode = validate($_POST['payment_mode']);

    $invoNumber = 0;
    $query = "SELECT MAX(invoiceId) AS maxInvoiceId FROM invoice;";
    $result = mysqli_query($conn, $query);

    if($result){
        $row = mysqli_fetch_assoc($result);
        if($row){
            $maxInvoiceId = $row['maxInvoiceId'];
            if($maxInvoiceId !== null) {
                $invoNumber = $maxInvoiceId + 1;
            } else {
                // Handle case when there are no rows in the table
                $invoNumber = 1;
            }
        }
    } else {
        // Handle query failure
        echo "Query failed: " . mysqli_error($conn);
    }

    $insertQuery = "INSERT INTO invoice (invoiceId) VALUES ($invoNumber);";
    if (mysqli_query($conn, $insertQuery)) {
        // echo "New record created successfully with invoiceId: $invoNumber";
    } else {
        echo "Error: " . $insertQuery . "<br>" . mysqli_error($conn);
    }


    //Checking for Customer
    $checkCustomer = mysqli_query($conn, "SELECT * FROM customers WHERE phone='$phone' LIMIT 1");
     if($checkCustomer){
         if(mysqli_num_rows($checkCustomer) > 0)
         {
            $_SESSION['invoice_no'] = $invoNumber;
            $_SESSION['cphone'] = $phone;
            $_SESSION['payment_mode'] = $payment_mode;
            jsonResponse(200, 'success', 'Customer Found');
         }
         else {
            $_SESSION['cphone'] = $phone;
            jsonResponse(404, 'warning', 'Customer Not Found');
        }
     }
     else {
        jsonResponse(500, 'error', 'Something Went Wrong');
    }
}

if(isset($_POST['saveCustomerBtn']))
{
    $name = validate($_POST['name']);
    $phone = validate($_POST['phone']);
    $email = validate($_POST['email']);

    if($name != '' && $phone != ''){
        $data = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ];
        $result = insert('customers', $data);
        if($result){
            jsonResponse(200, 'Success', 'Customer Created Successfully');
        }
        else{
            jsonResponse(500, 'error', 'Something Went Wrong');
        }
    }
    else{
        jsonResponse(422, 'warning', 'Please fill required fields');
    }
}

if(isset($_POST['saveOrder']))
{
    $phone = validate($_SESSION['cphone']);
    $invoice_no = validate($_SESSION['invoice_no']);
    $payment_mode = validate($_SESSION['payment_mode']);
    $order_placed_by_id = $_SESSION['loggedInUser']['user_id'];

    $checkCustomer = mysqli_query($conn, "SELECT * FROM customers WHERE phone='$phone' LIMIT 1");
    if(!$checkCustomer){
        jsonResponse(500, 'error', 'Something Went Wrong!');
    }

    if(mysqli_num_rows($checkCustomer) > 0){
        $customerData = mysqli_fetch_assoc($checkCustomer);

        if(!isset($_SESSION['productItems'])){
            jsonResponse(404, 'warning', 'No Items to place the Order!');
        }

        $sessionProducts = $_SESSION['productItems'];

        $totalAmount = 0;
        foreach($sessionProducts as $amtItem){
            $totalAmount += $amtItem['price'] * $amtItem['quantity'];
        }

        $data = [
            'customer_id' => $customerData['id'],
            'tracking_no' => rand(11111,99999),
            'invoice_no' => $invoice_no,
            'total_amount' => $totalAmount,
            'order_date' => date('Y-m-d'),
            'order_status' => 'Booked',
            'payment_mode' => $payment_mode,
            'order_placed_by_id' => $order_placed_by_id
        ];
        $result = insert('orders', $data);
        $lastOrderId = mysqli_insert_id($conn);

        foreach($sessionProducts as $prodItem){

            $productId = $prodItem['product_id'];
            $price = $prodItem['price'];
            $quantity = $prodItem['quantity'];

            // Inserting order Items
            $dataOrderItem = [
                'order_id' => $lastOrderId,
                'product_id' => $productId,
                'price' => $price,
                'quantity' => $quantity,
            ];
            $orderItemQuery = insert('order_items', $dataOrderItem);

            // Checking for the books quantity, decreasing quantity and making total quantity
            $chechProductQuantityQuery = mysqli_query($conn, "SELECT * FROM products WHERE id='$productId'");
            $productQtyData = mysqli_fetch_assoc($chechProductQuantityQuery);
            $totalProductQuantity = $productQtyData['quantity'] - $quantity;

            $dataUpdate = [
                'quantity' => $totalProductQuantity
            ];
            $updateProductQty = update('products', $productId, $dataUpdate);
        }

        unset($_SESSION['productItemIds']);
        unset($_SESSION['productItems']);
        unset($_SESSION['cphone']);
        unset($_SESSION['payment_mode']);
        unset($_SESSION['invoice_no']);

        jsonResponse(200, 'success', 'Order Placed Successfully');
    }
    else {
        jsonResponse(404, 'warning', 'No Customer Found!');
    }
}

?>