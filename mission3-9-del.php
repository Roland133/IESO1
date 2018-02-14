<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>代理探し掲示板</title>
</head>
<h2><font color="#237BE8">代理探し</font>掲示板　削除確認画面</h2>

<body>


<?php
session_start();
 // ログイン状態チェック
 if (!isset($_SESSION["USERID"])) {
header("Location: mission3-9-login.php");
 exit;
 }

$toukou_number_get=$_GET["id"];
//echo $toukou_number_get;


//表示フォーム
//dsnの記述をする
$dsn = 'mysql:dbname=***********;host=localhost';

//ユーザ名
$user = '**********';
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
	$select = $pdo->query("SELECT*FROM user WHERE id='$toukou_number_get'");
	//shiftテーブルの入力があるか検索
	$select0 = $pdo->query("SELECT*FROM shift WHERE userid='$userid'");
	//fetchColumnでセレクトの検索結果の件数を出す
	$count = $select0->fetchColumn();


	foreach($select as $tablecont){
		$toukou_number_select = $tablecont["id"];
		$toukou_userid = $tablecont["userid"];
		$toukou_pass = $tablecont["pass"];
		
		if($_SESSION["USERID"] === $toukou_userid && $_SESSION["USERPASS"] === $toukou_pass){
			echo "本当に以下の投稿を削除しますか？"."<br>"."<br>";
		 	echo $tablecont['id']."<br>";
		 	echo $tablecont['date']."<br>";
		 	echo $tablecont['name']."<br>";
		 	echo $tablecont['comment']."<br>"."<br>";
			//画像や動画があれば表示させる
			if(isset($tablecont['raw_data']) && isset($tablecont['fname'])){
				$cont=$tablecont['fname'];
				if( $tablecont["extension"] == "jpeg"|| $tablecont["extension"] == "png"|| $tablecont["extension"] == "gif"){
					echo "<img src= 'display.php?target=$cont' width=30% height=auto>"."<br>";
					 }
				if( $tablecont["extension"] == "mp4"|| $tablecont["extension"] == "mov"){
					echo "<video src= 'display.php?target=$cont' width=30% height=auto controls></video>"."<br>";
					 }
				}
			//該当がある場合は基本シフトを表示
			if($count>1){
				foreach($select0 as $tablecont0){
					if(!empty($tablecont0["day1"])){
					echo $tablecont0["day1"];}
					if(!empty($tablecont0["time1"])){
					echo $tablecont0["time1"]." "."<br>";}
					if(!empty($tablecont0["day2"])){
					echo $tablecont0["day2"];}
					if(!empty($tablecont0["time2"])){
					echo $tablecont0["time2"]." "."<br>";}
					if(!empty($tablecont0["day3"])){
					echo $tablecont0["day3"]." "."<br>";}
					if(!empty($tablecont0["time3"])){
					echo $tablecont0["time3"];}
					}
				}
			echo $tablecont['mail']."<br>";


			echo "<form action='mission3-9-delexe.php' method='post'>"."<input type='submit' value='削除' name='delete'>"."</form>";
			$_SESSION["NUMBER"]=$toukou_number_select;
			}else{
				echo "不正なアクセスです。自分の投稿以外は削除できません。";
				}
		}
	}
	catch(PDOException $e){
		var_dump($e);
		}
?>
</html>