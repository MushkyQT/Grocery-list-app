<div class="container-md">
    <div class="row justify-content-center">
        <div class="col-10 col-md-8 col-lg-6 col-xl-4 surround text-center">
            <?php echo $fatal ?>
            <form method="post" class="pb-3">
                <div class="form-group">
                    <label for="newUser">Username</label>
                    <input type="text" name="newUser" id="newUser" placeholder="Your username" class="form-control">
                </div>
                <div class="form-group">
                    <label for="newPass">Password</label>
                    <input type="password" name="newPass" id="newPass" placeholder="Your password" class="form-control">
                </div>
                <div class="form-group">
                    <label for="newPassConfirm">Confirm Password</label>
                    <input type="password" name="newPassConfirm" id="newPassConfirm" placeholder="Your password again" class="form-control">
                </div>
                <button type="submit" name="signUp" class="btn btn-primary">Sign Up</button>
            </form>
        </div>
    </div>
</div>