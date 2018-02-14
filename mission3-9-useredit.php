<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<titleユーザー情報編集</title>
</head>

<body>
<?php
session_start();
// ログイン状態チェック
if (!isset($_SESSION["USERID"])) {
	header("Location: mission3-9-login.php");
 	exit;
}
?>
<form action="mission3-9-useredit.php" method="post">
以下でユーザー情報を編集してください。<br>
ユーザー名は日本語入力可能です。<br><br>
ユーザー名<br>
<input type="text" name="name" value="<?php echo $_SESSION['USERNAME']; ?>"><br>
パスワード<br>
<input type="password" name="pass"><br><br>
固定シフト①<br>
曜日<input type="text" name="day1">
時間<input type="text" name="time1"><br><br>
固定シフト②<br>
曜日<input type="text" name="day2">
時間<input type="text" name="time2"><br><br>
固定シフト③<br>
曜日<input type="text" name="day3">
時間<input type="text" name="time3"><br><br>
<input type="submit" name="submit" value="内容を変更する">
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

if(isset($_POST["submit"]))
{
	if(empty($_POST["name"])){
		$errors["input"]="ユーザー名を入力して下さい。";
		}else if(empty($_POST["pass"])){
		$errors["input"]="パスワードを入力して下さい。";
		}
	
	$name=$_POST['name'];
	//前後のスペースを削除
	$name=spaceTrim($name); 
	$pass=$_POST['pass'];
	//前後のスペースを削除
	$pass=spaceTrim($pass);
	$pass=hash('sha256',$pass);
	$day1=$_POST["day1"];
	$time1=$_POST["time1"];
	$day2=$_POST["day2"];
	$time2=$_POST["time2"];
	$day3=$_POST["day3"];
	$time3=$_POST["time3"];
	
	//dsnの記述をする
	$dsn = 'mysql:dbname=***********;host=localhost';
	
	//ユーザー情報
	$user = '************';
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
		
		//ユーザー名の重複をチェック
		$select0 = $pdo->query("SELECT*FROM member WHERE name='$name'");
		//rowCount();でセレクトの検索結果の件数を出す
		$count = $select0->rowCount();
		
		foreach($select0 as $select1){
			//重複がある場合はエラー
			if($count = 1 && $select1["id"] != $_SESSION["USERID"]){
				$errors["duplication"]="すでに登録されたユーザー名です。別のユーザー名で登録してください。";
				}
			}
		
		if(count($errors)===0){
			
			
			//UPDATE を使って対象の番号を編集
			$sql = 'UPDATE member SET name=:name, password=:pass WHERE id = :id';
			$stmt = $pdo -> prepare($sql);
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
			$stmt->bindValue(':id', $_SESSION["USERID"], PDO::PARAM_INT);
			$stmt->execute();
			
			$userid=$_SESSION['USERID'];
			//echo $userid;
			//shiftの検索
			$select2 = $pdo->query("SELECT*FROM shift WHERE userid='$userid'");
			//rowCount();でセレクトの検索結果の件数を出す
			$count1 = $select2->rowCount();

			if($count1>0){
				
				//UPDATE を使って対象の番号を編集
				$sql1 = "UPDATE shift SET day1=:day1, time1=:time1, day2=:day2, time2=:time2, day3=:day3, time3=:time3 WHERE userid = '$userid'";
				$stmt = $pdo -> prepare($sql1);
				$stmt->bindParam(':day1',$day1, PDO::PARAM_STR);
				$stmt->bindParam(':time1',$time1, PDO::PARAM_STR);
				$stmt->bindParam(':day2',$day2, PDO::PARAM_STR);
				$stmt->bindParam(':time2',$time2, PDO::PARAM_STR);
				$stmt->bindParam(':day3',$day3, PDO::PARAM_STR);
				$stmt->bindParam(':time3',$time3, PDO::PARAM_STR);
				$stmt->execute();
				
				session_destroy();			
				$message['link']="<a href='mission3-9-login.php'>◯ログイン画面へ</a>";
				}
			
			if($count1==0){
				
				//INSERTを使ってデータを入力
				$stmt = $pdo -> prepare("INSERT INTO shift (userid, day1, time1, day2, time2, day3, time3) VALUES(:userid, :day1, :time1, :day2, :time2, :day3, :time3)");
				$stmt->bindParam(':userid',$userid, PDO::PARAM_STR);
				$stmt->bindParam(':day1',$day1, PDO::PARAM_STR);
				$stmt->bindParam(':time1',$time1, PDO::PARAM_STR);
				$stmt->bindParam(':day2',$day2, PDO::PARAM_STR);
				$stmt->bindParam(':time2',$time2, PDO::PARAM_STR);
				$stmt->bindParam(':day3',$day3, PDO::PARAM_STR);
				$stmt->bindParam(':time3',$time3, PDO::PARAM_STR);
				$stmt->execute();
				
				session_destroy();
				$message['link']="<a href='mission3-9-login.php'>●ログイン画面へ</a>";
				}
			}
			
		
		
	}catch(PDOException $e){
		var_dump($e);//エラーがあれば表示
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