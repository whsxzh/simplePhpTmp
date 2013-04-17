<?php
//by 席占宏 qq：4262833 2012.12.15 
/*
功能说明：
1.支持{_key}变量的替换，替换为数组的value
2.支持{_IF key}.....{ENDIF}条件语句 = < > #(不等于) {_IF key=value} {_IF key=_value}
3.支持{_FOREACH key}...{ENDFOR}循环语句
4.支持{_key.k1.k2}的多层数组变量替换
5.支持{_0}数字下标的普通变量的替换
6.支持条件和循环语句的多层嵌套
7.支持单字符操作符表达式 .|=><#+- * /  .表示下一级 |表示变量链接 #表示不等于 =是逻辑相等 操作符后面的变量要用_开始，常量不用

发明目的
  实现基于数组（数据）的编程
	程序分为界面	业务逻辑	后台数据 三层
	一般总是实现如下交互
		1.界面返回数据给业务逻辑 HTTP协议已经实现 POST，GET，COOKIE数组返回给业务
		2.业务逻辑处理或得到数据后返回给界面 本模板实现show（tmp，Array）
		3.业务逻辑从后台读取数据 SQL封装接口实现 return Array getData
		4.业务逻辑写入数据到后台 SQL封装接口实现 saveData(table,Array)
		     
		界面《===》业务逻辑《==》后台数据

*/

function msg($msg)
{
print ( "<script language=JavaScript>\nalert('".$msg."')\n </script> ");
//exit;
}

function show($filename,$PArray)//如果不能正确替换一般是下标出错或者模板文件不存在
{
	$file=file_get_contents($filename);
	//echo 
	@REPE($file,$PArray);	
}



function REPE($file,$PArray) 
{
	$head="";
	$end=$file;
	$pos=strpos($end,"{_");
	
	if(! is_array($PArray))//解决层数组的问题
			$PArray=array($PArray);
			
	$i=0;	
	while($pos&&$i<100)//限定100个变量，超过100个停止，防止死循环
	{
		echo substr($end,0,$pos);
		$end=substr($end,$pos);
		if(substr($end,0,4)=="{_IF")//处理条件语句
		{
			$pos=findEndPos($end,"{_IF","{ENDIF}");//有可能找不到 故意留的错误，便于判断标签的完整性
			//if(!$pos) echo "IFIF";
			$tmp=substr($end,0,$pos);
			echo IFE($tmp,$PArray);
			//$end=strstr($end,"{ENDIF}");
			$end=substr($end,$pos+7);
			
		}
		elseif(substr($end,0,9)=="{_FOREACH")//处理循环语句
		{	$pos=findEndPos($end,"{_FOREACH","{ENDFOR}");
			//if(!$pos)  echo "FORFOR";

			//echo $pos;
			$tmp=substr($end,0,$pos);
			echo FORE($tmp,$PArray);
			//$end=strstr($end,"{ENDFOR}");
			$end=substr($end,$pos+8);			
		}
		else//处理普通替换
		{
			$pos=strpos($end,"}");
			//if(!$pos) echo "RRR";

			
			$key=substr($end,2,$pos-2);
				//echo $key;
				
			if($tmp=getArrValue($key,$PArray))//$PArray[$key]
			{
				echo $tmp;
					
			}
			$end=substr($end,$pos+1);//+1
		
		}
		
		$pos=strpos($end,"{_");

			
			$i++;
			
			//if($i==99)
			//	echo "9999999999999999999999999999999999";
	}
	
	//return $head.$end;
	echo $end;
}

function findEndPos($str,$start,$end)//找到开始位置对应的结束位置
{
	$pos=1;
	$times=0;
	$spos=2;
	$epos=3;
	
	//echo "s ";
	while($pos&&$times<1&&$spos<$epos)
	{
		if($pos=strpos($str,$start,$spos))
		{
			$times-=1;
			$spos=$pos+1;
		}
		if($pos=strpos($str,$end,$epos))
		{
			$times+=1;
			$epos=$pos+1;
		}
		//echo $pos;
			
	}
	
	//echo "e ";
	return $pos; 
}

