<?php

require '../includes/init.php';

Auth::requireLogin();

$conn = require '../includes/db.php';

$paginator = new Paginator($_GET[ 'page' ] ?? 1, $_GET[ 'limit' ] ?? 4, Article::getTotal($conn));

$articles = Article::getPage($conn, $paginator->limit, $paginator->offset);

?>

<?php require '../includes/header.php'; ?>

<h2>Administration</h2>

<p><a href="new-article.php">New article</a></p>

<?php if (empty($articles)): ?>
    <p>No articles found.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <th>ID</th>
            <th>Title</th>
            <th>Content</th>
            <th>Published at</th>
        </thead>
        <tbody>
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?=htmlspecialchars($article[ 'id' ]); ?></td>
                    <td><a href="article.php?id=<?=$article[ 'id' ]; ?>"><?=htmlspecialchars($article[ 'title' ]); ?></a></td>
                    <td><?=htmlspecialchars($article[ 'content' ]); ?></td>
                    <td>
                        <?php if ($article[ 'published_at' ]): ?>
                            <time datetime="<?=$article[ 'published_at' ]; ?>">
                                <?=$article[ 'published_at' ]; ?>
                            </time>
                        <?php else: ?>
                            Unpublished
                            <button class="publish" data-id="<?=$article[ 'id' ]; ?>">Publish</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php require '../includes/pagination.php'; ?>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>
