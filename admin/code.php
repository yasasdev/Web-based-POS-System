<?php

require '../config/function.php';

if(isset($_POST['saveAdmin'])){

    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $phone = validate($_POST['phone']);
    $is_ban = validate($_POST['is_ban']) == true ? 1:0;

    if($name != '' && $email != '' && $password != ''){

        $emailCheck = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('admin-create.php','Email already exist.');
            }
        }

        $bcrypt_password = password_hash($password, PASSWORD_BCRYPT);

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $bcrypt_password,
            'phone' => $phone,
            'is_ban' => $is_ban
        ];
        $results = insert('admins', $data);
        if($results){
            redirect('admins.php','Admin Created Successfully!');
        }else {
            redirect('admin-create.php','Something Went Wrong!');
        }

    }else {
        redirect('admin-create.php','Please fill required fields.');
    }

}

if(isset($_POST['updateAdmin']))
{
    $adminId = validate($_POST['adminId']);

    $adminData = getById('admins', $adminId);
    if($adminData['status'] != 200){
        redirect('admins-edit.php?id='.$adminId,'Please fill required fields.');
    }

    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $phone = validate($_POST['phone']);
    $is_ban = isset($_POST['is_ban']) == true ? 1 : 0;

    $EmailCheckQuery = "SELECT * FROM admins WHERE email='$email' AND id!='$adminId'";
    $checkResult = mysqli_query($conn, $EmailCheckQuery);
    if($checkResult){
        if(mysqli_num_rows($checkResult) > 0){
            redirect('admins-edit.php'.$adminId,'Email is already used by an another user!');
        }
    }

    if($password != ''){
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    } else {
        $hashedPassword = $adminData['data']['password'];
    }

    if($name != '' && $email != ''){
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'phone' => $phone,
            'is_ban' => $is_ban
        ];

        $result = update('admins', $adminId, $data);
        if($result){
            redirect('admins-edit.php?id='.$adminId, 'Admin Updated Successfully!');
        } else {
            redirect('admins-edit.php?id='.$adminId, 'Something Went Wrong!');
        }
    } else {
        redirect('admin-create.php', 'Please fill required fields.');
    }
}

if(isset($_POST['saveCategory']))
{
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']) == true ? 1:0;

    $data = [
        'name' => $name,
        'description' => $description,
        'status' => $status
    ];
    $results = insert('categories', $data);
    if($results){
        redirect('categories.php','Category Added Successfully!');
    }else {
        redirect('categories-create.php','Something Went Wrong!');
    }

}

if(isset($_POST['updateCategory']))
{
    $categoryId = validate($_POST['categoryId']);

    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']) == true ? 1:0;

    $data = [
        'name' => $name,
        'description' => $description,
        'status' => $status
    ];
    $results = update('categories', $categoryId, $data);
    if($results){
        redirect('categories-edit.php?id='.$categoryId,'Category Updated Successfully!');
    }else {
        redirect('categories-edit.php?id='.$categoryId,'Something Went Wrong!');
    }
}

if(isset($_POST['saveProduct']))
{
    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $price = validate($_POST['price']);
    $highMargin = validate($_POST['highMargin']);
    $lowMargin = validate($_POST['lowMargin']);
    $supplier = validate($_POST['supplier']);
    $quantity = validate($_POST['quantity']);
    $status = isset($_POST['status']) == true ? 1:0;

    if($_FILES['image']['size'] > 0)
    {
        $path = "../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $filename = time().'.'.$image_ext;

        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);

        $finalImage = "assets/uploads/products/".$filename;
    }
    else {
        $finalImage = '';
    }

    $data = [
        'category_id' => $category_id,
        'item_name' => $name,
        'description' => $description,
        'cost' => $price,
        'quantity' => $quantity,


        'high_margin' => $highMargin,
        'low_margin' => $lowMargin,
        'supplier' => $supplier,


        'image' => $finalImage,
        'status' => $status
    ];
    $results = insert('products', $data);
    if($results){
        redirect('products.php','Product Added Successfully!');
    }else {
        redirect('products-create.php','Something Went Wrong!');
    }
}

if(isset($_POST['updateProduct']))
{
    $product_id = validate($_POST['product_id']);

    $productData = getById('products',$product_id);
    if(!$productData){
        redirect('products.php','No such product found');
    }

    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $price = validate($_POST['price']);
    $highMargin = validate($_POST['highMargin']);
    $lowMargin = validate($_POST['lowMargin']);
    $supplier = validate($_POST['supplier']);
    $quantity = validate($_POST['quantity']);
    $status = isset($_POST['status']) == true ? 1:0;

    if($_FILES['image']['size'] > 0)
    {
        $path = "../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $filename = time().'.'.$image_ext;

        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);

        $finalImage = "assets/uploads/products/".$filename;

        $deleteImage = "../".$productData['data']['image'];
        if(file_exists($deleteImage))
        {
            unlink($deleteImage);
        }
    }
    else {
        $finalImage = $productData['data']['image'];
    }

    $data = [
        'category_id' => $category_id,
        'item_name' => $name,
        'description' => $description,
        'cost' => $price,
        'quantity' => $quantity,
        'high_margin' => $highMargin,
        'low_margin' => $lowMargin,
        'supplier' => $supplier,
        'image' => $finalImage,
        'status' => $status
    ];
    $results = update('products', $product_id, $data);

    if($results){
        redirect('products-edit.php?id='.$product_id,'Product Updated Successfully!');
    }else {
        redirect('products-edit.php?id='.$product_id,'Something Went Wrong!');
    }
}

if(isset($_POST['saveCustomer']))
{
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $description = validate($_POST['description']);
    $status = validate($_POST['status']);

    if($name != '')
    {
        $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('customers.php','Email is already exists!');
            }
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'description' => $description,
            'status' => $status
        ];

        $result = insert('customers',$data);
        if($result){
            redirect('customers.php','Customer Created Successfully');
        }
        else {
            redirect('customers.php','Something Went Wrong');
        }
    }
    else {
        redirect('customers.php','Please fill required fields');
    }
}

if(isset($_POST['updateCustomer']))
{
    $customerId = validate($_POST['customerId']);

    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']);

    if($name != '')
    {
        $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email' AND id!='$customerId'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('customers-edit.php?id='.$customerId,'Email is already exists!');
            }
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'description' => $description,
            'status' => $status
        ];

        $result = update('customers',$customerId ,$data);
        if($result){
            redirect('customers.php?id='.$customerId,'Customer Updated Successfully');
        }
        else {
            redirect('customers-edit.php?id='.$customerId,'Something Went Wrong');
        }
    }
    else {
        redirect('customers-edit.php?id='.$customerId,'Please fill required fields');
    }
}

if (isset($_POST['productId'])) {
    $productId = intval($_POST['productId']); // Get the productId from POST data

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT high_margin, low_margin FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($high_margin, $low_margin);
    $stmt->fetch();
    $stmt->close();

    // Return the high_margin and low_margin values as JSON
    echo json_encode(array('high_margin' => $high_margin, 'low_margin' => $low_margin));
}


?>