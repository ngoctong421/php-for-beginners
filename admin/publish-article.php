<?php

require '../includes/init.php';

Auth::requireLogin();

$conn = require '../includes/db.php';

$article = Article::getByID($conn, $_POST[ 'id' ]);

$published_at = $article->publish($conn);

?>

<time datetime="<?=$published_at; ?>">
    <?=$published_at; ?>
</time>