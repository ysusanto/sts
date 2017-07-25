<div class="container text-center">
    <div class="box">
        <form class="form-signin" method="POST" action="<?php echo site_url() ?>home/signin">
            <?php
            if (isset($error)) {
                echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
            }
            ?>
            <input type="email" class="form-control" name="email" placeholder="Email" required autofocus value="<?php if (isset($email)) echo $email ?>">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            <input type="submit" class="btn btn-lg btn-primary btn-block" value="Sign in">
        </form>

<!--        <div class="text-center">
            <h6>Don't have an account? Do create one!</h6>
            <a href="<?php echo site_url() ?>home/register" class="btn btn-default" role="button">Register</a>
        </div>-->
    </div>
</div> <!-- /container -->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<!--<script src="assets/js/ie10-viewport-bug-workaround.js"></script>-->
</body>
</html>