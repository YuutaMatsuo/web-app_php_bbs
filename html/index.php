<?php

// タイムゾーンの設定
date_default_timezone_set("Asia/Tokyo");

// コメントを保存する配列
$comment_array = array();
// エラーメッセージを保存する配列
$error_message = array();

// DB接続
try {
  $pdo = new PDO('pgsql:dbname=postgres_db_yztc host=dpg-cn8opo0cmk4c739s8ig0-a port=5432', "render", "GwHkVIfxFzKuRfT26uTbVLphSw6IpmcU");
} catch (PDOException $e) {
  echo $e->getMessage();
}

// $_POSTとすることでフォームからの入力を受け取ることができる
if (!empty($_POST["submitButton"])) {

  // 名前のチェック
  if (empty($_POST["username"])) {
    echo "名前を入力してください";
    $error_message["username"] = "名前を入力してください";
  }
  // コメントのチェック
  if (empty($_POST["comment"])) {
    echo "コメントを入力してください";
    $error_message["comment"] = "コメントを入力してください";
  }

  // エラーがある時はSQLを生成しない
  if (empty($error_message)) {
    $postDate = date("Y-m-d H:i:s");

    // フォームから値を受け取り、SQLに流し込む
    try {
      $stmt = $pdo->prepare(
        "INSERT INTO bbs_table (username, comment, postdate) VALUES (
          :username, :comment, :postDate);"
      );
      $stmt->bindParam(':username', $_POST["username"], PDO::PARAM_STR);
      $stmt->bindParam(':comment', $_POST["comment"], PDO::PARAM_STR);
      $stmt->bindParam(':postDate', $postDate, PDO::PARAM_STR);

      $stmt->execute();
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  }
}

// DBからコメントデータを取得する
$sql = "select * from bbs_table ;";
$comment_array = $pdo->query($sql);

// DBの接続を閉じる
$pdo = null;

?>



<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP掲示板</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1 class="title">掲示板アプリ</h1>
  <p>PHPを用いて作成した掲示板アプリです。</p>
  <hr>
  <div class="boardWrapper">
    <section>
      <?php foreach ($comment_array as $comment) : ?>
        <article>
          <div class="wrapper">
            <div class="nameArea">
              <span>名前：</span>
              <p class="username"><?php echo $comment["username"]; ?></p>
              <time>:<?php echo $comment["postdate"]; ?></time>
            </div>
            <p class="comment"><?php echo $comment["comment"]; ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </section>
    <form class="formWrapper" method="POST">
      <div>
        <input type="submit" value="書き込む" name="submitButton">
        <label for="">名前：</label>
        <input type="text" name="username">
      </div>
      <div>
        <textarea class="commentTextArea" name="comment"></textarea>
      </div>
    </form>
  </div>
</body>

</html>