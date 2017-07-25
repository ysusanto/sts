<div class="container text-center">
    <div class="box">
        <h2 class="form-header">Sign Up</h2>
        <form class="form-register" method="POST" action="<?php echo site_url() ?>home/signup">
            <?php
            if (isset($error)) {
                echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
            }
            ?>
            <input type="text" class="form-control" name="name" placeholder="Name" style="text-transform: capitalize" autofocus value="<?php if (isset($name)) echo $name ?>">
            <input type="email" class="form-control" name="email" placeholder="Email*" required value="<?php if (isset($email)) echo $email ?>">
            <input type="password" class="form-control" name="password" placeholder="Password*" required>
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password*" required>
            <input type="password" class="form-control" name="admin_password" placeholder="Admin Password">
            <input type="submit" class="btn btn-lg btn-primary btn-block" value="Sign Me Up!">
        </form>
    </div>
</div> <!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->
</body>
</html>