<?php
session_start();
 // ログイン状態チェック
 if (!isset($_SESSION["USERID"])) {
header("Location: mission3-9-login.php");
 exit;
 }

$getname=$_SESSION['USERNAME'];
$getmail=$_SESSION['USERMAIL'];
$userid=$_SESSION["USERID"];
 //echo $getname;
 //echo($_FILES['imgmov']['error']);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>代理探し掲示板</title>
</head>
<center><h2><font color="#237BE8">代理探し</font>掲示板</h2>
<body>
MENU<br>
<a href='exp.php'>◯この掲示板の説明</a>　<a href="mission3-9-useredit.php">◯ユーザー情報の編集</a>　<a href="Logout.php">◯ログアウト</a></center>
<form action="mission3-9-main.php" enctype="multipart/form-data" method="post">
名前<br><input type="text" name="name" value="<?php echo $getname; ?>" size="15"/><br/>
コメント<br><textarea name="comment"rows="5" cols="27"/></textarea><br/>
メールアドレス<br><input type="text" name="mail" value="<?php echo $getmail ?>" size="30"/><br/>
動画・画像<br><input type="file" name="imgmov" accept="image/*, video/mp4, video/quicktime">
<input type="submit" value="投稿" name="send">　<input type="reset" value="クリア">
</form><br>

</body>

<?php
session_start();
 // ログイン状態チェック
 if (!isset($_SESSION["USERID"])) {
header("Location: mission3-9-login.php");
 exit;
 }
if($_SESSION["USERID"]==="2" && $_SESSION["USERMAIL"]==="@gmail.com"){echo "<div align='right'><a href='mission3-9-SA.php'>●管理者削除ページ</a></div>";}
//変数にformからの内容を代入
$Name=$_POST["name"];
$Mail=$_POST["mail"];
$Comment=$_POST["comment"];
$Pass=$_SESSION["USERPASS"];
//タイムゾーンを東京に設定、変数に代入
date_default_timezone_set('Asia/Tokyo');
$date=date("Y/m/d H:i:s");
//動画・画像が選択されて送信された時の変数
$FILE=$_POST["imgmov"];
//echo ($FILE);

//区切りと改行をセットにして変数に代入
$kaigyou="<>\n";
//送信ボタンが押された場合にのみ追記保存
//更新とリンクから飛んだ際はGET、送信ボタンを押した際はPOST
if($_SERVER["REQUEST_METHOD"] == "POST"){
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
	// 改行処理
	$Comment = nl2br($Comment);	
	//コメント欄の改行コードを削除する
	$Comment = str_replace(array("\r\n", "\r", "\n"), '', $Comment);

	//dsnの記述をする
	$dsn = 'mysql:dbname=****;host=****';
	
	//ユーザー情報
	$user = '*******';
	$password = '******';
	$dbname = '*******';
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
		$stmt = $pdo -> prepare("INSERT INTO user (userid, name, comment, mail, pass, date, fname, extension, raw_data) VALUES (:userid, :name, :comment, :mail, :pass, :date, :fname, :extension, :raw_data)");
		$stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
		$stmt->bindParam(':name', $Name, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $Comment, PDO::PARAM_STR);
		$stmt->bindParam(':mail', $Mail, PDO::PARAM_STR);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':pass', $Pass, PDO::PARAM_STR);
		$stmt->bindParam(":fname",$fname, PDO::PARAM_STR);
        $stmt->bindParam(":extension",$extension, PDO::PARAM_STR);
        $stmt->bindParam(":raw_data",$raw_data, PDO::PARAM_STR);
		$stmt->execute();
		
	}catch(PDOException $e){
		var_dump($e);//エラーがあれば表示
		}
}
	
	//表示フォーム
	//dsnの記述をする
	$dsn = 'mysql:dbname=*********;host=*******';

	//ユーザー情報
	$user = '********';
	$password = '******';
	$dbname = '*********';
	try	{
		//接続する
		$pdo = new PDO($dsn, $user, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//エラーがある場合は表示
		if ($pdo->connect_error){
			echo $pdo->connect_error;
			exit();
		}
		//select時はORDER BY　を使って昇順で選ぶ
		$select = $pdo->query('SELECT*FROM user ORDER BY id ASC');

		//内容の表示
		foreach($select as $tablecont){
			//echo "<hr>".$count."<hr>";
			echo "<hr>".$tablecont['id']."<br>";
			echo "【投稿日時】".$tablecont['date']."<br>";
			echo "【ユーザー名】".$tablecont['name']."<br>";
			echo "【コメント】".$tablecont['comment']."<br>";
			$tablecontuserid=$tablecont['userid'];
			 
			//該当がある場合は基本シフトを表示
			$abc ="SELECT*FROM shift WHERE userid='$tablecontuserid'";
			$select0 = $pdo->query($abc);
			$count = $select0->rowCount();
			if($count=1){
				foreach($select0 as $tablecont0){
					echo "【シフト】";
					if(!empty($tablecont0["day1"])){
					echo "① ".$tablecont0["day1"];}
					if(!empty($tablecont0["time1"])){
					echo $tablecont0["time1"]." "."		";}
					if(!empty($tablecont0["day2"])){
					echo " ② ".$tablecont0["day2"];}
					if(!empty($tablecont0["time2"])){
					echo $tablecont0["time2"]." "."		";}
					if(!empty($tablecont0["day3"])){
					echo " ③ ".$tablecont0["day3"];}
					if(!empty($tablecont0["time3"])){
					echo $tablecont0["time3"]." "."		";}
					continue;
					}
				}
			echo "<br>"."【メール】".$tablecont['mail']."<br>";
			
			//画像や動画があれば表示させる
			if(isset($tablecont['raw_data']) && isset($tablecont['fname'])){
				$cont=$tablecont['fname'];
				if( $tablecont["extension"] == "jpeg"|| $tablecont["extension"] == "png"|| $tablecont["extension"] == "gif"){
					echo "<img src= 'display.php?target=$cont' width=30% height=auto>"."<br>";
					 }
				if( $tablecont["extension"] == "mp4"|| $tablecont["extension"] == "mov"){
					echo "<video src= 'display.php?target=$cont' width='400' height='300' controls></video>"."<br>";
					 }
				}
			//セッションに保存されたIDとパスワードが投稿に保存されているIDとパスワードに一致すれば編集削除のリンクを表示
			//echo $userid."|";
			//echo $tablecont["userid"]."|";
			//echo $Pass."|";
			//echo $tablecont["pass"]."|";
			if($userid === $tablecont["userid"] && $Pass === $tablecont["pass"]){
				$urleditget="<a href="."http://co-604.it.99sv-coco.com/mission3-9-edit.php?id=";
				$urldelget="<a href="."http://co-604.it.99sv-coco.com/mission3-9-del.php?id=";
				//GETで投稿番号を一緒にパラメータとして付与しておく。
				echo "<div align='right'>".$urleditget.$tablecont["id"].">"."編集"."</a>"."		";
				echo $urldelget.$tablecont["id"].">"."削除"."</a></div>";
				}
		}
	
	}catch(PDOException $e){
		var_dump($e);
		}
?>
</html>