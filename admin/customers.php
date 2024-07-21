<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow">
        <div class="card-header">
            <h4 class="mb-0">Customers
                <a href="customer-create.php" class="btn btn-primary float-end">Add Customer</a>
            </h4>
        </div>
        <div class="card-body">
            <?php alertMessage(); ?>

            <?php
            $customers = getAll('customers');
            if(!$customers){
                echo '<h4>Something Went Wrong!</h4>';
                return false;
            }
            if(mysqli_num_rows($customers) > 0){
            ?>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach($customers as $Item) : ?>
                        <tr>
                            <td><?php echo $Item['id']; ?></td>
                            <td><?php echo $Item['name']; ?></td>
                            <td><?php echo $Item['email']; ?></td>
                            <td><?php echo $Item['phone']; ?></td>
                            <td><?php echo $Item['description']; ?></td>
                            <td>
                                <?php
                                    if($Item['status'] == 1){
                                        echo '<span class="badge bg-danger">Hidden</span>';
                                    }
                                    else {
                                        echo '<span class="badge bg-primary">Visible</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <a href="customers-edit.php?id=<?= $Item['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                                <a 
                                    href="customers-delete.php?id=<?= $Item['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this Customer? ')"
                                >
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                    </tbody>
                </table>
            </div>
            <?php 
            }
            else {
                ?>
                <tr>
                    <h4 class="mb-0">No Records Found</h4>
                </tr>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>