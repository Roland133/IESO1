<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>代理探し掲示板</title>
</head>
<h2><font color="#237BE8">代理探し</font>掲示板　編集画面</h2>

<body>


<?php
session_start();
 // ログイン状態チェック
 if (!isset($_SESSION["USERID"])) {
header("Location: mission3-9-login.php");
 exit;
 }

if(isset($_POST["edit"])){
	
	//ファイルが選択された場合に分岐
	if(!empty($_FILES['imgmov']['name'])){
		//echo "<br>";
		//var_dump($_FILES['imgmov']['error']);
		//echo "<br>";
		//エラーチェック
		
		
		try{
		
			if($_FILES['imgmov']['error'] == 0) {
			//成功
			throw new RuntimeException('ファイルアップロード完了');
			}
			if($_FILES['imgmov']['error'] == 1) {
			//ファイルサイズが大きすぎます
			throw new RuntimeException('ファイルサイズが大きすぎます');
			}
			if($_FILES['imgmov']['error'] == 2) {
			//ファイルサイズが大きすぎます
			throw new RuntimeException('ファイルサイズが大きすぎます');
			}
			if($_FILES['imgmov']['error'] == 4) {
			//ファイルが未選択です
			throw new RuntimeException('ファイルが未選択です');
			}
			if($_FILES['imgmov']['error'] == 3 || $_FILES['imgmov']['error'] == 5 || $_FILES['imgmov']['error'] == 6 || $_FILES['imgmov']['error'] == 7 || $_FILES['imgmov']['error'] == 8 ) {
			//その他のエラー'
			throw new RuntimeException('その他のエラー');
			}
			
			
		}
		catch(Exception $e)
		{var_dump($e);}
		 	
			//画像・動画をバイナリデータにする
            $raw_data = file_get_contents($_FILES['imgmov']['tmp_name']);
			//var_dump($raw_data) ;
			

            //拡張子を見る
            $tmp = pathinfo($_FILES["imgmov"]["name"]);
			/*echo "<br>";
			var_dump($tmp);
			echo "<br>";*/
			$fname=$tmp["basename"];
			echo $fname;
            $extension = $tmp["extension"];
			//echo "<br>";
			//echo $extension;
			//echo "<br>";
            if($extension === "jpg" || $extension === "jpeg" || $extension === "JPG" || $extension === "JPEG"){
                $extension = "jpeg";
            }
            elseif($extension === "png" || $extension === "PNG"){
                $extension = "png";
            }
            elseif($extension === "gif" || $extension === "GIF"){
                $extension = "gif";
            }
            elseif($extension === "mp4" || $extension === "MP4"){
                $extension = "mp4";
			}
            elseif($extension === "mov" || $extension === "MOV"){
                $extension = "mov";
            }
            else{
				echo "非対応ファイルです．<br/>";
				exit;
				}
	}

	$toukou_number = $_SESSION["NUMBER"];
	
	//表示フォーム
	//dsnの記述をする
	$dsn = 'mysql:dbname=***********;host=localhost';

	//ユーザ名
	$user = '*************';
	$password = '**********';
	$dbname = '************';
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
		
		//タイムゾーンを東京に設定、変数に代入
		date_default_timezone_set('Asia/Tokyo');
		$date = date("Y/m/d H:i:s");	
		$name = $_POST["name"];
		$Comment = $_POST["comment"];
		$mail = $_POST["mail"];
		$uwagakipass = $_SESSION["USERPASS"];
		// 改行処理
		$Comment = nl2br($Comment);	
		//コメント欄の改行コードを削除する
		$Comment = str_replace(array("\r\n", "\r", "\n"), '', $Comment);

		//画像・動画の投稿があった場合
		if(!empty($_FILES['imgmov']['name'])){
			//UPDATE を使って対象の番号を編集
			$sql = 'UPDATE user SET userid=:userid, name=:name, comment=:comment, pass=:uwagakipass, mail=:mail, date=:date, fname=:fname, extension=:extension, raw_data=:raw_data WHERE id = :id';
			$stmt = $pdo -> prepare($sql);
			$stmt->bindParam(':userid', $_SESSION["USERID"], PDO::PARAM_STR);			
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $Comment, PDO::PARAM_STR);
			$stmt->bindParam(':uwagakipass', $uwagakipass, PDO::PARAM_STR);
			$stmt->bindParam(':mail', $mail, PDO::PARAM_STR);			
			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
			$stmt->bindParam(":fname",$fname, PDO::PARAM_STR);
			$stmt->bindParam(":extension",$extension, PDO::PARAM_STR);
			$stmt->bindParam(":raw_data",$raw_data, PDO::PARAM_STR);			
			$stmt->bindValue(':id', $toukou_number, PDO::PARAM_INT);
			$stmt->execute();
			}
		
		//画像・動画の投稿がない場合	
		if(empty($_FILES['imgmov']['name'])){
			//UPDATE を使って対象の番号を編集
			$sql = 'UPDATE user SET userid=:userid, name=:name, comment=:comment, pass=:uwagakipass, mail=:mail, date=:date WHERE id = :id';
			$stmt = $pdo -> prepare($sql);
			$stmt->bindParam(':userid', $_SESSION["USERID"], PDO::PARAM_STR);			
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $Comment, PDO::PARAM_STR);
			$stmt->bindParam(':uwagakipass', $uwagakipass, PDO::PARAM_STR);
			$stmt->bindParam(':mail', $mail, PDO::PARAM_STR);			
			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
			$stmt->bindValue(':id', $toukou_number, PDO::PARAM_INT);
			$stmt->execute();
			}
			
	}catch(PDOException $e){
		var_dump($e);
		}
	echo "編集が完了しました。";
	echo "<li><a href='mission3-9-main.php'>メイン</a></li>";
}
else{
echo "何らかのエラーが発生しました。";
}
?>
</html>