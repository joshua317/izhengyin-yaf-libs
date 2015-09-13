<?php
/**
 * PDO 数据访问接口
 * @date 2014-8-25
 * @author zhengyin <zhengyin.name@gmail.com>
 * [DESC]
 *    该文件由Dao继承
 */
 
namespace IZY\Db\Dao;

/**
 * Pdo 工程驱动类
 */
use IZY\Db\Driver\PdoFactory;

/**
 * DB 异常类
 */
use IZY\Sys\Exception\DbException;


abstract class PdoAbstract
{
	protected  $pdo = null;
	
	private static $expr = array('=','!=','<','>','<=','>=');
	
	protected $result;
	
	private $pconnect;
	
	
	//初始化
	public function __construct($pconnect = false)
	{
		$this->pconnect = $pconnect;
	}
	
	/**
	 * 插入数据
	 * @param  $data
	 * @return int
	 */
	public function insert($data)
	{
		$pdo = $this->getInstance();
		
		if(!is_array($data) || empty($data) || !is_string(key($data))) 
			throw new DbException('insert data is invalid!');
		$fields = '';
		$params = '';
		foreach ($data as $field=>$value){
			$fields .='`'.$field.'`,';
			$params .=':'.$field.',';
		}
		//拼接预准备sql
		$sql = '';
		$sql .='INSERT INTO '.$this->tableName().'('.rtrim($fields,',').') VALUES('.rtrim($params,',').')';
		$stmt = $pdo->prepare($sql);
		foreach ($data as $field=>$v){
			$stmt->bindParam(':'.$field, $data[$field]);
		}
		$affecteds = $stmt->execute();
		$insertId = $pdo->lastInsertId();
		return $insertId?$insertId:$affecteds;
	}
	
	/**
	 * 数据更新
	 * @param  $where
	 * @param  $data
	 */
	public function update($where,$data)
	{
		$pdo = $this->getInstance();
		
		if(empty($data) || !is_array($data)){
			throw new DbException('update data is invalid!');
		}
		
		//拼接Sql
		$sqlFields = '';
		$sqlWheres = '';
		
		//组合字段
		foreach ($data as $field=>$value){
			if(is_numeric($field)){
				throw new DbException('update data is invalid!');
			}
			$sqlFields .='`'.$field.'`=:'.$field.',';
		}
		
		//组合Where
		$where = $this->joinWhere($where);
		$sqlWheres = $where[0];
		$params = $where[1];
		if(empty($sqlWheres) || empty($params)){
			throw new DbException('update where is empty!');
		}
		
		//拼接完整的Sql信息
		$sql = 'UPDATE '.$this->tableName().' SET ';
		$sql .= rtrim($sqlFields,',').' WHERE '.$sqlWheres;
		
		//预准备方式执行Sql语句
		$stmt = $pdo->prepare($sql);
		foreach ($params as $k=>$v){
			$stmt->bindParam(':'.$k,$params[$k]);
		}
		foreach ($data as $field=>$v){
			$stmt->bindParam(':'.$field, $data[$field]);
		}
		return $stmt->execute();
	}
	
	/**
	 * 通过Sql查询结果
	 * @param  $sql
	 * @param  $where
	 */
	public function fetchAllBySql($sql,$where=null)
	{
		return $this->query($sql, $where);
	}
	
	/**
	 * 取得单条结果集
	 * @param  $sql
	 * @param string $where
	 */
	public function fetchRowBySql($sql,$where=null)
	{
		return current($this->query($sql, $where));
	}
	
	/**
	 * 通过Sql直接删除
	 * @param  $sql
	 * @param  $where
	 */
	public function deleteBySql($sql,$where)
	{
		return $this->exec($sql, $where);	
	}
	/**
	 * 通过Sql直接更新
	 * @param  $sql
	 * @param  $where
	 * @return multitype:
	 */
	public function updateBySql($sql,$where)
	{
		return $this->exec($sql, $where);
	}
	
	
	/**
	 * 发送查询语句
	 * @param  $sql
	 */
	private function query($sql,$where)
	{
		$pdo = $this->getInstance();
		
		$result = array();
		try {
			if(is_array($where) && !empty($where))
			{
				$stmt =$pdo->prepare($sql);
				foreach ($where as $field=>$v)
				{
					$stmt->bindParam($field,$where[$field]);
				}
				$stmt->execute();
				$this->result = $stmt;
			}else{	//普通查询
				$this->result = $pdo->query($sql);
			}
			$result = $this->result->fetchAll(\PDO::FETCH_ASSOC);
		}catch (\PDOException $e){
			throw new DbException($e->getMessage());
		}
		return $result;
	}
	
	
	/**
	 * 发送执行语句
	 * @param  $sql
	 */
	private function exec($sql,$where)
	{
		$pdo = $this->getInstance();
		$result = array();
		try {
			if(is_array($where) && !empty($where))
			{
				$stmt = $pdo->prepare($sql);
				foreach ($where as $field=>$v)
				{
					$stmt->bindParam($field,$where[$field]);
				}
				$result = $stmt->execute();
			}else{	
				throw new DbException('缺少 WHERE!');
			}
		}catch (\PDOException $e){
			throw new DbException($e->getMessage());
		}
		return $result;
	}
	
	/**
	 * 拼接Where
	 * @param  $where
	 */
	private function joinWhere($where)
	{
		$sqlWhere = '';
		$params = array();
		if(isset($where['k'])) $where = array($where);
		foreach ($where as $v)
		{
			if(empty($v['k']) || empty($v['v']) ||  empty($v['s']) || !in_array($v['s'],self::$expr))
				throw new DbException('where is invalid!');
			$sqlWhere .= ' `'.$v['k'].'`'.$v['s'].':'.$v['k'].' AND';
			$params[$v['k']] = $v['v'];
		}
		$sqlWhere = trim($sqlWhere,'AND');
		return array($sqlWhere,$params);
	}
	
	/**
	 * 取得Pdo实例
	 * @throws DbException
	 */
	private function getInstance(){
		
		if($this->pdo === null){
			try {
				$this->pdo = PdoFactory::instance($this->dbName(),$this->pconnect);
			}catch (\PDOException $e){
				throw new DbException($e->getMessage());
			}
		}
		return $this->pdo;
	}
	
	public abstract function dbName();
	public abstract function tableName();
}


