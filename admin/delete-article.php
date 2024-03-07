<?php

require '../includes/init.php';

Auth::requireLogin();

$conn = require '../includes/db.php';

if (isset($_GET[ 'id' ])) {
    $article = Article::getByID($conn, $_GET[ 'id' ]);

    if (!$article) {
        die("article not found");
    }
} else {
    die("id not supplied, article not found");
}

if ("POST" == $_SERVER[ "REQUEST_METHOD" ]) {
    if ($article->delete($conn)) {
        Url::redirect("/admin");
    }
}

?>

<?php require '../includes/header.php'; ?>

<h2>Delete article</h2>
<form method="post">
    <p>Are you sure?</p>

    <button>Delete</button>
    <a href="article.php?id=<?=$article->id; ?>">Cancel</a>
</form>

<?php require '../includes/footer.php'; ?>
