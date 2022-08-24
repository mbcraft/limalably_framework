<?php


abstract class LMysqlAbstractInsertOrReplaceStatement extends LMysqlAbstractQuery {
	
	protected $table_name;
	protected $insert_data_connector = "";
	protected $column_list;
	protected $data;


	public function __construct($table_name,$column_list=null,$data=null) {

		if (!is_string($table_name)) throw new \Exception("Table name is not a valid string. ".$table_name." found.");
		$this->table_name = $table_name;
		
		if ($column_list!==null) {
			if (is_array($column_list)) {
				if (empty($column_list)) throw new \Exception("The column list of the mysql ".$this->statement_name()." statement is empty");
				$column_list = new LMysqlElementList(... $column_list);
			}
			if (is_string($column_list)) $column_list = new LMysqlElementList([$column_list]);
			
			ensure_instance_of("mysql column list in ".$this->statement_name()." statement",$column_list,[LMysqlElementList::class]);
			
			$this->column_list = $column_list;
		}

		if ($data) {
			$this->data($data);
		}
	}

	protected abstract function statement_name();

	public function column_list(... $column_list) {

		if (count($column_list)>1) {
			$this->column_list = new LMysqlElementList($column_list);

			return $this;
		}

		if (count($column_list)==1) {
			if (is_string($column_list[0])) {
				$this->column_list = new LMysqlElementList([$column_list[0]]);

				return $this;	
			} else {
				$this->column_list = new LMysqlElementList(... $column_list[0]);

				return $this;
			}
		}

		throw new \Exception("Invalid column list in mysql insert statement");
	}

	public function data($data) {

		if ($data instanceof LMysqlSelectStatement) {
			$this->insert_data_connector = "";

			$my_data = $data;
		}
		else
			$this->insert_data_connector = "VALUES";

		if (is_array($data))
		{
			if (empty($data)) throw new \Exception("data array is empty in mysql ".$this->statement_name()." statement");
			if(is_array($data[0])) 
			{
				$my_data = new LMysqlElementListList(... $data);
			} else {
				$keys = array_keys($data);

				if (is_string($keys[0]))
					$my_data = new LMysqlNameValuePairList(... $data);
				else
					$my_data = new LMysqlElementList(... $data);
			}
		}

		if ($this->statement_name() == "insert") {
			ensure_instance_of("mysql data of insert statement",$my_data,[LMysqlSelectStatement::class,LMysqlElementList::class,LMysqlElementListList::class]);
		} else {
			ensure_instance_of("mysql data of replace statement",$my_data,[LMysqlSelectStatement::class,LMysqlElementList::class,LMysqlElementListList::class,LMysqlNameValuePairList::class]);
		}
		$this->data = $my_data;

		return $this;
	}
}