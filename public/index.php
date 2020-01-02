<?php require __DIR__ . '/views/header.php';

if (!isLoggedIn()) {
    redirect('/');
}

// Include user and userID
$userID = (int) $_SESSION['user']['id'];
$user = getUserById((int) $userID, $pdo);

$posts = getAllPosts($pdo); ?>

<article>

    <?php foreach ($posts as $post) : ?>
        <?php $avatar = $post['avatar']; ?>
        <img class="avatar" src="<?php echo ($post['avatar'] !== null) ? "/uploads/avatar/" . $post['avatar'] : "/uploads/avatar/placeholder.png"; ?>">
        <h3><?php echo $post['username']; ?></h3>
        <img src="<?php echo '/uploads/posts/' . $post['filename']; ?>" id="<?php echo $post['id']; ?>">
        <p><?php echo $post['caption']; ?></p>
        <?php if (($_SESSION['user'] === $post['user_id'])) : ?>
            <a class="btn btn-sm btn-danger" href="../../edit-post.php?id=<?php echo $post['id']; ?>">Edit</a>
            <a class="btn btn-sm btn-danger" href="../posts/delete.php?id=<?php echo $post['id']; ?>">Delete</a>
        <?php endif; ?>
        <br>
    <?php endforeach; ?>
</article>

<?php require __DIR__ . '/views/footer.php'; ?>
