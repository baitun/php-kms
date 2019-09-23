<?php 
	/**
	 * Отображение вложенных категорий для пользователей с выпадающими списками
	 */
	if(!isset($_GET['cc_id'])) header('location: ./?cc_id=1');
	$cc_id=$_GET['cc_id'];
	if(!($cc_id>=1 && $cc_id<=4)) header('location: ./?cc_id=1');

	include_once('./header.php');
	$simpleDB=new SimpleDB();
	$km=new Km($simpleDB);

  $categories=$km->get_full_tree(1);
  // var_dump($categories); die;

	$last_depth=0;
	$html_list="";
	$tmp="";
	$n_ul=0;
	$n_li=0;
	foreach ($categories as $category) {
		if(($category->depth==0)) continue;
		$is_leaf_node=true;
		if($category->depth > $last_depth) {
			$is_leaf_node=false;
			$class=($category->depth==1)?'accordion':'inner';
			$html_list.="<ul class='$class'>";
			$tmp.="["; $n_ul++;
		}
		else if($category->depth < $last_depth) {
			$html_list.="</li></ul></li>";
			$tmp.=">]>";$n_li-=2;$n_ul--;
		}
		else { // depth == last_depth
			$html_list.="</li>";
			$tmp.=">";$n_li--;
		}
		$html_list.="
		<li id='category$category->category_id'><a class='toggle' href='#category$category->category_id'>$category->name</a>
		";
		if(!empty($category->content)){
			$category->content = preg_replace("/{{category(\d+)\|((\w|\s)+)}}/u", "<a href='#category$1'>$2</a>", $category->content);
			$html_list.="<div class='inner'>$category->content</div>";
		}
		$tmp.="<";$n_li++;
		$last_depth=$category->depth;
	}

	for ($i=0; $i <= $last_depth; $i++) { 
		$html_list.="</li></ul>";
		$tmp.=">]";
	}
	?>

	<!DOCTYPE html>
	<html lang="ru">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta charset="UTF-8">
		<title>База знаний IT</title>
		<link href="main.css" rel="stylesheet">
		<script src="js/jquery-3.1.1.min.js"></script>
		<script src="js/collapse.js"></script>
		<style>
		a { text-decoration: none }
		.inner * {max-width: 100%}
	</style>
</head>
<body>

	<h1>Система управления знаниями</h1>

	<?=$html_list?>

	<p>Время загрузки страницы: <?=round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2)?> с.</p>
	
</body>
</html>