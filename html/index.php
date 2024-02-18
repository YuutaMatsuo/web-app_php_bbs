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
  exit;
}

// $_POSTとすることでフォームからの入力を受け取ることができる
if (!empty($_POST["submitButton"])) {

  // 名前のチェック
  if (empty($_POST["username"])) {
    $error_message["username"] = "名前を入力してください";
  }
  // コメントのチェック
  if (empty($_POST["comment"])) {
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

      // データ挿入後に同じページにリダイレクト
      header("Location: " . $_SERVER['PHP_SELF']);
      exit;

    } catch (PDOException $e) {
      echo $e->getMessage();
      exit;
    }
  }
}

// DBからコメントデータを取得する
$sql = "select * from bbs_table ;";
$comment_array = $pdo->query($sql);

// DBの接続を閉じる
$pdo = null;

?>