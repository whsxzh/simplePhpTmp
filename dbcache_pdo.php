<?
//sqlite 数据缓存

function getmicrotime(){ 

    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
}

function clearCache()
{
	$db=new PDO("sqlite: cache.db");
		if(!$db)
			return false;
	 $db->exec("VACUUM");
}

function getCacheData()
{
	
	$db=new PDO("sqlite: cache.db");
		if(!$db)
		{
			echo "没有找到数据库";
			return false;
		}
	//查询数据库

	$sql="select sql,data,time,m from cache1 ";
	if($result=$db->query($sql))
	{
		$row1=$result->fetchAll();
		
		foreach($row1 as $row)
		{
				$now1=time();//date("Y-m-d H:i"); 
		$dt=strtotime("{$row['time']} +{$row['m']} minutes ");
		echo date("Y-m-d H:i:s",$now1)." ". date("Y-m-d H:i:s",$dt);
		//$row=$row[0];
		echo "\n".$row[0];
		print_r(json_decode($row[1]));
		}
	}
	else
	{
		echo "没有查到数据";
		$sql="CREATE TABLE IF NOT EXISTS cache1(sql text PRIMARY KEY,data BLOB,time datetime,m INTEGER)";
		$result=$db->exec($sql);
		return false;
	}
}

function getCacheObj($sql1)
{
	getCache($sql1,$isArr=false);
}

function getCache($sql1,$isArr=true)
{
	
	$db=new PDO("sqlite: cache.db");
		if(!$db)
			return false;
	//查询数据库
	$sql1=str_replace("'","_",$sql1);
	$sql="select * from cache1 where sql='$sql1'";
	if($result=$db->query($sql))
	{
		$row=$result->fetchAll();
				
		$row=$row[0];
		$now1=time();//date("Y-m-d H:i"); 
		$dt=strtotime("{$row['time']} +{$row['m']} minutes ");
		//echo $dt." tmvs ".$now1." ".$row['time'];
		if($dt<$now1)//如果过时 删除缓存
		{
			$sql="delete from cache1 where sql='$sql1'";	
			$db->exec($sql);
		}
		
		
	
		return json_decode($row['data'],$isArr);
	}
	else
		return false;
}

function putCache($sql1,$data,$minutes)
{
	//echo "put";
	$db=new PDO("sqlite: cache.db");
		if(!$db)
			return false;
	//创建数据库
	//$sql="CREATE TABLE  cache1(sql text PRIMARY KEY,data BLOB,time datetime,m INTEGER)";
	//$result=$db->exec($sql);
	
	
	//查询数据库
	$data=json_encode($data);
	
	$sql1=str_replace("'","_",$sql1);
	$sql="insert into cache1(sql,data,time,m) values('$sql1','{$data}','".date("Y-m-d H:i:s")."',$minutes)";//datetime('now')
	//echo $sql;
	if($result=$db->exec($sql))
	{
		
		return $result;
		
	}
	else
	{
		$sql="CREATE TABLE IF NOT EXISTS cache1(sql text PRIMARY KEY,data BLOB,time datetime,m INTEGER)";
		$result=$db->exec($sql);
		return false;	
	}
}