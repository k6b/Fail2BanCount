<?php
echo "<!DOCTYPE html>\n";
echo "<html lang='en'>\n";
echo "<head>\n";
echo "\t<title>Fail2BanCount - by k6b</title>\n";
echo "\t<meta http-equiv='content-type' content='text/html; charset=utf-8' />\n";
echo "\t<meta name='author' content='k6b' />\n";
echo "\t<meta name='description' content='Fail2BanCount - by k6b' />\n";
echo "\t<!-- CSS/STYLESHEET -->\n";
echo "\t<link rel='stylesheet' href='/bancount.css' type='text/css' />\n";
echo "</head>\n";
echo "<body>\n";
echo "<div id='header'>\n";

// Fail2BanCount - Displays information from the database
// I won't claim this is all my original code, as it has
// been borrowed from various places online. Use it as you
// like.

// Database connection info

$db_host = 'localhost';
$db_user = 'fail2bancount';
$db_pwd = 'OJ9tzckQyVqCLzut';
$database = 'fail2bancount';

// Page variable

$page = htmlspecialchars($_GET["page"]);
$orderby = htmlspecialchars($_GET["orderby"]);

// Connect to MySQL

if (!mysql_connect($db_host, $db_user, $db_pwd))
	die("Can't connect to database");

// Select the database

if (!mysql_select_db($database))
	die("Can't select database");

// Get some information from the database

// Find IPs banned more than once

$multiplebans = mysql_query("SELECT ip,COUNT(*) count,country FROM bans GROUP BY ip HAVING count > 1 ORDER BY count DESC");
if (!$multiplebans) {
	die("Query failed.");
}

// Find the IPs currently banned

$currentbans = mysql_query("SELECT ip,ban_date,ban_time,country FROM bans WHERE bans.id NOT IN ( SELECT unbans.id FROM unbans WHERE bans.id=unbans.id)");
if (!$currentbans) {
	die("Query failed.");
}

// Find the total number of IPs banned

$totalbans = mysql_query("SELECT MAX(id) FROM bans");
if (!$totalbans) {
	die("Query failed.");
}
while($row = mysql_fetch_array($totalbans)) {
	$numbans = $row['MAX(id)'];
}

// Find the total number of IPs unbanned

$totalunbans = mysql_query("SELECT MAX(id) FROM unbans");
if (!$totalunbans) {
	die("Query failed.");
}
while($row = mysql_fetch_array($totalunbans)) {
	$numunbans = $row['MAX(id)'];
}

// Find multiple country bans

$countrybans = mysql_query("SELECT country,COUNT(*) count FROM bans GROUP BY country ORDER BY count DESC LIMIT 10");
if (!$countrybans) {
	die("Query failed.");
}

// Display every IP banned

// Order by ID

$allbans = mysql_query("SELECT id,ip,ban_date,ban_time,country FROM bans ORDER BY id");
if (!$multiplebans) {
	die("Query failed.");
}

// Order by IP

$allbans_ip = mysql_query("SELECT id,ip,ban_date,ban_time,country FROM bans ORDER BY ip");
if (!$multiplebans) {
	die("Query failed.");
}

// Order by Date

$allbans_date = mysql_query("SELECT id,ip,ban_date,ban_time,country FROM bans ORDER BY ban_date");
if (!$multiplebans) {
	die("Query failed.");
}

// Order by Time

$allbans_time = mysql_query("SELECT id,ip,ban_date,ban_time,country FROM bans ORDER BY ban_time");
if (!$multiplebans) {
	die("Query failed.");
}

// Order by Country

$allbans_country = mysql_query("SELECT id,ip,ban_date,ban_time,country FROM bans ORDER BY country");
if (!$multiplebans) {
	die("Query failed.");
}


// Find the number of currently banned IP's using subtraction. 
// I'm sure I can do this with a single MySQL query and get 
// rid of the above 2 queries all together.

$currentlybanned = $numbans - $numunbans;

// Print some HTML

echo "\t<h1>Fail2BanCount - by k6b</h1>\n";
echo "</div>\n";
echo "<div id='container'>\n";

echo "\t<h3>$numbans IPs have been banned.</h3>\n";

// Menu

echo "\t<div class='table'>\n";
echo "\t\t<div class='row'>\n";
echo "\t\t\t<a href='/bancount.php?page=home' class='menu'>Home</a>\n";
echo "\t\t\t<a href='/bancount.php?page=allbans' class='menu'>All Bans</a>\n";
echo "\t\t</div>\n";
echo "\t</div>\n";

