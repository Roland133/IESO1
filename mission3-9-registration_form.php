<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<titleユーザー登録</title>
</head>

<body>
<?php
session_start();
//GETデータを変数に入れる
$urltoken = $_GET['urltoken'];
?>
<form action="http://**************/mission3-9-registration_form.php?urltoken=<?php echo $urltoken; ?>" method="post">
以下を入力してユーザー登録してください。<br>
ユーザー名は日本語入力可能です。<br><br>
ユーザー名
<input type="text" name="name"><br>
パスワード
<input type="password" name="pass"><br>
<input type="submit" name="submit" value="登録">
</form>
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

$errors= array();
$message=array();


//echo $urltoken;
//メール入力判定
if ($urltoken == ''){
	$errors['urltoken'] = "もう一度登録をやりなおして下さい。";
}
//dsnの記述をする
$dsn = 'mysql:dbname=***************;host=localhost';
	
//ユーザー情報
$user = '************';
$password = '***********';
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
	//flagが0の未登録者・仮登録日から24時間以内
	$statement = $pdo->prepare("SELECT*FROM pre_member WHERE urltoken=(:urltoken) AND flag = 0");
	$statement->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
	$statement->execute();
			
	//レコード件数取得
	$row_count = $statement->rowCount();
			
	//24時間以内に仮登録され、本登録されていないトークンの場合
	if($row_count >0){
		$mail_array = $statement->fetch();
		$mail = $mail_array[mail];
		$_SESSION['mail'] = $mail;
		}else{
		$errors['urltoken_timeover'] = "このURLはご利用できません。有効期限が過ぎた等の問題があります。もう一度登録をやりなおして下さい。";
		}
	//データベース接続切断
	$pdo = null;
	}
catch(PDOException $e){
		var_dump($e);//エラーがあれば表示
	}

if(isset($_POST["submit"]))
{
if(empty($_POST["name"])){
	$errors["input"]="ユーザー名を入力して下さい。";
	}else if(empty($_POST["pass"])){
	$errors["input"]="パスワードを入力して下さい。";
	}
	
	if(!empty($_POST["name"]) && !empty($_POST["pass"])){	
	$name=$_POST['name'];
	//前後のスペースを削除
	$name=spaceTrim($name); 
	$pass=$_POST['pass'];
	//前後のスペースを削除
	$pass=spaceTrim($pass);
	$pass=hash('sha256',$pass);
	
	//dsnの記述をする
	$dsn = 'mysql:dbname=***********;host=localhost';
	
	//ユーザー情報
	$user = '*************';
	$password = '*************';
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
		
		//ユーザー名の重複をチェック
		$select0 = $pdo->query("SELECT*FROM member WHERE name='$name'");
		//fetchColumnでセレクトの検索結果の件数を出す
		$count = $select0->fetchColumn();
		//重複がある場合はエラー
		if($count>1){
			$errors["duplication"]="すでに登録されたユーザー名です。別のユーザー名で登録してください。";
		}
		if(count($errors)===0){
		//INSERTを使ってデータを入力
		$stmt = $pdo -> prepare("INSERT INTO member (name, password, mail) VALUES (:name, :pass, :mail)");
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
		$stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
		$stmt->execute();
		
		//フラグを１にして仮登録状態を解除
		$stmt1 = $pdo -> query("UPDATE pre_member SET flag=1 WHERE mail='$mail'");
		
			
		$select = $pdo->query("SELECT*FROM member WHERE name='$name'");
		//それぞれのデータを取り出す
		foreach($select as $userinform){
			
			$message['success']="<hr>"."<font color='#FF0000' >登録完了！</font>";
			$message['id']="ユーザーID：".$userinform['id'];
			$message['name']="ユーザー名：".$userinform['name'];
			$message['pass']="パスワード：".$_POST['pass'];
			$message['mail']="メールアドレス".$userinform['mail'];
			$message['link']=" <li><a href='mission3-9-login.php'>ログイン画面へ</a></li>";
			}
		}	
		
		
	}catch(PDOException $e){
		var_dump($e);//エラーがあれば表示
		}
			
	}
}

	
?>

<?php
if(count($errors) < 1){
	foreach($message as $value){
		echo "<p>".$value."</p>";
	}
}
else 
if(count($errors)>0){
	foreach($errors as $value){
		echo "<p>".$value."</p>";
		}
}
?>
</html>