<?php
/**
 * Класс для работы с системой управления знаниями 
 */
class Km {	
	private $simpleDB;

	function __construct($simpleDB=null){
		$this->simpleDB=($simpleDB)?$simpleDB : new SimpleDB();
	}

	/**
	 * Получить всё дерево категорий
	 */
	public function get_full_tree($with_article=false)	{
		$query="
			SELECT node.category_id, node.name, (COUNT(parent.name) - 1) AS depth, node.article_id
			FROM nested_category AS node,
			     nested_category AS parent
			WHERE node.lft BETWEEN parent.lft AND parent.rgt
			GROUP BY node.category_id
			ORDER BY node.lft
		";
		if($with_article){
			$query="
				SELECT category.category_id, category.name, category.depth, category.article_id, articles.content
				FROM (
				    $query
				) as category
				LEFT JOIN articles on articles.id=category.article_id
			";
		}
		return $this->simpleDB->get_array($query);
	}

	/**
	 * Получить только Категории без дочерних элементов (крайние листья без потомков)
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function get_leaf_nodes($value='')	{
		$query="
			SELECT name
			FROM nested_category
			WHERE rgt = lft + 1;
		";
	}

	/**
	 * Добавить подкатегорию в текущую категорию
	 */
	public function add_child($parent_id, $category_name) {
		$query="
			CALL add_child($parent_id, '$category_name')
		";		
		$result=$this->simpleDB->query($query);
		if(!$result) throw new Exception($this->simpleDB->mysqli->error, 1);
		do {
			$this->simpleDB->mysqli->store_result();
		}
		while (mysqli_more_results($this->simpleDB->mysqli) && mysqli_next_result($this->simpleDB->mysqli));		
	}

	/**
	 * Вставить категорию после текущей
	 */
	public function insert_after($id, $category_name) {
		$query="
			CALL insert_after($id, '$category_name')
		";		
		$result=$this->simpleDB->query($query);
		if(!$result) throw new Exception($this->simpleDB->mysqli->error, 1);
		do {
			$this->simpleDB->mysqli->store_result();
		}
		while (mysqli_more_results($this->simpleDB->mysqli) && mysqli_next_result($this->simpleDB->mysqli));		
	}

	/**
	 * Удалить категорию и все её подкатегории
	 */
	public function delete_node($id){
		if(empty($id)) throw new Exception("id required", 1);
		
		$query="
			CALL delete_node($id)
		";
		$r=$this->simpleDB->mysqli->multi_query($query);
		if(!$r) throw new Exception($this->simpleDB->mysqli->error, 1);
		do {
			$this->simpleDB->mysqli->store_result();
		}
		while (mysqli_more_results($this->simpleDB->mysqli) && mysqli_next_result($this->simpleDB->mysqli));
	}

	/**
	 * Получить отдельную статью по её ID
	 */
	public function get_article($id)	{
		return $this->simpleDB->get_first_row('articles', $id);
	}

	/**
	 * Изменить содержимое статьи
	 */
	public function update_atricle($id, $content)	{
		return $this->simpleDB->update('articles', $id, 'content', $content);
	}
}