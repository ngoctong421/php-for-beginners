<?php

require '../includes/init.php';

Auth::requireLogin();

$conn = require '../includes/db.php';

$article      = new Article();
$category_ids = [  ];
$categories   = Category::getAll($conn);

if ("POST" == $_SERVER[ "REQUEST_METHOD" ]) {
    $article->title        = $_POST[ 'title' ];
    $article->content      = $_POST[ 'content' ];
    $article->published_at = $_POST[ 'published_at' ];

    $category_ids = $_POST[ 'category' ] ?? [  ];

    if ($article->create($conn)) {
        $article->setCategories($conn, $category_ids);
        Url::redirect("/admin/article.php?id=$article->id");
    }
}

?>

<?php require '../includes/header.php'; ?>

<h2>New article</h2>

<?php require './includes/article-form.php'; ?>

<?php require '../includes/footer.php'; ?>