switch ($page) {

default:

// Begin creating the first table of IPs that have been banned
// more than once.

echo "\t<div class='table'>\n";
echo "\t\t<div class='row'>\n";
echo "\t\t\t<div class='cell-header'>\n\t\t\t\tIP\n\t\t\t</div>\n";
echo "\t\t\t<div class='cell-header'>\n\t\t\t\tBans\n\t\t\t</div>\n";
echo "\t\t\t<div class='cell-header'>\n\t\t\t\tCountry\n\t\t\t</div>\n";
echo "\t\t</div>\n";

// Print the data obtained from the MySQL database

// Print the first table

while($row = mysql_fetch_row($multiplebans)) {
	echo "\t\t<div class='row'>\n";
	foreach($row as $cell)
		echo "\t\t\t<div class='cell'>\n\t\t\t\t$cell\n\t\t\t</div>\n";
	echo "\t\t</div>\n";
}

echo "\t</div>\n";

mysql_free_result($multiplebans);

// Use correct grammer

if ($currentlybanned != 1) {
	$grammer = "IPs are";
} else {
	$grammer = "IP is";
}

echo "\t<h3>Currently $currentlybanned $grammer banned.</h3>\n";

// Only print the second table if we have an IP
// currently banned.

if ($numbans > $numunbans) {

// Print the data obtained from the MySQL database

// Table title

echo "\t<h2>Currently Banned</h2>\n";

// Create the second table, of currently banned IPs

echo "\t<div class='table'>\n";
echo "\t\t<div class='row'>\n";
echo "\t\t\t<div class='cell-header'>\n\t\t\t\tIP\n\t\t\t</div>\n";
echo "\t\t\t<div class='cell-header'>\n\t\t\t\tDate\n\t\t\t</div>\n";
echo "\t\t\t<div class='cell-header'>\n\t\t\t\tTime\n\t\t\t</div>\n";
echo "\t\t\t<div class='cell-header'>\n\t\t\t\tCountry\n\t\t\t</div>\n";
echo "\t\t</div>\n";

// Print out the second table 

while($row = mysql_fetch_row($currentbans)) {
	echo "\t\t<div class='row'>\n";
	foreach($row as $cell)
		echo "\t\t\t<div class='cell'>\n\t\t\t\t$cell\n\t\t\t</div>\n";
	echo "\t\t</div>\n";

}

echo "\t</div>\n";

mysql_free_result($currentbans);

}

// Print more HTML
echo "\t<h2>Top 10 Countries</h2>\n";

// Begin creating the second table of counrtys  that have been banned
// more than once.

echo "\t<div class='table'>\n";
echo "\t\t<div class='row'>\n";
echo "\t\t\t<div class='cell-header'>\n\t\t\t\tCountry\n\t\t\t</div>\n";
echo "\t\t\t<div class='cell-header'>\n\t\t\t\tBans\n\t\t\t</div>\n";
echo "\t\t</div>\n";

// Print the first table



while($row = mysql_fetch_row($countrybans)) {
	echo "\t\t<div class='row'>\n";
	foreach($row as $cell)
		echo "\t\t\t<div class='cell'>\n\t\t\t\t$cell\n\t\t\t</div>\n";
	echo "\t\t</div>\n";
}

echo "\t</div>\n";

mysql_free_result($countrybans);

break;
case "allbans":

echo "\t<div class='table'>\n";
echo "\t\t<div class='row'>\n";
echo "\t\t\t<a href='/bancount.php?page=allbans&amp;orderby=id' class='cell-header'>\n\t\t\t\t\n\t\t\t</a>\n";
echo "\t\t\t<a href='/bancount.php?page=allbans&amp;orderby=ip' class='cell-header'>\n\t\t\t\t<u>IP</u>\n\t\t\t</a>\n";
echo "\t\t\t<a href='/bancount.php?page=allbans&amp;orderby=date' class='cell-header'>\n\t\t\t\t<u>Ban Date</u>\n\t\t\t</a>\n";
echo "\t\t\t<a href='/bancount.php?page=allbans&amp;orderby=time' class='cell-header'>\n\t\t\t\t<u>Ban Time</u>\n\t\t\t</a>\n";
echo "\t\t\t<a href='/bancount.php?page=allbans&amp;orderby=country' class='cell-header'>\n\t\t\t\t<u>Country</u>\n\t\t\t</a>\n";
echo "\t\t</div>\n";

switch ($orderby) {

default:
case "id":

while($row = mysql_fetch_row($allbans)) {
	echo "\t\t<div class='row'>\n";
	foreach($row as $cell)
		echo "\t\t\t<div class='cell'>\n\t\t\t\t$cell\n\t\t\t</div>\n";
	echo "\t\t</div>\n";
}

case "ip":

while($row = mysql_fetch_row($allbans_ip)) {
	echo "\t\t<div class='row'>\n";
	foreach($row as $cell)
		echo "\t\t\t<div class='cell'>\n\t\t\t\t$cell\n\t\t\t</div>\n";
	echo "\t\t</div>\n";
}

break;

case "date":

while($row = mysql_fetch_row($allbans_date)) {
	echo "\t\t<div class='row'>\n";
	foreach($row as $cell)
		echo "\t\t\t<div class='cell'>\n\t\t\t\t$cell\n\t\t\t</div>\n";
	echo "\t\t</div>\n";
}

break;

case "time":

while($row = mysql_fetch_row($allbans_time)) {
	echo "\t\t<div class='row'>\n";
	foreach($row as $cell)
		echo "\t\t\t<div class='cell'>\n\t\t\t\t$cell\n\t\t\t</div>\n";
	echo "\t\t</div>\n";
}

break;

case "country":

while($row = mysql_fetch_row($allbans_country)) {
	echo "\t\t<div class='row'>\n";
	foreach($row as $cell)
		echo "\t\t\t<div class='cell'>\n\t\t\t\t$cell\n\t\t\t</div>\n";
	echo "\t\t</div>\n";
}

break;

}

echo "\t</div>\n";

break;
}

echo "</div>\n";
echo "<div id='footer'>\n";
echo "\t&#169;2012 released under GNU GPL contact <a href='mailto:kyle@kylefberry.net'>k6b</a> with any questions.\n";
echo "</div>\n";
echo "</body>\n";
echo "</html>\n";
?>
