<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>代理探し掲示板削除編集フォーム</title>
</head>
<h2><font color="#237BE8">代理探し</font>掲示板　編集フォーム</h2>

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
$dsn = 'mysql:dbname=**************;host=localhost';

//ユーザ名
$user = '***********';
$password = '************';
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
	$select = $pdo->query("SELECT*FROM user WHERE id='$toukou_number_get'");
	//shiftテーブルの入力があるか検索
	$select0 = $pdo->query("SELECT*FROM shift WHERE userid='$userid'");
	//fetchColumnでセレクトの検索結果の件数を出す
	$count = $select0->fetchColumn();


	foreach($select as $tablecont){
		$toukou_number_select = $tablecont["id"];
		$toukou_name = $tablecont["name"];
		$toukou_userid = $tablecont["userid"];
		$toukou_comment = $tablecont["comment"];
		$toukou_mail = $tablecont["mail"];
		$toukou_pass = $tablecont["pass"];
		//コメントは改行処理する
		$Commentbr = str_replace(array("<br />","<br>"),"\n",$toukou_comment);

		
		if($_SESSION["USERID"] === $toukou_userid && $_SESSION["USERPASS"] === $toukou_pass){
			echo "編集して下さい。画像や動画は新たなファイルの選択がなければ編集されません。基本シフトを編集したい場合は「ユーザー情報の編集」を行って再度投稿し直して下さい。"."<br>"."<br>";
			echo "<form action='mission3-9-editexe.php' enctype='multipart/form-data' method='post'>";
			echo "名前<br><input type='text' name='name'  size='42' value='".$toukou_name."'/>"."<br/>";
			echo "コメント<br><textarea name='comment' rows='5'  cols='30' />"."$Commentbr"."</textarea>"."<br/>";
			echo "メールアドレス<br><input type='text' name='mail'  size='42' value='".$toukou_mail."'/>"."<br/>";
			echo "動画・画像：<input type='file' name='imgmov' accept='image/*, video/mp4, video/quicktime'>	";
			echo "<input type='submit' value='編集' name='edit'>"."</form>"."<br>";
			
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