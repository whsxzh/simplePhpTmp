<?
include "dbcache_pdo.php";
//include "dbcache.php";
// db settings are in config.inc.php

// Establish a database connection.
$mysql_port = "3306";
$mysql_host = "localhost";
if ($mysql_port != 3306) {
$mysql_host = "$mysql_host:$mysql_port";
}
// End NuSphere modification
$mysql_login = "root";
$mysql_password = " ";
$mysql_database = " ";
//http://phpmyadmin.zzw.cn/phpMyAdminTest
ini_set('date.timezone','Etc/GMT-8');//设置时区

$c = mysql_pconnect ( $mysql_host, $mysql_login, $mysql_password );
if ( ! $c ) {
  echo mysql_error()."Error connecting to database!<P>";
  exit;
}

if ( ! mysql_select_db ( $mysql_database ) ) {
  echo "Error selecting \"$mysql_database\" database!<P>" . mysql_error ();
  exit;
}
mysql_query("SET NAMES 'UTF8'"); 
//mysql_query("SET NAMES 'utf8_general_ci'"); //utf8_general_ciUTF8
function getData($sql,$m=0)
{
	if($m>0)//读取缓存
	{
	if($data1=getCache($sql))
		return $data1;
	}
	//return false;
		 $data1=array();
		 if($result=mysql_query($sql))
						  {
						  while($row = mysql_fetch_array($result,MYSQL_ASSOC))
						 		{
									$data1[]=$row;
								}
							}
			else
			{
				 echo json_encode(array("msg"=>"<br>\n".$sql.mysql_error()));	
				
			}
		if($m>0)//保存缓存
			putCache($sql,$data1,$m);
		return $data1;	
}

function getDataO($sql,$m=0)
{
	if($m>0)//读取缓存
	{
	if($data1=getCache($sql))
		return $data1;
	}
	//return false;
		 $data1=array();
		 if($result=mysql_query($sql))
						  {
						  while($row = mysql_fetch_object($result))
						 		{
									$data1[]=$row;
								}
							}
			else
			{
				echo json_encode(array("msg"=>"<br>\n".$sql.mysql_error()));	
				 exit(0);
			}
		if($m>0)//保存缓存
			putCache($sql,$data1,$m);
		return $data1;	
}

function exeSql($sql)
{
		//echo "test ";
		 $result=mysql_query($sql);
			if(!$result)
			{
				 echo $sql.mysql_error();	
				// return false;
				// exit;
			}
				 
		return $result;	
}

function saveData($table,$post)
{
	//读取表结构 主键 
	//Field       | Type         | Null | Key | Default | Extra 
	$PK="";
	$field=array();
	$sql = "describe $table"; 
	
	$re=mysql_query($sql); 
	while( $row=mysql_fetch_array($re))
	{ 
	$field[$row['Field']]=$post[$row['Field']];
	if($row['Key']=='PRI')
		{ 
		$PK=$row['Field'];
		//ECHO $row['Field']."是这个表的主键。"; 
		//break; 
		} 
	}
	
	if($field[$PK])//update
	{
		$sql="update $table set ";
		foreach($field as $k=>$v)
		{
			if($v&&$k!=$PK)
				$sql.="$k='$v',"	;
		}
		$sql=substr($sql,0,-1);
		$sql.=" where $PK='{$field[$PK]}'";		
	}
	else//insert 
	{
		$sql="insert into $table(";
		$fstr="";
		$vstr="";
		foreach($field as $k=>$v)
		{
			if($v)
			{
				$fstr.=$k.",";
				$vstr.="'$v',"	;
			}
		}
		$sql.=substr($fstr,0,-1).") values(".substr($vstr,0,-1).")";
	}
	//echo $sql."<br>\n";
	return exeSql($sql);
}

function saveDataMuti($table,$data) //保存多条数据
{//INSERT INTO table (字段) VALUES (字段值) ,(字段值) ,(字段值) ,(字段值)
	$d1=$data[0];
	$sql="insert into $table(";//(user, answer, itemid) values";
	foreach($d1 as $k=>$v)
	{
		$sql.=$k.",";	
	}
	
	$sql=substr($sql,0,-1).") values";
	
	foreach($data as $d1)
	{
		$sql.="('";
		foreach($d1 as $k=>$v)
		{
			
			$sql.=$v."','";	
		}
		$sql.=substr($sql,0,-2)."),";
		//$sql.="('$user', '{$a[1]}',{$a[0]}),";//
	}
	$sql=substr($sql,0,-1);
	//echo $sql;
	return exeSql($sql);	
	//substr();
}
?>
