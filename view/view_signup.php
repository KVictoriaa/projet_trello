<?php require_once "template_top.php";  ?>
<h1 class="centered-title">Sign Up</h1>
<div class="login-wrapper">

<script src="js/signup.js" type="text/javascript"></script>

    <form action="user/signup" method="post" id="form">
        <div class="align-items-center">
            <div class="my-2">
                <label class="sr-only" for="mail">Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-user"></i></div>
                    </div>
                    <input type="text" name="mail" id="mail" class="form-control" value="<?= $mail ?>" placeholder="Email">
                </div>
            </div>
            <div class="my-2">
                <label class="sr-only" for="fullname">FullName</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-user"></i></div>
                    </div>
                    <input type="text" name="fullname" id="fullname" class="form-control" value="<?= $fullname ?>" placeholder="Full name">
                </div>
            </div>
            <div class="my-2">
                <label class="sr-only" for="Password">Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-lock"></i></div>
                    </div>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                </div>
            </div>
            <div class="my-2">
                <label class="sr-only" for="passwordconfirm">Password confirm</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-lock"></i></div>
                    </div>
                    <input type="password" name="passwordconfirm" class="form-control" id="passwordconfirm" placeholder="Confirm your password">
                </div>
            </div>
            <div class="my-1">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>


    <?php if (count($errors) != 0): ?>
        <div class='errors'>
            <br><br><p>Please correct the following error(s) :</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

<?php require_once "template_bottom.php"; ?>
