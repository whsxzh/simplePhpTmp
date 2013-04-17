<?
include "tmp.php";

$a=array("a1"=>"this is a test",
"b1"=>array(
array("a1"=>"sss","a2"=>"2222"),
array("a1"=>"good","a2"=>"bad","nomaile")
),
"b2"=>array(
array("sss","2222"),
array("good","bad","nomaile")
),
"a2"=>"a22"
);

//$file=readf("test.html");
show("test.html",$a);


?>
