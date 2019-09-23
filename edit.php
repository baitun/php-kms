<?php 
/**
 * Страница редактирования статьи
 */
	include_once('./auth.php');
	include_once('./header.php');
	$simpleDB=new SimpleDB();
	$_REQUEST=$simpleDB->EscapeMysqli($_REQUEST);
	$km=new Km($simpleDB);

	if(!empty($_REQUEST['article_id']))	$article_id=$_REQUEST['article_id'];
	else die("article_id required");


	if(isset($_REQUEST['update'])){
		$content=$_REQUEST['article_text'];
		$r=$km->update_atricle($article_id, $content);
		var_dump($r);
		if($r) header("Location: ?article_id=$article_id");
		else var_dump($SimpleDB->mysqli->error);
	}

	$content="";
	if(!empty($article_id)) {
		$article=$km->get_article($article_id);
		if(!empty($article->content)) $content=$article->content;
	}
?>

<!DOCTYPE html>
<html lang="ru">
	<head>
	  <meta charset="utf-8">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <meta charset="UTF-8">
	  <style>
	  	textarea{
	  		margin: auto;
	  		width: 100%;
	  		min-height: 500px;
	  		height: 100%;
	  	}
	  </style>
	</head>
	<body>
	<p>Имя пользователя: <?=$auth_data['username']?></p>
	<a href="admin.php">Вернуться</a>
	<h1><?=(isset($article->name))?$article->name:''?></h1>
	<form method="POST" action="?update=1">
		<input type="hidden" name="update" value="1">
		<input type="hidden" name="article_id" value="<?=$article_id?>">
		<textarea name="article_text" id="article_text">
			<?=$content?>
		</textarea>
		<input type="submit">
	</form>
	<br>
	<div>
		<div>Список шаблонов:</div>
		<ul>
			<li>{{it_email}}</li>
		</ul>
	</div>

	<script src="nicEdit/nicEdit.js" type="text/javascript"></script>
	<script type="text/javascript">
		bkLib.onDomLoaded(function(argument) {
			new nicEditor().panelInstance('article_text');
		});
	</script>
	</body>
</html>
