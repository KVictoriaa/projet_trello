<?php require_once "template_top.php"; ?>
<h1 class="centered-title">Sign In</h1>
<div class="login-wrapper">

<script src="js/signIn.js" type="text/javascript"></script>

    <form action="user/login" method="post" id="form">
        <div class="align-items-center">
            <div class="my-2">
                <label class="sr-only" for="mail">Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-user"></i></div>
                    </div>
                    <input type="email" name="mail" id="mail" class="form-control" value="<?= $mail ?>" placeholder="Email">
                </div>
            </div>
            <div class="my-2">
                <label class="sr-only" for="password">Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-lock"></i></div>
                    </div>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                </div>
            </div>
            <div class="my-1">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
    <?php if (count($errors) != 0): ?>
        <div class='errors'>
            <p>Please correct the following error(s) :</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
<?php require_once "template_bottom.php"; ?>
