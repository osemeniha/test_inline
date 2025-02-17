<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inline_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$posts = json_decode(file_get_contents("https://jsonplaceholder.typicode.com/posts"), true);
$postsCount = 0;
foreach ($posts as $post) {
    $stmt = $conn->prepare("INSERT INTO posts (id, title, body, userId) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('issi', $post["id"], $post["title"], $post["body"], $post["userId"]);
    $stmt->execute();
    $postsCount++;
}

$comments = json_decode(file_get_contents("https://jsonplaceholder.typicode.com/comments"), true);
$commentsCount = 0;
foreach ($comments as $comment) {
    $stmt = $conn->prepare("INSERT INTO comments (id, name, email, body, postId) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('isssi', $comment["id"], $comment["nsme"], $comment["email"], $comment["body"], $comment["postId"]);
    $stmt->execute();
    $commentsCount++;
}

echo "Загружено $postsCount записей и $commentsCount комментариев";