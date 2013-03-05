<?php

require_once("config.php");
global $DB_HOST, $DB_USERNAME, $DB_PASSWORD;

$dbh=mysql_connect ($DB_HOST, $DB_USERNAME, $DB_PASSWORD) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("gellius"); 

?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="styles.css" />
</head>
<body>

<?
/*
$d=$_REQUEST['d'];
if ($d) {
$sql = "SELECT text FROM texts WHERE tid='$d'";
$res = mysql_query($sql);
list($b) = mysql_fetch_row($res);
echo "<p>$b</p>";
$sql = "DELETE FROM texts WHERE tid='$d'";
$res = mysql_query($sql);
echo "<p>$res</p>";

}*/

?>


<table border="1">
<?
$sql = "SELECT tid,type,book,chapter,text FROM texts ORDER BY book ASC, chapter ASC, type DESC";
$res = mysql_query($sql);

if (!$res) {echo "error"; }

while (list($tid,$type,$book, $chapter, $text) = mysql_fetch_row($res)){


/*
list($bbook, $bchapter, $btext) = mysql_fetch_row($res);

echo "<tr><th>$hbook:$hchapter</th><td>$bbook:$bchapter</td>";

if ( ($hbook==$bbook) and ($hchapter==$bchapter) ) {
echo "<td>:)</td>";
} else {
echo "<td>XXX</td>";
}

echo "</tr>\n";
*/

echo "<tr><th>$tid</th><td>$book:$chapter</td><td>$text</td></tr>\n";


/*
$hl = strlen($htext)/10;
$bl = strlen($btext)/100;
echo "<tr><th>$hbook</th><td>$hchapter</td><td><img src='images/red.gif' height='10' width='$hl' /> <img src='images/red.gif' height='10' width='$bl' /></td></tr>";
*/



/*
 echo "<tr><td><a href='list.php?d=$tid'>[X]</a></td><th>$tid</th><td>$type</td><td>$hbook</td><td>$hchapter</td><td>$htext</td></tr>";
*/



/* $tl = strlen($text) / 10;
 echo "<tr><td><a href='addboth.php?tid=$tid'>[X]</a></td>"; 
 echo "<td>$book</td><td>$chapter</td><td><img src='images/red.gif' height='10' width='$tl' /></td></tr>\n";
*/


/* echo "<tr><td><img src='images/red.gif' height='10' width='$tl' /></td></tr>";
*/
}
?>
</table>



</body>

</html>