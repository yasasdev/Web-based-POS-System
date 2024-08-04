<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow">
        <div class="card-header">
            <h4 class="mb-0">Products
                <a href="products-create.php" class="btn btn-primary float-end">Add Product</a>
            </h4>
            
            <form action="" method="GET">
                <div class="row g-1">
                    <div class="col-md-4">
                        <input type="text" name="item_name" placeholder="Search" class="form-control" value="<?= isset($_GET['item_name']) ? $_GET['item_name'] : ''; ?>" />
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="products.php" class="btn btn-danger">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <?php alertMessage(); ?>

            <?php

            // Initialize the query
            $query = "SELECT * FROM products ORDER BY id DESC";

            if (isset($_GET['item_name'])) {
                $itemName = validate($_GET['item_name']);

                if ($itemName != '') {
                    // Corrected SQL query to use the validated input
                    $query = "SELECT * FROM products WHERE item_name LIKE '%$itemName%' ORDER BY id DESC";
                }
            }

            // Execute the query
            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo '<h4>Something Went Wrong!</h4>';
                return false;
            }

            if (mysqli_num_rows($result) > 0) {
                ?>

                <div class="table-responsive">  
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Item Name</th>
                                <th>Cost</th>
                                <th>High Margin</th>
                                <th>Low Margin</th>
                                <th>Quantity</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($Item = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?= $Item['id'] ?></td>
                                    <td><img src="../<?= $Item['image']; ?>" style="width:50px;height:50px;" alt="Img" /></td>
                                    <td><?= $Item['item_name'] ?></td>
                                    <td><?= $Item['cost'] ?></td>
                                    <td><?= $Item['high_margin'] ?></td>
                                    <td><?= $Item['low_margin'] ?></td>
                                    <td><?= $Item['quantity'] ?></td>
                                    <td><?= $Item['supplier'] ?></td>
                                    <td>
                                        <?php
                                        if ($Item['status'] == 1) {
                                            echo '<span class="badge bg-danger">Hidden</span>';
                                        } else {
                                            echo '<span class="badge bg-primary">Visible</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="products-edit.php?id=<?= $Item['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                                        <a href="products-delete.php?id=<?= $Item['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php
            } else {
                echo '<h4 class="mb-0">No Records Found</h4>';
            }
            ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
