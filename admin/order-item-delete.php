<?php

require '../config/function.php';

$paramResult = checkParamId('index');
if(is_numeric($paramResult))
{
    $indexValue = validate($paramResult);

    if(isset($_SESSION['productItems']) && isset($_SESSION['productItemIds'])){

        unset($_SESSION['productItems'][$indexValue]);
        unset($_SESSION['productItemIds'][$indexValue]);

        redirect('orders-create.php','Item Removed Successfully');
    }else {
        redirect('orders-create.php','There is no Items');
    }
}
else {
    redirect('orders-create.php','Something Went Wrong!');
}

?>