function getArrValue($key,$PArray)
{
//判断是否包含=><#
	 similar_text($key,".|=><#+-*/",$per); 
	 if($per==0)
	 {	//echo " 2s ".$key.'=>'.$PArray[$key];
		if($PArray[$key])
			return $PArray[$key];
		else
			return false;
	}
	
	if($pos=strpos($key,"."))
	{
		//echo " 1s ";
		$k=substr($key,0,$pos);
		if($PArray[$k])
			return getArrValue(substr($key,$pos+1),$PArray[$k]);
		else
			return false;
	}
	elseif($oppos=strpos($key,"|"))
		$op=".";	
	elseif($oppos=strpos($key,"="))
		$op="==";
	elseif($oppos=strpos($key,">"))
		$op=">";
	elseif($oppos=strpos($key,"<"))
		$op="<";
	elseif($oppos=strpos($key,"#"))
		$op="!=";	
	elseif($oppos=strpos($key,"+"))
		$op="+";
	elseif($oppos=strpos($key,"-"))
		$op="-";
	elseif($oppos=strpos($key,"*"))
		$op="*";
	elseif($oppos=strpos($key,"/"))
		$op="/";	
	
	if($oppos)
	{		
		$key1=substr($key,$oppos+1);
		$key=substr($key,0,$oppos);
		$value=getArrValue($key,$PArray);
		if(substr($key1,0,1)=="_")//说明是变量
			$value1=getArrValue(substr($key1,1),$PArray);
		else
			$value1=$key1;
		
		eval("\$str='".$value."'".$op."'".$value1."';");
		//echo $str;
		return $str;
	}	
	else
	{
		return false;
	}
}

function FORE($str,$PArray)//模板循环
{
	//$str=$file;//strstr($file,"{_FOREACH");
	
	$pos=strpos($str,"}");
	$key=substr($str,10,$pos-10);
		
	$retstr="";
	if($tmp=getArrValue($key,$PArray))
	{
		$str=substr($str,$pos+1);
		foreach($tmp as $d)
		{
			
			$retstr.=REPE($str,$d)."\n";		
		}
	}
	else
		return "";
	
	return $retstr;
}

function IFE($str,$PArray)//条件模板
{
	//$str=$file;//strstr($file,"{_IF");
	$pos=strpos($str,"}");
	$key=substr($str,5,$pos-5);
		
	if(getArrValue($key,$PArray))
	{	
		$str=substr($str,$pos+1);
		return REPE($str,$PArray);
	}
	else
		return "";
}

//获取标记 动态生成 加载 类 设置名称 id 值
function getHtmlStr($str,$keystr,$htmltype="div")//获取代码段 如id="abc",class="aaa",前后搜索长度
{
	$pos=strpos($str,$keystr);
	$head=substr($str,0,$pos);
	$head=strrchr($head,"<".$htmltype);
	$foot=substr($str,$pos);
	$pos=strpos($foot,"</".$htmltype);
	$len=strlen("</".$htmltype);
	$foot=substr($foot,0,$pos+$len);
	return $head.$foot;
}

function getUi($uiname,$str)//{_UI $uiname}  {ENDUI}
{
	$pos=strpos($str,"{_UI $uiname}");
	$pos2=strpos($str,"{ENDUI}",$pos+1);
	$head=substr($str,$pos+strlen("{_UI $uiname}"),$pos2-$pos-strlen("{_UI $uiname}"));
	return $head ;
}

function setUi($uistr,$PArray)
{
	return REPE($uistr,$PArray);
}

function setHtmlStr($str,$PArray)//设置代码段变量并返回设置后的字符串
{
	return REPE($str,$PArray);
}

?>
