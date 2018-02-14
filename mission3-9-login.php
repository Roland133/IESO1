<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>ログイン画面</title>
</head>
<form action="mission3-9-login.php" method="post">
メールアドレス:<input type="text" name="mail"><br>
パスワード    :<input type="password" name="pass"><br>
<input type="submit" name="login" value="ログイン">
</form><br><br>
 <li><a href="mission3-9-mailregister.php">ユーザー登録</a></li>
<body>
</body>
<?php

//前後にある半角全角スペースを削除する関数
function spaceTrim ($str) {
	// 行頭
	$str = preg_replace('/^[ 　]+/u', '', $str);
	// 末尾
	$str = preg_replace('/[ 　]+$/u', '', $str);
	return $str;
}
	
session_start();

//LOGIN済みであれば以下のプログラムを実行せずに掲示板にジャンプ
if(isset($_SESSION["USERID"])){
	header('Location: mission3-9-main.php');
 exit;
 }

$error="";
if(isset($_POST["login"])){
	if(empty($_POST["mail"])){
		$error="メールアドレスを入力して下さい。";
		echo $error."<br>";
		}else if(empty($_POST["pass"])){
			$error="パスワードを入力して下さい。";
			echo $error;
		}
	
	if(!empty($_POST["mail"]) && !empty($_POST["pass"])){
		
		$mail=$_POST['mail'];
		//前後のスペースを削除
		$mail=spaceTrim($mail);
		$pass=$_POST['pass'];
		//前後のスペースを削除
		$pass=spaceTrim($pass);
		//ハッシュ関数でパスワードをハッシュ化する
		$pass=hash('sha256',$pass);	
	
		//dsnの記述をする
		$dsn = 'mysql:dbname=*************;host=localhost';
	
		//ユーザー情報
		$user = '***************';
		$password = '*************';
		$dbname = '*************';
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

			$select = $pdo->query("SELECT*FROM member WHERE mail='$mail'");
			foreach($select as $userinform){
				//echo $userinform["password"];
				if($userinform["mail"]==$mail && $userinform["password"]==$pass){
					$_SESSION['USERID'] = $userinform["id"];
					$_SESSION['USERNAME'] = $userinform["name"];
					$_SESSION['USERPASS']=$_POST['pass'];
					$_SESSION['USERMAIL'] = $userinform["mail"];
					header('Location: mission3-9-main.php');
					exit();
					}else{
					$error = 'ユーザーIDあるいはパスワードに誤りがあります。';
					echo $error;
					}

			}

		}catch(PDOException $e){
		var_dump($e);//エラーがあれば表示
		}
	}
}
?>	
</html>