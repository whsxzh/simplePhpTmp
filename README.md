simplePhpTmp
============

a simple php template,quicker then  smarty,can use IF FOREACH

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
