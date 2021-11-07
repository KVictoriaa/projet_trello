<?php function error($errors, $title="") { ?>
    <?php if (count($errors) != 0): ?>
        <div class='errors'>
            <p><?= $title ?></p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
        </div>
    <?php endif; ?>
<?php  } ?>