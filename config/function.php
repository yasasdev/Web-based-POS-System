<?php

session_start();

require 'dbconnection.php';

// Input field validation
function validate($inputData){
    global $conn;
    $validatedData = mysqli_real_escape_string($conn, $inputData);
    return trim($validatedData);
}

// Redirect from one page to another with a message (status)
function redirect($url, $status){
    $_SESSION['status'] = $status;
    header('Location: '.$url);
    exit(0);
}

// Display messages or status after ant process
function alertMessage(){
    if(isset($_SESSION['status'])){
         echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6>'.$_SESSION['status'].'</h6>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
        unset($_SESSION['status']);
    }
}

// Inserting Records
function insert($tableName, $data){

    global $conn;

    $table = validate($tableName);
    $columns = array_keys($data);
    $values = array_values($data);
    $finalColumn = implode(',', $columns);
    $finalValues = "'".implode("', '", $values)."'";

    $query = "INSERT INTO $table ($finalColumn) VALUES ($finalValues)";
    $result = mysqli_query($conn, $query);
    return $result;
}

// Updating Records
function update($tableName, $id, $data){

    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $updateDataString = "";

    foreach($data as $column => $value){
        $updateDataString .= $column.'='."'$value', ";
    }

    $finalUpdateData = substr(trim($updateDataString),0,-1);

    $query = "UPDATE $table SET $finalUpdateData WHERE id='$id'";
    $result = mysqli_query($conn, $query);
    return $result;
}

function getAll($tableName, $status = NULL){

    global $conn;

    $table = validate($tableName);
    $status = validate($status);

    if($status == 'status'){
        $query = "SELECT * FROM $table WHERE status='0'";
    }
    else
    {
        $query = "SELECT * FROM $table";
    }
    return mysqli_query($conn, $query);
}

function getById($tableName, $id){

    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $query = "SELECT * FROM $table WHERE id='$id' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($result){
        
        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_assoc($result);
            $response = [
                'status' => 200,
                'data' => $row,
                'message' =>  'Record Found!'
            ];
            return $response;
        }
        else {
            
            $response = [
                'status' => 404,
                'message' =>  'No Data Found!'
            ];
            return $response;

        }
    }
    else {

        $response = [
            'status' => 500,
            'message' => 'Something Went Wrong!'
        ];
        return $response;

    }
}

// Delete Data
function delete($tableName, $id){

    global $conn;

    $table = validate($tableName);
    $id = validate($id);

    $query = "DELETE FROM $table WHERE id='$id' LIMIT 1";
    $result = mysqli_query($conn, $query);
    return $result;
}

function checkParamId($type){
    if(isset($_GET[$type])){
        if($_GET[$type] != ''){
            return $_GET[$type];
        }
        else{
            return '<h5>No Id Found</h5>';
        }
    }else {
        return '<h5>No Id Given</h5>';
    }
}

function logoutSession(){
    unset($_SESSION['loggedIn']);
    unset($_SESSION['loggedInUser']);
}

function jsonResponse($status, $status_type, $message){

    $response = [
        'status' => $status,
        'status_type' => $status_type,
        'message' => $message
    ];
    echo json_encode($response);
    return;

}

function getCount($tableName)
{
    global $conn;

    $table = validate($tableName);

    $query = "SELECT * FROM $table";
    $query_run = mysqli_query($conn, $query);
    if($query_run){
        $totalCount = mysqli_num_rows($query_run);
        return $totalCount;
    }
    else {
        return 'Something Went Wrong!';
    }
}

function getTotalAmount($orderDate, $paymentStatus){

    global $conn;

    if($orderDate != '' && $paymentStatus == ''){
        $total = mysqli_query($conn, "SELECT SUM(total_amount) AS total_amount_sum FROM orders WHERE order_date='$orderDate' ");
    }
    else if($orderDate == '' && $paymentStatus != ''){
        $total = mysqli_query($conn, "SELECT SUM(total_amount) AS total_amount_sum FROM orders WHERE payment_mode='$paymentStatus' ");
    }
    else if($orderDate != '' && $paymentStatus != ''){
        $total = mysqli_query($conn, "SELECT SUM(total_amount) AS total_amount_sum FROM orders WHERE order_date='$orderDate' AND payment_mode='$paymentStatus' ");
    }
    else{
        $total = mysqli_query($conn, "SELECT SUM(total_amount) AS total_amount_sum FROM orders");
    }
    
    if($total){
        if(mysqli_num_rows($total) > 0){
            $row = mysqli_fetch_assoc($total);
            $total_amount_sum = $row['total_amount_sum'];
            echo "<script>
                    document.getElementById('totalAmount').innerText = 'Total Sales: Rs.'+'$total_amount_sum /=';
                </script>";
        } else {
            echo "<script>
                    document.getElementById('totalAmount').innerText = 'Total Sales: Rs.'+' 0 /=';
                </script>";
        }
    } else {
        echo "<script>
                document.getElementById('totalAmount').innerText = '0';
            </script>";
    }
}

?>