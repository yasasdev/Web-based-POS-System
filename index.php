<?php include('includes/header.php'); ?>

    <div class="py-5" style="background-image: url('assets/image/pos.jpg'); background-size: cover;">
        <div class="container my-5">
            <div class="row">
                <div class="col-md-12 py-5 text-center">

                    <?php alertMessage(); ?>

                    <h1 class="mt-3" style="color: white">Sakura Mobile</h1>

                    <?php if(!isset($_SESSION['loggedIn'])) : ?>
                    <a href="login.php" class="btn btn-primary mt-4">LOGIN</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php include('includes/footer.php'); ?>