<?php 
	/**
	 * Страница администрирования категорий (добавление, удаление)
	 */
	include_once('./auth.php');
	include_once('./header.php');
	$simpleDB=new SimpleDB();
	$_REQUEST=$simpleDB->EscapeMysqli($_REQUEST);
	$km=new Km($simpleDB);

	if(isset($_REQUEST['action'])){
		switch ($_REQUEST['action']) {
			case 'delete':
				$km->delete_node($_REQUEST['id']);
			break;
			case 'add_child':
				$km->add_child($_REQUEST['id'], $_REQUEST['name']);
			break;
			case 'insert_after':
				$km->insert_after($_REQUEST['id'], $_REQUEST['name']);
			break;
		}
	}

	$categories=$km->get_full_tree();

	$last_depth=-1;
	$html_list="";
	$tmp="";
	foreach ($categories as $category) {
		$is_leaf_node=true;
		if($category->depth > $last_depth) {
			$is_leaf_node=false;
			$class=($category->depth==0)?'accordion':'inner';
			$html_list.="<ul class='$class'>";
			$tmp.="[";
		}
		else if($category->depth < $last_depth) {
			$html_list.="</li></ul></li>";
			$tmp.=">]>";
		}
		else { // depth == last_depth
			$html_list.="</li>";
			$tmp.=">";
		}
		if(!empty($category->article_id)) $category->name="<a href='edit.php?article_id=$category->article_id'>$category->name</a>";
		$html_list.="
			<li> 
			$category->name
			<a class='minus' title='Удалить категорию' href='javascript:delete_node($category->category_id)'>&#8854;</a>
			<a class='plus' title='Добавить подкатегорию' href='javascript:add_child($category->category_id)'>&#8853;</a> 
			<a class='plus' title='Добавить категорию' href='javascript:insert_after($category->category_id)'>▼</a> 
		";
		$tmp.="<";
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
	  <title>Администрирование категорий</title>
	  <script src="js/jquery-3.1.1.min.js"></script>
	  <style>
	  	a { text-decoration: none }
	  	a.plus { color:green; }
	  	a.minus { color:red; }
	  	ul { counter-reset: item }
			li { display: block }
			li:before { content: counters(item, ".") " "; counter-increment: item }
	  </style>
	</head>
	<body>
	<p>Имя пользователя: <?=$auth_data['username']?></p>
	<h1>Управление категориями</h1>

	<?=$html_list?>

	<script>
		function add_child(id) {
			var name=prompt("Название категории");
			document.location="?action=add_child&id="+id+"&name="+name;
		}
		function insert_after(id) {
			var name=prompt("Название категории");
			document.location="?action=insert_after&id="+id+"&name="+name;
		}
		function delete_node(id) {
			if(confirm("Точно удаляем эту группу?\nУдалятся также все её подгруппы!")){
				document.location="?action=delete&id="+id;
			}
		}
	</script>

	</body>
</html>