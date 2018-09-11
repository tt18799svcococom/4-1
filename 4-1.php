<?php
//土井先生のコードを参考にしました。
//変数この変数はなぜ必要なのか。
$edit_val ="";
$name_val ="";
$comment_val ="";
$pass_val ="";
$name = $_POST["name"];
$comment = $_POST["comment"];

//データベースへの接続(3-1)tryとはなにか、25,26？？
try {
    $dsn = 'mysql:dbname=tt_187_99sv_coco_com;host=localhost';
    $user = 'tt-***.**sv-coco';
    $pass = 'password';
    $pdo = new PDO(
        $dsn,$user,$pass,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//エラー投げる
            PDO::ATTR_EMULATE_PREPARES => false,//静的プレースホルダー
        )
    );
    //テーブル削除 なぜ//使うのか
    //$sql = "DROP TABLE IF EXISTS table";
    //$pdo -> exec($sql);
    
    //テーブル作成 35行目'？ not exists, not null??(32)?
    $DB_table_name = "table1";
    $create_query = '
    CREATE TABLE IF NOT EXISTS '.$DB_table_name.'(
    id INT NOT NULL AUTO_INCREMENT primary key,
    name CHAR(32) NOT NULL,
    comment TEXT NOT NULL,
    date DATETIME NOT NULL,
    password TEXT NOT NULL
    )';
    $create_table = $pdo -> prepare($create_query);
    $create_table -> execute();
    
    //作成できたか確認 なぜ/*
    /*
     $is_table = $pdo -> query('SHOW TABLES');
    foreach($is_table as $rows){
        print_r($rows) ."<br>";
}
    //create tableの中身表示
    $showcre_sql ='SHOW CREATE TABLE table1';
    $showcre_result = $pdo-> query($showcre_sql);
    foreach ($showcre_result as $row_1){
        print_r($row_1);
    }
    echo"<hr>";
    */
    //現在時刻取得 これの必要性？
    $date = date(Y."-".m."-".d."-".H."-".i."-".s);
    
    //削除パス判定　削除判定パスとは？FETCH＿NUM？
    if(!empty($_POST["delete_pass"]) && !empty($_POST["delete_num"]) && ctype_digit($_POST["delete_num"])){
        //削除行の情報取得
        $delete_pass = $_POST["delete_pass"];
        $delete_num = $_POST["delete_num"];
        $select_sql ="SELECT * FROM table1 where id=$delete_num";
        $select_result = $pdo->query($select_sql);
        $sel_result = $select_result->fetch(PDO::FETCH_NUM);
        
        //パスが一致してたら削除実行$sql_result[4]？
        if($sel_result[4] == $delete_pass){
            $delete_sql = "delete from table1 where id=$delete_num";
            $delete_result = $pdo->query($delete_sql);
        }elseif($sel_result[4] != $delete_pass){
            echo "削除のパスワードが違います";
        }
    }
    //編集パス判定
    if(!empty($_POST["edit_pass"]) && !empty($_POST["edit_num"]) && ctype_digit($_POST["edit_num"])){
        $edit_pass = $_POST["edit_pass"];
        $edit_num = $_POST["edit_num"];
        unset($select_sql);
        unset($select_result);
        unset($sel_result);
        $select_sql ="SELECT * FROM table1 where id=$edit_num";
        $select_result = $pdo->query($select_sql);
        $sel_result =$select_result->fetch(PDO::FETCH_NUM);
 
        //パスが一致していたら編集内容をフォームに表示
        if($sel_result[4] == $edit_pass){
            $edit_val = $sel_result[0];
            $name_val = $sel_result[1];
            $comment_val = $sel_result[2];
            $pass_val = $sel_result[4];
        }elseif($sel_result[4] != $edit_pass){
            echo "編集のパスワードが違います";
        }
    }
    
    //編集機能
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $password = $_POST["password"];
    $edit = $_POST["edit"];
    if(!empty($_POST["edit"]) && !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])){
    
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $password = $_POST["password"];
        $edit = $_POST["edit"];
        
        $update_sql = "update table1 set name='$name' , comment='$comment' , date='$date' , password='$password' where id=$edit";
        $update_result = $pdo->query($update_sql);
    }elseif(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])){
        //投稿機能
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $password = $_POST["password"];
        
        //DBに追加 PDO::PARAM_STRとは？
        $add_sql = $pdo->prepare("INSERT INTO table1(name,comment,date,password)VALUES(:name,:comment,:date,:password)");
        $add_sql->bindParam(':name',$name,PDO::PARAM_STR);
        $add_sql->bindParam(':comment',$comment,PDO::PARAM_STR);
        $add_sql->bindParam(':date',$date,PDO::PARAM_STR);
        $add_sql->bindParam(':password',$password,PDO::PARAM_STR);
        $add_sql->execute();
    }

    //DB内容取得
        unset($select_sql);
        unset($select_result);
        unset($sel_reslt);
        $select_sql ='SELECT * FROM table1 ORDER BY id';
        $select_result = $pdo->query($select_sql);
        /*方法1
        foreach($select_result as $row){
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].'<br>';
        }*/
        //方法2
        $sel_result = $select_result->fetchAll(PDO::FETCH_NUM);
        //print_r($sel_result);

} catch (PDOException $e) {
    echo $e->getMessage()." - ".$e->getLine().PHP_EOL;

}
?>

<html lang="ja">
     <head>
     <meta charset="utf-8">
     <title>4-1</title>
     </head>
     <body>
        <form action="mission_4-1_(nakadai).php" method="post">
            <input type="text" name="edit" value="<?=$edit_val;?>" placeholder="編集対象番号"><br>
            <input type="text" name="name" value="<?=$name_val;?>" placeholder="名前"><br>
            <input type="text" name="comment" value="<?=$comment_val;?>" placeholder="コメント"><br>
            <input type="text" name="password" value="<?=$pass_val;?>" placeholder="パスワード"><br>
            <input type="submit" value="送信">
        </form>
        <form action="mission_4-1_(nakadai).php" method="post">
            <input type="text" name="delete_num" placeholder="削除番号"><br>
            <input type="text" name="delete_pass" placeholder="パスワード"><br>
            <input type="submit" value="削除">
        </form>
        <form action="mission_4-1_(nakadai).php" method="post">
            <input type="text" name="edit_num" placeholder="編集番号"><br>
            <input type="text" name="edit_pass" placeholder="パスワード"><br>
            <input type="submit" value="編集">
        </form>
        <?php
        //ブラウザ表示用
        foreach((array)$sel_result as $key1 => $val1){
            foreach( $val1 as $key2 => $val2 ){
                echo $val2." ";
            }
            echo "<br>";
        }
        ?>
     </body>
</html>
