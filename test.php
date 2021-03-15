<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
        // フォームから取得
        $name = $_POST["name"]; //名前
        $comment = $_POST["comment"]; //投稿
        $num = $_POST["num"]; //投稿番号
        $pass = $_POST["pass"]; //パスワード
        $delete = $_POST["delete"]; //削除対象番号
        $delpass = $_POST["delpass"]; //削除用パスワード
        $edit = $_POST["edit"]; //編集対象番号
        $edpass = $_POST["edpass"]; //編集用パスワード
        $date = date("Y/m/d H:i:s"); //日付
        
        //DB接続設定
        $dsn = 'mysql:dbname=tb*****db;host=localhost';
        $user = 'tb-*****';
        $password = '*********';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        //編集番号受け取りフォームに表示
        //編集番号とパスワードを書くと
        if(!empty($edit && $edpass)){
            $sql = 'SELECT * FROM tbpos';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach($results as $row){
                if(($row['id'] == $edit) && ($row['pass'] == $edpass)){
                    $reuser = $row['name']; //名前(編集)
                    $repos = $row['comment']; //投稿(編集)
                    $renum = $row['id']; //投稿番号
                    $repass = $row['pass']; //パスワード
                }    
            }
        }
    ?>
    <!--投稿フォーム-->
    <form action="" method="POST">
        <!--新規登録-->
        <input type="text" name="name" placeholder="名前" value="<?php echo $reuser; ?>">
        <br>
        <input type="text" name="comment" placeholder="コメント" value="<?php echo $repos; ?>">
        <br>
        <input type="hidden" name="num" value="<?php echo $renum; ?>">
        <br>
        <input type="text" name="pass" placeholder="パスワード" value="<?php echo $repass; ?>">
        <input type="submit" name="submit">
        <br>
        <br>
        <!--削除-->
        <input type="text" name="delete" placeholder="削除対象番号">
        <input type="text" name="delpass" placeholder="パスワード">
        <input type="submit" value="削除">
        <br>
        <br>
        <!--編集-->
        <input type="text" name="edit" placeholder="編集対象番号">
        <input type="text" name="edpass" placeholder="パスワード">
        <input type="submit" value="編集">
    </form>
    <?php
        //DB内にテーブルを作成(id,name,comment,date)
        $sql = "CREATE TABLE IF NOT EXISTS tbpos"
        ." ("
        ."id INT AUTO_INCREMENT PRIMARY KEY,"
        ."name char(32),"
        ."comment TEXT,"
        ."date TEXT,"
        ."pass TEXT"
        .");";
        $stmt = $pdo->query($sql);
        
        //編集済みのものを登録
        //投稿番号が記入されていたら
         if(!empty($num)){
        	$sql = 'UPDATE tbpos SET name=:name,comment=:comment, date=:date, pass=:pass WHERE id=:id';
        	$stmt = $pdo->prepare($sql);
        	$stmt->bindParam(':name', $name, PDO::PARAM_STR); //名前
        	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR); //投稿
        	$stmt->bindParam(':id', $num, PDO::PARAM_INT); //投稿番号
        	$stmt->bindParam(':date', $date, PDO::PARAM_STR); //日付
        	$stmt->bindParam(':pass', $pass, PDO::PARAM_STR); //パスワード
        	$stmt->execute();
         }
        
        // データを新規登録
        // 空欄がある場合は登録されない
        else if(!empty($name && $comment && $pass)){
            $sql = $pdo ->prepare("INSERT INTO tbpos (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR); //名前
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR); //投稿
            $sql -> bindParam(':date', $date, PDO::PARAM_STR); //日付
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR); //パスワード
            $name = $_POST["name"]; //名前
            $comment = $_POST["comment"]; //コメント
            $pass = $_POST["pass"]; //パスワード
            $date = date("Y/m/d H:i:s"); //日付
            $sql->execute();
        }
    
        //削除機能
        //削除番号とパスワードが記入されていたら
        if(!empty($delete && $delpass)){
            //テーブル内のデータを展開
            $sql = 'SELECT * FROM tbpos';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach($results as $row){
                //削除対象番号のデータを抽出
                if($row['id'] == $delete){
                    //パスワード確認
                    if($row['pass'] == $delpass){
                        $sql = 'delete from tbpos where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }
        }
        
        //表示
        $sql = 'SELECT * FROM tbpos';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].' ';
            echo $row['name'].' ';
            echo $row['comment'].' ';
            echo $row['date'].'<br>';
        echo "<hr>";
        }
    ?>
        
        
</body>
</html>