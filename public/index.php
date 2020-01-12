<?php require __DIR__ . '/views/header.php';

if (!isLoggedIn()) {
    redirect('/');
}

// Include user and userID
$userID = (int) $_SESSION['user']['id'];
$user = getUserById((int) $userID, $pdo);

$posts = getAllPosts($pdo); ?>

<div class="feed-container">

    <?php foreach ($posts as $post) : ?>

        <article>

            <?php $avatar = $post['avatar']; ?>
            <?php $postID = $post['id']; ?>
            <?php $likes = numberOfLikes((int) $postID, $pdo); ?>
            <?php $isLiked = isLiked((int) $userID, (int) $postID, $pdo); ?>

            <div class="post-user-container">

                <img class="post-avatar" src="<?php echo ($post['avatar'] !== null) ? "/uploads/avatar/" . $post['avatar'] : "/uploads/avatar/placeholder.png"; ?>">

                <h3><?php echo $post['username']; ?></h3>

            </div>

            <!-- POST IMAGE -->
            <img class="post-img" src="<?php echo '/uploads/posts/' . $post['filename']; ?>" id="<?php echo $post['id']; ?>">


            <!-- POST LIKE, COMMENT, EDIT, DELETE BUTTONS -->
            <div class="post-buttons-container">

                <div class="post-buttons">

                    <!-- LIKE BUTTON -->
                    <form class="form-like" id="<?php echo $postID; ?>" action="app/posts/like.php" method="POST">

                        <input type="hidden" name="id" value="<?php echo $postID; ?>">

                        <button class="like-button" type="submit" id="<?php echo $postID; ?>">

                            <i class="<?php echo ($isLiked !== true) ? "far fa-heart" : "fas fa-heart"; ?>"></i>

                            <!-- NUMBER OF LIKES -->
                            <?php echo $likes; ?>

                        </button>

                    </form>

                    <button class="comment-button">
                        <i class="far fa-comment-alt"></i>
                    </button>

                </div>

                <!-- IF POST USER EQUALS LOGGED IN USER, SHOW EDIT AND DELETE BUTTONS ON THEIR POSTS-->
                <div class="edit-buttons">

                    <?php if (($_SESSION['user']['id'] === $post['user_id'])) : ?> <a href="../../edit-post.php?id=<?php echo $post['id']; ?>"><i class="far fa-edit"></i></a>
                        <a href="../posts/delete.php?id=<?php echo $post['id']; ?>"><i class="far fa-trash-alt"></i></a>
                    <?php endif; ?>

                </div>

            </div>

            <p><?php echo $post['caption']; ?></p>
            <p class="post-date">
                <?php
                $date = explode(" ", $post['date_created']);
                echo $date[0];
                ?></p>


        </article>
    <?php endforeach; ?>

    <?php require __DIR__ . '/views/footer.php'; ?>
