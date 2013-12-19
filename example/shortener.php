<?php if(!array_key_exists('url', $_POST) || !$shorty->isValidURL(@$_POST['url'])): ?>
    <?php if(array_key_exists('url', $_POST) && !$shorty->isValidURL($_POST['url'])) {
        echo '<h3>Invalid URL!</h3>';
    }
    ?>
    <h3>Shorten your URL:</h3>
    <form name="urlShortener" method="POST" action="<?= full_url($_SERVER); ?>">
        <input type="text" name="url">
        <input type="submit" name="submit" value="shorten!">
    </form>
<?php else: ?>
    <?php $code = $shorty->shorten($_POST['url']); ?>
    You can now use this link: <a href="<?= full_url($_SERVER).'?q='.$code; ?>"><?= full_url($_SERVER).'?q='.$code; ?></a>
<?php endif; ?>