<?php

$dbh=mysql_connect ("localhost", "root", "root") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("noctes"); 
$mode = $_REQUEST["mode"];
$wtype = $_REQUEST["wtype"];
$and = $_REQUEST["and"];
$rbook = $_REQUEST["book"];
$rnaid = $_REQUEST["naid"];
$searchword = $_REQUEST["searchword"];
$searchone = $_REQUEST["searchone"];
$searchtwo = $_REQUEST["searchtwo"];
$searchlist = $_REQUEST["searchlist"];
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="styles.css" />
<?
if ($searchword) {
 echo "<title>$searchword</title>";
}
?>
</head>
<body>
<? 

/* We decide which function the user wants by checking the $mode querystring.  The most useful mode is called map. */
/* Some view modes remain active here but have been commented out of topbar.php, which contains the function menu. */

if ($mode!="printready") {
include "topbar.php"; 
}

?>

<?


if ($mode=="listall") { /* LISTALL*/
?>
<table border="1">
<?
if ($rbook) {
$sql = "SELECT naid,book,chap,head,body FROM atticae WHERE book=$rbook ORDER BY book ASC, chap ASC";
} else {
$sql = "SELECT naid,book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
}

$res = mysql_query($sql);

if (!$res) {echo "error"; }

while (list($naid,$book,$chap,$head,$body) = mysql_fetch_row($res)){

echo "<tr><th>$naid</th><td><a href='index.php?mode=listall&book=$book'>[$book]</a></td><td>$chap</td><td class='small'>$head</td><td class='wee'>$body</td></tr>\n";

}
?>
</table>

<?
} elseif ($mode=="printready") { /* PRINTREADY*/

echo "<center><h1>Noctes Atticae Auli Gellii</h1>\n";
echo "<h3>text from Lacus Curtius</h3></center>\n";

$asql = "SELECT naid,book,chap FROM atticae ORDER BY book ASC, chap ASC";

$ares = mysql_query($asql);

echo "<center>\n";
while (list($naid,$book,$chap) = mysql_fetch_row($ares)){
	
	if ($book != $lbook) {
		$anch = "#b".$book;
		echo "<a href='$anch'>Book $book</a>";
		echo "<br />\n";
	}
/*	$anch = "#".$book."_".$chap;
	echo "<a href='$anch'>[$book.$chap]</a>&nbsp;";*/
	$lbook = $book;
}
echo "</center>\n";

$sql = "SELECT naid,book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";

$res = mysql_query($sql);

if (!$res) {echo "error"; }

while (list($naid,$book,$chap,$head,$body) = mysql_fetch_row($res)){

	if ($book != $lastbook) {
		$anch = "b".$book;
		echo "<hr />\n<a name='$anch'><h1>Book $book</h1></a>";
		echo "<center>";
		echo "<a href='#top'>Top of Text</a><br />\n";
		$bsql = "SELECT naid,chap FROM atticae WHERE book=$book ORDER BY chap ASC";
		$bres = mysql_query($bsql);
		while (list($bnaid,$bchap) = mysql_fetch_row($bres)) {
			$anch = "#".$book."_".$bchap;
			echo "<a href='$anch'>[$book.$bchap]</a>&nbsp;";
		}
		echo "</center>";

	}
	$anch = $book."_".$chap;
	echo "<a name='$anch'><h2>$book.$chap</h2></a>";
	echo "<center>$head</center>\n";
	echo "<p>$body</p>\n";
	$anch = "#b".$book;
	echo "<center><a href='$anch'>Top of Book</a></center>";
	echo "<center><a href='#top'>Top of Text</a></center>";
	echo "<hr />\n";
	$lastbook = $book;
	$lastchap = $chap;
}

?>

<?
} elseif ($mode=="index") { /* INDEX*/

?><div><form action='index.php' method='post'><input type='hidden' name='mode' value='index' />search: <input type='text' name='searchword' value='<? echo $searchword; ?>' /><input type='submit' /></form></div>
<?
$sql = "SELECT naid,book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";

$res = mysql_query($sql);

$prevbook = -1;
	echo "<div>";
while (list($naid,$book,$chap,$head,$body) = mysql_fetch_row($res)){
	if ($prevbook != $book) {
		echo "</div><div class='liber'>\n";
		echo "<a name='$book'><h1>$book</h1></a>";
	}
	$heiho = str_word_count($body) / 10;
	if ($heiho < 10) { $heiho = 10; }
	$col = "green";
	 if ($searchword) {
	 	if ( strpos(strtolower($body),strtolower($searchword)) OR strpos(strtolower($head),strtolower($searchword))) {
	 	$col = "red";
	 	}
	 }


//	echo "<a href='index.php?mode=indiv&naid=$naid'>";
	echo "<a class='wee' href='edit.php?naid=$naid'>";
	echo "$chap <img src='$col.gif' height='$heiho' width='40' /></a>\n";
	$prevbook = $book;
	$inc ++;
}

echo "</div>";
echo $inc;







} elseif ($mode=="lengths") { /*LENGTHS*/

if ($rbook) {
$sql = "SELECT naid,book,chap,head,body FROM atticae WHERE book=$rbook ORDER BY book ASC, chap ASC";
$hei=20;
} else {
$sql = "SELECT naid,book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
$hei=6;
}

$res = mysql_query($sql);
$prevbook=-1;

$bodyfactor = 5;

?>
<p><a href="index.php?mode=lengths&and=">all</a> | <a href="index.php?mode=lengths&and=justavg">just avgs</a></p>
<table id="lengths"><?
while (list($naid,$book,$chap,$head,$body) = mysql_fetch_row($res)) {

if ($prevbook != $book) {

	$bha = $bhto/$binc;
	$bba = $bbto/$binc;
	$bba = $bba / $bodyfactor;
	echo "<tr class='avg'><td></td>";
	echo "<td><img src='blue.gif' height='$hei' width='$bha' alt='$alt' /></td>";
	echo "<td><img src='red.gif' height='$hei' width='$bba' alt='$alt' /></td></tr>\n";
	echo "<tr><td colspan='3'><a href='index.php?mode=lengths&book=$book'>LIBER $book</a></td></tr>\n";
	$binc = 0;
	$bhto = 0;
	$bbto = 0;

}
$hl = str_word_count($head);
$bl = str_word_count($body);


$hto = $hto + $hl;
$bto = $bto + $bl;
$inc ++;
$bhto = $bhto + $hl;
$bbto = $bbto + $bl;
$binc ++;

$hl = $hl / 1;
$bl = $bl / $bodyfactor;

$ratio = (str_word_count($head)/str_word_count($body))*100;
if ($ratio>100) {$ratio=0;}

$alt = "$book:$chap";

if ($and != "justavg"){
echo "<tr><td><img src='green.gif' height='$hei' width='$ratio' /></td>";
echo "<td><a class='len' href='index.php?mode=indiv&naid=$naid'><img src='blue.gif' height='$hei' width='$hl' alt='$alt' /></a></td>";
echo "<td><a class='len' href='index.php?mode=indiv&naid=$naid'><img src='red.gif' height='$hei' width='$bl' alt='$alt' /></a></td></tr>\n";
}

$prevbook = $book;
}
$ha = $hto/$inc;
$ba = $bto/$inc;
$ba = $ba / 10;
echo "<tr><td colspan='3'>avg</td></tr>\n";
echo "<tr class='avg'><td></td>";
echo "<td><img src='blue.gif' height='$hei' width='$ha' alt='$alt' /></td>";
echo "<td><img src='red.gif' height='$hei' width='$ba' alt='$alt' /></td></tr>\n";
?></table><?

} elseif ($mode == "indiv") { /* INDIVIDUAL ENTRY DISPLAY */
if ($rnaid) {

$sql = "SELECT book,chap,head,body FROM atticae WHERE naid=$rnaid";
$res=mysql_query ($sql);
list ($book,$chap,$head,$body) = mysql_fetch_row($res);

chapout ($rnaid,$book,$chap,$head,$body);

}else {
echo "no naid?";
}


}elseif ($mode=="tindex") { /* TEXTUAL INDEX */

$sql = "SELECT naid,book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
$res = mysql_query($sql);

$prevbook = 0;
while (list($naid,$book,$chap,$head,$body) = mysql_fetch_row($res)){
	if ($prevbook != $book) {
		if ($prevbook != 0) { echo "</div>\n"; }
		echo "<h2><a name='$naid'>LIBER $book</h2>\n<div class='tin_book'>\n";
	}
	$body = str_replace("\n","<br />",$body);
	echo "<div class='tin_chapframe'><a name='' class='tin_chap'>Chapter $chap: <span class='tin_head'>$head</span>\n<span class='tin_chabo'>$body</span></a></div>\n";
	$prevbook = $book;
}

}elseif ($mode=="graphical") {   /* GRAPHICAL DISPLAY - IN DEVELOPMENT */

$w = $_REQUEST[w];
if ($w==0) { $w = 20; }

$prev = 0;

if ($rbook) {
$sql = "SELECT naid,book,chap,head,body FROM atticae WHERE book=$rbook ORDER BY book ASC, chap ASC";
} else {
$sql = "SELECT naid,book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
}

$res = mysql_query($sql);

echo "<div>";
while (list($naid,$book,$chap,$head,$body) = mysql_fetch_row($res)) {

if ($prev != $book) {
 echo "</div><div class='graphical_book'>";
}

$chapleng = str_word_count($body);
$chapheigh = $chapleng/$w;
$chapwidth = $w;

$chapheigh = $chapheigh * 10;
$chapwidth = $chapwidth * 10;

echo "<div class='graphical_chapter'>\n";
echo "<img src='blue.gif' width='$chapwidth' height='8' class='graphical_chapter' />";
echo "<img src='red.gif' width='$chapwidth' height='$chapheigh' class='graphical_chapter' alt='$naid' />";
echo "\n</div>\n";

$prev = $book;
}
echo "</div>";


} elseif ($mode=="search") { /* TEXT SEARCH */

?><div><form action='index.php?' method='post'><input type='hidden' name='mode' value='search' />search: <input type='text' name='searchword' value='<? echo $searchword; ?>' /><input type='submit' /></form></div>
<?

$sql = "SELECT book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
$res = mysql_query($sql);

$behemoth = "";

while (list($book,$chap,$head,$body) = mysql_fetch_row($res)) {
	
	if ( strpos(strtolower($body),strtolower($searchword)) OR strpos(strtolower($head),strtolower($searchword))) {
//	if ( strpos($body,$searchword) OR strpos($head,$searchword)) {
		$body = str_replace($searchword, "<span class='hl'>$searchword</span>",$body);
		$head = str_replace($searchword, "<span class='hl'>$searchword</span>",$head);
		echo "<div><h1>book $book chapter $chap</h1><h2>$head</h2><div>$body</div></div>";
	}
}

if ($searchword) {
 $behemoth = str_replace($searchword, "<span class='hl'>$searchword</span>", $behemoth);
}

} elseif ($mode=='coinc') { /* COINCIDENCE */

?><div><form action='index.php?' method='post'><input type='hidden' name='mode' value='coinc' />search: <input type='text' name='searchone' value='<? echo $searchone; ?>' /><input type='text' name='searchtwo' value='<? echo $searchtwo; ?>' /><input type='submit' /></form></div>
<?

$sql = "SELECT book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
$res = mysql_query($sql);

$behemoth = "";

while (list($book,$chap,$head,$body) = mysql_fetch_row($res)) {
	
	if
	(( strpos(strtolower($body),strtolower($searchone)) OR strpos(strtolower($head),strtolower($searchone)) ) AND ( strpos(strtolower($body),strtolower($searchtwo)) OR strpos(strtolower($head),strtolower($searchtwo)) ))

	{

		$body = str_replace($searchone, "<span class='hl'>$searchone</span>",$body);
		$head = str_replace($searchone, "<span class='hl'>$searchone</span>",$head);

		$body = str_replace($searchtwo, "<span class='hlb'>$searchtwo</span>",$body);
		$head = str_replace($searchtwo, "<span class='hlb'>$searchtwo</span>",$head);


		echo "<div><h1>book $book chapter $chap</h1><h2>$head</h2><div>$body</div></div>";
	}
}

} elseif ($mode=='multi') { /* MULTIPLE word search - never completed, now obsolete*/

?><div><form action='index.php?' method='post'><input type='hidden' name='mode' value='multi' />search: <textarea name='searchlist' rows='1' cols='60'><? echo $searchlist; ?></textarea><input type='submit' /></form></div>
<?

$swords = ($searchlist);

echo $swords;


/*
$sql = "SELECT book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
$res = mysql_query($sql);


while (list($book,$chap,$head,$body) = mysql_fetch_row($res)) {
	
	if
	(( strpos(strtolower($body),strtolower($searchone)) OR strpos(strtolower($head),strtolower($searchone)) ) AND ( strpos(strtolower($body),strtolower($searchtwo)) OR strpos(strtolower($head),strtolower($searchtwo)) ))

	{

		$body = str_replace($searchone, "<span class='hl'>$searchone</span>",$body);
		$head = str_replace($searchone, "<span class='hl'>$searchone</span>",$head);




		echo "<div><h1>book $book chapter $chap</h1><h2>$head</h2><div>$body</div></div>";
	}
}
*/

} elseif ($mode=='all') { /*ALL OF IT*/

?><div><form action='index.php?' method='post'><input type='hidden' name='mode' value='all' />search: <input type='text' name='searchword' value='<? echo $searchword; ?>' /><input type='submit' /></form></div>
<?


$sql = "SELECT book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
$res = mysql_query($sql);

$behemoth = "";

while (list($book,$chap,$head,$body) = mysql_fetch_row($res)) {
	$behemoth = $behemoth . "b" . $book . "c" . $chap . $head . "//" . $body;
}

if ($searchword) {
 $behemoth = str_replace($searchword, "<span class='hl'>$searchword</span>", $behemoth);
}

echo "<div id='all'>";
echo $behemoth;
echo "</div>";





} elseif ($mode=='sortedbytypes') { 

$sql = "SELECT naid,book,chap,head FROM atticae ORDER BY book ASC, chap ASC";
$res = mysql_query($sql);

$typsql = "SELECT type_id, type_name FROM types ORDER BY type_id";
$typres = mysql_query($typsql);
echo "<table border='1' style='font-size: 1em;'>\n";
$typecounts = array();
$types = array();
$typeinc = 0;
echo "<tr><th></th>";
while (list ($type_id,$type_name) = mysql_fetch_row($typres)) {
	$typecounts[$typeinc] = $type_id;
	$types[$type_id] = $type_name;
	echo "<th>$type_name</th>";
	$col = random_color();
	$typecolor[$type_id] = $col;
	$typebg[$type_id] = color_inverse($col);
	$typeinc ++;
}

while (list ($naid,$book,$chap,$head) = mysql_fetch_row($res)) {

	echo "<tr><th><a name='$naid'>$book.$chap</a></th>";
	for ($bobo = 0 ; $bobo < $typeinc ; $bobo ++) {
		$intsql = "SELECT type FROM notes WHERE ch='$naid' and TYPE='$typecounts[$bobo]'";
		$intres = mysql_query($intsql);
		list ($type) = mysql_fetch_row($intres);
		if ($type) {
			echo "<td style='background-color: $typebg[$type]; color: $typecolor[$type];'>$types[$type]</td>\n";
		} else {
			echo "<td></td>\n";
		}
	}

	echo "</tr>\n";

}
echo "<table>\n";

/*

} elseif ($mode=='chartbytypes') { 

$typsql = "SELECT type_id, type_name FROM types ORDER BY type_id";
$typres = mysql_query($typsql);
echo "<table border='1' style='font-size: 1em;'>\n";
$typecounts = array();
$types = array();
$typeinc = 0;
echo "<p>";
while (list ($type_id,$type_name) = mysql_fetch_row($typres)) {
	$typecounts[$typeinc] = $type_id;
	$types[$type_id] = $type_name;
	$col = random_color();
	$typecolor[$type_id] = $col;
	$typebg[$type_id] = color_inverse($col);
	$typeinc ++;
	echo "<span style='padding: .5em; background-color: $typecolor[$type_id]; color: $typebg[$type_id];'>$type_name</span>";
}
echo "</p>\n";




$sql = "SELECT naid,book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
$res = mysql_query($sql);
?><div style='font-size: 8pt;'><?
echo "<span>";
while (list($naid,$book,$chap,$head,$body) = mysql_fetch_row($res)){
	if ($book!=$lastbook) {
#		echo "<p></p>";
		echo "</span><span class='chartbook'>";
	}
	$intsql = "SELECT type FROM notes WHERE ch = '$naid' AND kind = 'type'";
	$intres = mysql_query($intsql);
	list ($type) = mysql_fetch_row($intres);
	if (!$type) {
		$z = "#ffffff";
	}else {
		$z = $typecolor[$type];
	}
	$c = strlen($body);
	$d = $c / 200;
	$d = 4;
	echo "<span class='chart' style='background-color: $z;'><a class='chart' href='edit.php?naid=$naid'>";
	for ($q=0;$q<$d;$q++) {
		echo "&nbsp; ";
	}
	echo "<div class='detailbar'>$book.$chap:$head</div>";
	echo "</a></span>";
	$lastbook = $book;
}
#echo "<p></p>";
echo "</span>";
?></div><?

*/
} elseif ($mode=='map') { /* THIS IS THE MOST USEFUL MODE */

/* to-do list
-install tags
--make this chart color by tags instead
-create a chapter-specific view that shows the rest of the book
-get some english added
-figure out how to deal with multiple types?

-search mode

*/

$typsql = "SELECT type_id, type_name, type_color FROM types ORDER BY type_id";
$typres = mysql_query($typsql);
#echo "<table border='1' style='font-size: 1em;'>\n";
$typecounts = array();
$types = array();
$typeinc = 0;


echo "<div style='margin: 2em; font-size: .5em;'><div style='padding: .5em;'>";
while (list ($type_id,$type_name,$type_color) = mysql_fetch_row($typres)) {
	$typecounts[$typeinc] = $type_id;
	$types[$type_id] = $type_name;
#	$col = random_color();
	$typecolor[$type_id] = $type_color;
	$typebg[$type_id] = color_inverse($col);
	$typeinc ++;
	#echo "<span style='padding: .5em; background-color: $typecolor[$type_id]; color: $typebg[$type_id];'><a href='index.php?mode=map&wtype=$type_id'>$type_name</a></span>";
	#^^^ this line listed all the TYPES being used; i've disabled it for migration online (types remain visually interesting but we need a better tagging functionality to make them useful; my naming conventions are also deprecated at this point and will distract student users
}
echo $wtype;
?></div>


<div><form action="index.php" method="post"><input type="hidden" name="mode" value="map" /><input name="searchword" type="text" value="<? echo $searchword ?>" /><input type="submit" value="search"/></form></div>
<?
echo "</div>\n";




$sql = "SELECT naid,book,chap,head,body FROM atticae ORDER BY book ASC, chap ASC";
$res = mysql_query($sql);
?><div style='font-size: 8pt;'><?
$hitcount = 0;
while (list($naid,$book,$chap,$head,$body) = mysql_fetch_row($res)){
	if ($book!=$lastbook) {
#		echo "<p></p>";
		echo "</span><span class='chartbookCOL'><div class='bookheadCOL'>$book</div>";
	}
	
	$intsql = "SELECT type FROM notes WHERE ch = '$naid' AND kind = 'type'";
	$intres = mysql_query($intsql);
	list ($type) = mysql_fetch_row($intres);
	if (!$type) {
		$z = "#ffffff";
	}else {
		$z = $typecolor[$type];
	}
	$sear = "";


	 if ($searchword) {
	 	if ( strpos(strtolower($body),strtolower($searchword)) OR strpos(strtolower($head),strtolower($searchword))) {
	 	$sear = "style='background-image: url(mesh.gif);'";
	 	$hitcount ++;
	 	$hitlist = $hitlist . "($book.$chap), ";
	 	}
	 }
	 
	 if ($type==$wtype) {
	 	$sear = "style='background-image: url(mesh.gif);'";
	 	$hitcount ++;
	 	$hitlist = $hitlist . "($book.$chap), ";
	 }

	$typetotals[$type] ++;

	$c = strlen($body);
	$d = $c / 125;
	$f = 10;
	if ($d<$f){$d=$f;}
	$searchpass ="";
	if ($searchword){
	 	if ( strpos(strtolower($body),strtolower($searchword)) OR strpos(strtolower($head),strtolower($searchword))) {
	 	$searchpass = "&searchword=$searchword";
	 	}	
	}
	echo "<a style='display: block; background-color: $z;' class='chartCOL' href='index.php?mode=singleview&naid=$naid$searchpass'>";
	echo "<img $sear src='transparent.png' width='100%' height='$d' />";
	echo "<div class='detailbarCOL'><span class='labelCOL'>$book.$chap:</span> $head<br />";
	#echo "<span class='labelCOL'>narrative type:</span> $types[$type]";
	

/* we don't want to show the "thoughts" notes because we wrote them five years ago and while they are of academic interest some of them are mad as snakes
	$notsql = "SELECT what FROM notes WHERE kind='thought' AND ch='$naid'";
	$notres = mysql_query($notsql);
	if ($notres) {
#		echo "THOUGHT HERE";
		while (list($thought) = mysql_fetch_row($notres)) {
			echo "<br /><span class='labelCOL'>thought:</span> <q>$thought</q>";
		}
	}
#	echo "<br />$notres";
*/
	echo "</div>";
	echo "</a>";
	$lastbook = $book;
}
#echo "<p></p>";
echo "</span>";
?></div>

<div>
<?
for ($tortoise = 0;$tortoise<$typeinc;$tortoise++) {
	echo "<p>$typetotals[$tortoise]</p>";
}

?>

</div>
<div>hits: <? echo $hitcount; ?></div>
<div><? echo $hitlist; ?></div>

<?

} elseif ($mode=='singleview') { 

$sql = "SELECT chap, book, head, body FROM atticae WHERE naid='$rnaid'";
$res = mysql_query($sql);
list ($chap,$book,$head,$body) = mysql_fetch_row($res);

if ($searchword) {
	if ( strpos(strtolower($body),strtolower($searchword)) OR strpos(strtolower($head),strtolower($searchword))) {
//	if ( strpos($body,$searchword) OR strpos($head,$searchword)) {
		$body = str_replace($searchword, "<span class='hl'>$searchword</span>",$body);
		$head = str_replace($searchword, "<span class='hl'>$searchword</span>",$head);
	}
}

echo "<div style='padding: 2em; margin: 1em; margin-bottom: 10%; border: 1px solid black;'>
<div><a href='index.php?mode=map' style='display: block; width: 100%; border: 5px solid red; background-color: #FFcccc; text-align: center; font-size: 2em; color: black;'>back to map</a></div>
<h2>$book.$chap</h2>\n<div>$head</div>";

?>

<!-- THIS WAS THE FORM FOR ANNOTATION AND ADDING NOTES, DISABLED FOR MIGRATION ONLINE


<div style="float: right; display: block; width: 25%; border: 1px solid red; background-color: #ffcccc; padding: .5em; font-size: .75em;">



<form method="post" action="notes.php">
<input type="hidden" name="mode" value="noteadded" />
<input type="hidden" name="chapnoted"value="<? echo $rnaid; ?>" />

<p><input type="radio" value="type" name="kindofnote" checked>type note</input></p>
<div style="border: 1px solid grey;">
<p>what type is it? <select name="typenoted">

<?

$tsql = "SELECT type_id,type_name FROM types";
$tres = mysql_query($tsql);
while (list($ttype_id,$ttype_name) = mysql_fetch_row($tres)) {
echo "<option VALUE='$ttype_id'>$ttype_name";
}


?>

</select><br />
<input type="checkbox" name="cleartypes" CHECKED />overwrite other types?
</div>

<p><input type="radio" value="ref" name="kindofnote" />2ndary reference note</p>
<div style="border: 1px solid grey;">
<table>
<tr><th>who is it</th><td><input type="text" name="ref_whonoted" /></td></tr>
<tr><th>date</th><td><input type="text" name="ref_whennoted" /></td></tr>
<tr><th>other</th><td><input type="text" name="ref_therestnoted" /></td></tr>
<tr><th>say what</th><td><textarea name="whatnoted"></textarea></td></tr>
</table>
</div>

<p><input type="radio" value="thought" name="kindofnote" />personal thought note</p>
<div style="border: 1px solid grey;">
<table>
<tr><th>say what</th><td><textarea name="whatnoted"></textarea></td></tr>
</table>
</div>

<input type="submit" />

</form>


</div>


-->



<?


echo "<div style='margin-top: 1em;font-size: .8em; width: 70%;'>$body</div>\n</div>";

echo "<div style='padding: 5px; position: fixed; width: 99%; height: 10%; top: 90%; background-color: #ffffff; border: 1px solid #cccccc; margin: 0;'>";
$intsql = "SELECT naid,chap,body FROM atticae WHERE book='$book' ORDER BY chap";
$intres = mysql_query($intsql);
$c = mysql_num_rows($intres);
$c = (99/$c);
$c = round($c);
$c = $c-1;

while (list($inaid,$ichap,$ibody)=mysql_fetch_row($intres)){
	$z = random_color();
	if ($ichap==$chap) {
		$h=80;
	} else {
		$h=50;
	}
	
	$tsql = "SELECT type FROM notes WHERE kind='type' AND ch='$inaid'";
	$tres = mysql_query($tsql);
	list ($type) = mysql_fetch_row($tres);
	$csql = "SELECT type_color FROM types WHERE type_id='$type'";
	$cres = mysql_query($csql);
	list ($typecol) = mysql_fetch_row($cres);
#	echo $tsql;
	echo "<div style='background-color: $typecol; width: $c%; display: block; float: left; border: 3px solid black; height: $h%;'><a href='index.php?mode=singleview&naid=$inaid'><img src='transparent.png' width='100%' style='border:0;' /></a></div>";
}

echo "</div>";

} else {

echo "<p>Welcome to the Graphical Engine for Looking at and Learning from Interesting and Unusual Texts (GELLIUS).</p>";
echo "<p>This program uses a text derived from the Lacus Curtius version of the Loeb public domain text.</p>";
echo "<p>May I recommend the <a href='index.php?mode=map'>visual map</a>?</p>";

}
?>

</body>

</html>

<?
function chapout ($naid, $bobo, $chch, $hehe, $dydy) {

$dydy = str_replace ("\n","<br />",$dydy);

echo "<div class='chapter'>\n";
echo "<div class='chapinfo'><span class='book'>book $bobo</span> <span class='chapno'>chapter $chch</span>";
#echo "<a href='edit.php?naid=$naid'>[__EDIT__]</a>";
echo "</div>\n";
echo "<h2 class='chaphead'>$hehe</h2>\n";
echo "<div class='chapterbody'>$dydy</div>\n";
echo "</div>\n";

}


#http://www.jonasjohn.de/snippets/php/random-color.htm
function random_color(){
    mt_srand((double)microtime()*1000000);
    $c = '';
    while(strlen($c)<6){
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}

function color_inverse($color){
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return '000000'; }
    $rgb = '';
    for ($x=0;$x<3;$x++){
        $c = 255 - hexdec(substr($color,(2*$x),2));
        $c = ($c < 0) ? 0 : dechex($c);
        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
    }
    return '#'.$rgb;
}

