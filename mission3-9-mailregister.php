<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>メール登録</title>
</head>

<body>
<form action="mission3-9-mailregister.php" method="post">
以下を入力すると仮登録できます。<br>
メールアドレス
<input type="text" name="mail" size="50">
<input type="submit" name="submit" value="送信">
</form>
</body>

<?php
session_start();
$errors=array();
//送信ボタンが押下されたら
if(isset($_POST["submit"])){	
	//メールアドレスの入力がない場合にエラー
	if(empty($_POST["mail"])){
		$errors['mail']="メールアドレスが入力されていません。";
	}
	//メールアドレスが空でなければアドレスの形式をチェック
	if(!empty($_POST["mail"])){
		$mailad=$_POST['mail'];
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mailad)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}
	}		
	//dsnの記述をする
	$dsn = 'mysql:dbname=**********;host=********';	
	//ユーザー情報
	$user = '*************';
	$password = '**********';
	$dbname = '**************';
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
		//メールアドレスの重複をチェック
		$select0 = $pdo->query("SELECT*FROM pre_member WHERE mail='$mailad'");
		//fetchColumnでセレクトの検索結果の件数を出す
		$count = $select0->fetchColumn();
		//重複が無いときのみ登録させる
		if($count>0){	
			$errors['member_check'] = "このメールアドレスはすでに利用されております。";
			}
		}
	catch(PDOException $e){
		var_dump($e);//エラーがあれば表示
	}
	//ここまでにエラーがなければ実行
	if (count($errors) === 0){
		//ユニークな値をハッシュ化して保存
		$urltoken = hash('sha256',uniqid(rand(),1));
		$url = "http://***************/mission3-9-registration_form.php"."?urltoken=".$urltoken;	
		//dsnの記述をする
		$dsn = 'mysql:dbname=***************;host=localhost';
		//ユーザー情報
		$user = '***********';
		$password = '*********';
		$dbname = '**********';
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
			//INSERTを使ってデータを入力
			$stmt = $pdo -> prepare("INSERT INTO pre_member (urltoken, mail) VALUES (:urltoken, :mail)");
			$stmt->bindParam(':urltoken', $urltoken, PDO::PARAM_STR);
			$stmt->bindParam(':mail', $mailad, PDO::PARAM_STR);
			$stmt->execute();
			}
		catch(PDOException $e){
			var_dump($e);//エラーがあれば表示
			}		
		//メールの宛先
		$mailTo = $mailad;
		//Return-Pathに指定するメールアドレス
		$returnMail = '*********@gmail.com';
		$name = "MySQL掲示板";
		$mail = '************@gmail.com';
		$subject = "【代理探し掲示板】会員登録用URLの通知";
	 
	$body = <<< EOM
	24時間以内に下記のURLからご登録下さい。
	{$url}
EOM;
		mb_language('ja');
		mb_internal_encoding('UTF-8');
		//Fromヘッダーを作成
		$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';
		if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)){
			/*//セッション変数を全て解除
			$_SESSION = array();			
			//クッキーの削除
			if(isset($_COOKIE["PHPSESSID"])){
				setcookie("PHPSESSID",'', time() - 1800, '/');
			}
			//セッションを破棄する
			session_destroy();*/
			$message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";
		 }else{
			$errors['mail_error'] = "メールの送信に失敗しました。";
		}		
	}
}	
?>

<?php
if(count($errors)===0){
	echo $message;
}
else if(count($errors)>0){
	foreach($errors as $value){
		echo "<p>".$value."</p>";
		}
}

?>

</html>

