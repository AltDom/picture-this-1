<?php

declare(strict_types=1);


if (!function_exists('redirect')) {
    /**
     * Redirect the user to given path.
     *
     * @param string $path
     *
     * @return void
     */
    function redirect(string $path)
    {
        header("Location: ${path}");
        exit;
    }
}


/**
 * Determine if a user is logged in
 *
 * @return boolean
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Checks for errors in $_SESSION and prints them. Then unsets $_SESSION['error']
 *
 * @return boolean
 */
function isError(): bool
{
    if (isset($_SESSION['error'])) {
        return true;
    }
    return false;
}

/**
 * Checks for successes in $_SESSION and prints them. Then unsets $_SESSION['success']
 *
 * @return boolean
 */
function isSuccess(): bool
{
    if (isset($_SESSION['success'])) {
        return true;
    }
    return false;
}

/**
 * Get user data by id
 *
 * @param int $userID
 * @param PDO $pdo
 * @return array
 */
function getUserById(int $userID, PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT * FROM user WHERE id = :user_id');

    if (!$statement) {
        die(var_dump($pdo->errorInfo()));
    }

    $statement->execute([
        ':user_id' => $userID
    ]);

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return $user;
    } else {
        return [];
    }
}

/**
 * Get posts from user
 *
 * @param int $userID
 * @param PDO $pdo
 * @return array
 */
function getPostsByUser(int $userID, PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT * FROM post WHERE user_id = :user_id ORDER BY date_created DESC');

    if (!$statement) {
        die(var_dump($pdo->errorInfo()));
    }

    $statement->execute([
        ':user_id' => $userID
    ]);

    $posts = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $posts;
}


/**
 * Get post by ID from database
 *
 * @param integer $postID
 * @param PDO $pdo
 * @return array
 */
function getPostByID(int $postID, PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT * FROM post WHERE id = :id');

    if (!$statement) {
        die(var_dump($pdo->errorInfo()));
    }

    $statement->execute([
        ':id' => $postID
    ]);

    $post = $statement->fetch(PDO::FETCH_ASSOC);

    return $post;
}


/**
 * Get all posts from users
 *
 * @param PDO $pdo
 * @return array
 */
function getAllPosts(PDO $pdo): array
{
    $statement = $pdo->prepare('SELECT post.id, post.user_id, post.caption, post.filename, post.date_created, user.username, user.avatar FROM post JOIN user ON post.user_id = user.id ORDER BY post.date_created DESC');

    if (!$statement) {
        die(var_dump($pdo->errorInfo()));
    }

    $statement->execute();

    $allPosts = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $allPosts;
}


/**
 * Checks if post is liked by user, returns string
 *
 * @param integer $userID
 * @param integer $postID
 * @param PDO $pdo
 * @return boolean
 */
function isLiked(int $userID, int $postID, PDO $pdo): bool
{
    $statement = $pdo->prepare('SELECT * FROM like WHERE user_id = :user_id AND post_id = :post_id');

    if (!$statement) {
        die(var_dump($pdo->errorInfo()));
    }

    $statement->execute([
        'user_id' => $userID,
        'post_id' => $postID
    ]);

    $isLiked = $statement->fetch(PDO::FETCH_ASSOC);
    return $isLiked ? true : false;
}

/**
 * Get number of likes on post from database
 *
 * @param integer $postID
 * @param PDO $pdo
 * @return integer
 */
function numberOfLikes(int $postID, PDO $pdo): int
{
    $statement = $pdo->prepare('SELECT COUNT(*) FROM like WHERE post_id = :post_id');

    if (!$statement) {
        die(var_dump($pdo->errorInfo()));
    }

    $statement->execute([
        ':post_id' => $postID
    ]);

    $likes = $statement->fetch(PDO::FETCH_ASSOC);

    return (int) $likes["COUNT(*)"];
}

/**
 * Checks if user is owner of post
 *
 * @param array $post
 * @return boolean
 */
