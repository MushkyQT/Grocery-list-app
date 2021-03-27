<?php

if ($signedUp == true) {
    $fatal = "Successfully signed up, please log in.";
    session_unset();
}

?>

<div class="container-md">
    <div class="row justify-content-center">
        <div class="col-10 col-md-8 col-lg-6 col-xl-4 surround text-center">
            <?php echo $fatal ?>
            <form method="post" class="pb-3">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Your username" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Your password" class="form-control">
                </div>
                <button type="submit" name="login" class="btn btn-success">Log In</button>
                <p class="my-1">Or</p>
                <button type="submit" name="signUp" class="btn btn-primary">Sign Up</button>
            </form>
        </div>
    </div>
</div>