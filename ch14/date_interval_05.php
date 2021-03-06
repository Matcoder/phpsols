<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Recurring Dates with the DatePeriod Class</title>
<style>
body {
	background-color:#FFF;
	color:#000;
	font-family:"Lucida Sans Unicode", "Lucida Grande", sans-serif;
}
h1 {
	font-size:150%;
}
p {
	margin:0 20px;
	font-size:90%;
}
</style>
</head>

<body>
<h1>Second Tuesday of Each Month in 2011</h1>
<p>
<?php
$start = new DateTime('12/31/2010');
$interval = DateInterval::createFromDateString('second Tuesday of next month');
$end = new DateTime('12/31/2011');
$period = new DatePeriod($start, $interval, $end, DatePeriod::EXCLUDE_START_DATE);
foreach ($period as $date) {
	echo $date->format('l, F jS, Y') . '<br>';
}
?>
</p>
</body>
</html>