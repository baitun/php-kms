<?php 
/**
* Методы взаимодействия с базой данных
*/
class SimpleDB
{
	public $mysqli;
	
	/**
	 * [__construct description]
	 * @param [type] $db база, к какой таблице коннектиться (old, new, wfm)
	 */
	function __construct($db=null){
		include("config.php");				
		if(!$db) $db="new";
		$config=$configs[$db];
		
		$this->mysqli = mysqli_init();
		$this->mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
		$this->mysqli->real_connect($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']); 
		$this->mysqli->set_charset("utf8");
	}

	public function query($query , $resultmode = MYSQLI_STORE_RESULT){
		return $this->mysqli->query($query);
	}

	public function getMysqli(){
		return $this->mysqli;
	}

	/**
	 * Простое изменение значения ячейки в таблице БД
	 * @param  string $tablename Название таблицы в MySQL
	 * @param  integer $row_id    Во всех таблицах должно быть поле ID, называться оно должно именно так. Данный параметр однозначно определяет строку
	 * @param  string $colname   Название поля (столбца)
	 * @param  [type] $newvalue  Новое значение
	 * @return bool            Возвращает успешность выполнения
	 */
	public function update($tablename, $row_id, $colname, $newvalue){
	  // This very generic. So this script can be used to update several tables.
	  $return=false;
	  if ( $stmt = $this->mysqli->prepare("UPDATE $tablename SET $colname = ? WHERE id = ?")) {
	    $stmt->bind_param("si",$newvalue, $row_id);
	    $return = $stmt->execute();
	    $stmt->close();
	  }
	  return $return;
	}

	public function delete($tablename, $row_id){
		// var_dump($tablename,$row_id);
		$return=false;
		if ( $stmt = $this->mysqli->prepare("DELETE FROM $tablename  WHERE id = ?")) {
			$stmt->bind_param("i", $row_id);			
			$return = $stmt->execute();
			$stmt->close();
		}
		return $return;             
	}

	/**
	 * По данному id возвращает строку из таблицы. Удобно испольовать для того, чтобы получить строку из таблицы по её ID.
	 * Рекомендуется использовать именно этот метот, когда нужно получить только одну уникальную строку таблицы
	 * @param  [type] $tablename [description]
	 * @param  [type] $row_id    [description]
	 * @param  [type] $id_column как называется столбец с ID, по умолчанию `id`   
	 * @return [type]            [description]
	 */
	public function get_first_row($tablename, $row_id, $id_column='id')	{
		$query="SELECT * FROM $tablename where $id_column='$row_id' LIMIT 1";
		$result=$this->mysqli->query($query);
		if($result) return $result->fetch_object();
		else return false;
	}

	/**
	 * Возвращает результаты SELECT запроса в виде массива объектов
	 * @param  [type] $query запрос SELECT
	 * @return [type]        [description]
	 */
	public function get_array($query)	{
		$array=[];
		$rows = $this->mysqli->query($query);
		if($rows){
			while ($object=$rows->fetch_object()) {
				$array[]=$object;
			}
			return $array;
		} else {
			if($this->mysqli->error){
				throw new Exception($this->mysqli->error, 1);				
			}
		}
	}

	/**
	 * Возвращает одномерный массив
	 * в селекте должено быть только одно поле, возвращаемое AS element
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function get_linear_array($query) {
		$array=[];
		$rows = $this->mysqli->query($query);
		if($rows){
			while ($object=$rows->fetch_object()) {
				$array[]=$object->element;
			}
			return $array;
		} else return false;
	}

	/**
	 * Возвращает строку со значениями через запятую, при этом оборачивает их в одинарные кавычки, 
	 * пример: '1','2','3','4'
	 * D селекте должено быть только одно поле, возвращаемое AS element
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function get_imploded($query){
		$array=[];
		$rows = $this->mysqli->query($query);
		if($rows){
			while ($object=$rows->fetch_object()) {
				if(!isset($object->element)) throw new Exception("query must contaion only one field with name 'element'", 1);				
				$array[]="'$object->element'";
			}
			return implode(",", $array);
		} else return false;
	}


	/**
	 * fetch_pairs is a simple method that transforms a mysqli_result object in an array.
	 * It will be used to generate possible values for some columns.
	 * Используется в callbacks.get
	 * TODO перенести в другое место, туда, где все функции
	*/
	public function fetch_pairs($query){
		if (!($res = $this->mysqli->query($query))) return FALSE;
		$rows = array();
		while ($row = $res->fetch_assoc()) {
			$first = true;
			$key = $value = null;
			foreach ($row as $val) {
				if ($first) { $key = $val; $first = false; }
				else { $value = $val; break; } 
			}
			$rows[$key] = $value;
		}
		return $rows;
	}

	public function EscapeMysqli($array)
	{
		foreach($array as $key=>$value) {
			if($key="data") continue;
			$array[$key]=htmlspecialchars($this->mysqli->real_escape_string($value));
		}
		return $array;
	}	
}
?>