function isUser($post): bool
{
    if ($_SESSION['user']['id'] === $post['user_id']) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if id of current post matches post id in database. If true, return post
 *
 * @param array $posts
 * @return void
 */
function getCurrentPost($posts)
{
    $postID = $_GET['id'];
    if (isset($_GET['id'])) {
        foreach ($posts as $post) {
            if ($postID === $post['id']) {
                return $postID;
            }
        }
    }
}

if (!function_exists('getNumFollowers')) {
    /**
     * Get the number of followers by user ID
     *
     * @param int $userID
     * @param PDO $pdo
     * @return int
     */
    function getNumFollowers(int $userID, PDO $pdo): int
    {
        $statement = $pdo->prepare("SELECT * FROM follows WHERE id_following = :id_following");
        $statement->execute([
            ":id_following" => $userID
        ]);
        $followers = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($followers===false) {
            return 0;
        } else {
            $numFollowers = 0;
            foreach ($followers as $following) {
                $numFollowers++;
            }
            return $numFollowers;
        }
    }
}

if (!function_exists('getNumFollowings')) {
    /**
     * Get the umber of followings by user ID
     *
     * @param int $userID
     * @param PDO $pdo
     * @return int
     */
    function getNumFollowings(int $userID, PDO $pdo): int
    {
        $statement = $pdo->prepare("SELECT * FROM follows WHERE user_id = :user_id");
        $statement->execute([
            ":user_id" => $userID
        ]);
        $followings = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($followings===false) {
            return 0;
        } else {
            $numFollowings = 0;
            foreach ($followings as $following) {
                $numFollowings++;
            }
            return $numFollowings;
        }
    }
}

if (!function_exists('FollowByID')) {
    /**
     * Check whether logged-in user is following by profile ID
     *
     * @param int $profileID
     * @param int $userID
     * @param PDO $pdo
     * @return bool
     */
    function FollowByID(int $profileID, int $userID, PDO $pdo): bool
    {
        $statement = $pdo->prepare("SELECT * FROM follows WHERE user_id = :user_id AND id_following = :id_following");
        $statement->execute([
            ":user_id" => $userID,
            ":id_following" => $profileID
        ]);
        $following = $statement->fetch(PDO::FETCH_ASSOC);
        if($following!==false) {
            return true;
        }
        return $following;
    }
}

if (!function_exists('getCommentsByPostID')) {
    /**
     * Get User by user ID
     *
     * @param int $postID
     * @param PDO $pdo
     * @return array
     */
    function getCommentsByPostID(int $postID, PDO $pdo): array
    {
        $statement = $pdo->prepare("SELECT * FROM comments WHERE post_id = :post_id");
        $statement->execute([
            ":post_id" => $postID
        ]);
        $comments = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($comments===false) {
            $comments = [];
        }
        return $comments;
    }
}

if (!function_exists('timeSinceUploaded')) {
    /**
     * Calculates how many seconds, hours, or days since post was uploaded
     *
     * @param [type] $dateCreated
     * @return string
     */
    function timeSinceUploaded(string $dateCreated): string
    {
        $now = date("Y-m-d H:i:s");
        $uploaded = strtotime($now) - strtotime($dateCreated);
        if ($uploaded >= 1209600) {
            $time = floor($uploaded / 1209600) . ' WEEKS AGO';
        } elseif ($uploaded >= 604800) {
            $time = floor($uploaded / 604800) . ' WEEK AGO';
        } elseif ($uploaded >= 172800) {
            $time = floor($uploaded / 172800) . ' DAYS AGO';
        } elseif ($uploaded >= 86400) {
            $time = floor($uploaded / 86400) . ' DAY AGO';
        } elseif ($uploaded >= 3600) {
            $time = floor($uploaded / 3600) . ' HOURS AGO';
        } elseif ($uploaded >= 60) {
            $time = floor($uploaded / 60) . ' MINUTES AGO';
        } else {
            $time = 'A FEW SECONDS AGO';
        }
        return $time;
    }
}
