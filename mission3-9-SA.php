<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>代理探し掲示板</title>
</head>
<h2><font color="#237BE8">代理探し</font>掲示板　削除確認画面</h2>

<body>


<?php
session_start();
//errorsをリセット
$errors=array();

 // ログイン状態チェック
 if (!isset($_SESSION["USERID"])) {
	$errors["session"] = "ログインして下さい。";
 }
 
 if (isset($_SESSION["USERID"])) {
	if($_SESSION["USERID"] === "2" && $_SESSION["USERPASS"] === "1111"){
	echo "<form action='mission3-9-SA.php' method='post'>";
	echo "削除する投稿番号　<input type='text' name='delnum' size='5'><br>";
	echo "<input type='submit' value='削除' name='delete'>"."</form>";
 }else{
	$errors["user"] = "管理者ユーザーではありません。権限がありません。";
	 }
}


if(isset($_POST["delete"])){
	
	$toukou_number = $_POST["delnum"];	
	//表示フォーム
	//dsnの記述をする
	$dsn = 'mysql:dbname=**************;host=localhost';
	//ユーザ名
	$user = '***********';
	$password = '**********';
	$dbname = '***********';
	try	{
		//接続する
		$pdo = new PDO($dsn, $user, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//エラーがある場合は表示
		if ($pdo->connect_error){
			echo $pdo->connect_error;
			exit();
			}
		//文字化け対策
		$stmt=$pdo->query('SET NAMES utf8');
		//対象の番号を削除。この時プリペア構文を使う。
		$stmt = $pdo -> prepare("DELETE FROM user WHERE id = :id");
		$stmt -> bindValue(':id', $toukou_number, PDO::PARAM_INT);
		$stmt -> execute();
			
	}catch(PDOException $e){
		var_dump($e);
		}
	$message="投稿を削除しました。";
	echo $message;
}





?>

<?php

if(count($errors)>0){
	foreach($errors as $value){
		echo "<p>".$value."</p>";
		}
}

echo "<li><a href='mission3-9-main.php'>メイン</a></li>";
?>
</body>
</html>