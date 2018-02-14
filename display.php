<?php
if(isset($_GET["target"]) && !empty($_GET["target"])){
	$cont=$_GET["target"];
}else{
	header("Location mission3-9-main.php");}

$MIMEtypes = array(
'png' => 'image/png',
'jpge' => 'image/jpeg',
'gif' => 'image/gif',
'mp4' => 'video/mp4',
'mov' => 'video/quicktime'
);

	//表示フォーム
	//dsnの記述をする
	$dsn = 'mysql:dbname=***********;host=localhost';

	//ユーザー情報
	$user = '********';
	$password = '***********';
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
		$select = 'SELECT*FROM user WHERE fname=:cont';
		$stmt = $pdo->prepare($select);
		$stmt -> bindValue(":cont",$cont,PDO::PARAM_STR);
		$stmt -> execute();
		//内容の表示
		$tablecont=$stmt->fetch(PDO::FETCH_ASSOC);
			header("Content-Type: ".$MIMEtypes[$tablecont["extension"]]);
			echo ($tablecont['raw_data']);
			
			
			}catch(PDOException $e){
		var_dump($e);
		}
?>