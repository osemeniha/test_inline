<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Тестовое задание</title>
</head>
<body>
<div class="container">
    <form action="search.php" method="get">
        <input type="text" name="search_comment">
        <input type="submit" value="Найти">
    </form>

</div>
</body>
</html>

<?php
if (isset($_GET['search_comment']) && strlen($_GET['search_comment']) >= 3) {
    $search_comment = $_GET['search_comment'];
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "inline_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT posts.id, posts.title, comments.body FROM comments JOIN posts 
    ON comments.postId = posts.id WHERE comments.body LIKE ?");
    $str = "%$search_comment%";
    $stmt->bind_param("s", $str);
    $stmt->execute();
    $result = $stmt->get_result();
    $results = [];
    while ($row = $result->fetch_assoc()) {
        $postId = $row['id'];
        $postTitle = $row['title'];
        $commentBody = $row['body'];

        if (isset($results[$postId])) {
            $results[$postId]['comments'][] = $commentBody;
        } else {
            $results[$postId] = [
                'title' => $postTitle,
                'comments' => [$commentBody]
            ];
        }
    }

    $stmt->close();
    $conn->close();

    if(empty($results)){
        echo "Ничего не найдено";
    }
    else{
        echo "<div class='posts'>";
        foreach ($results as $postId => $postData) {
            echo "<div class='post'>";
            echo "<h2>{$postData['title']}</h2>";
            echo "<ul>";
            foreach ($postData['comments'] as $comment) {
                $highlightedComment = str_ireplace($search_comment, "<strong>{$search_comment}</strong>", $comment);
                echo "<li>{$highlightedComment}</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        echo "</div>";
    }
}
else{
    echo "Введите минимум 3 символа для поиска";
}
?>