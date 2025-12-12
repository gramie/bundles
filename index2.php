<!DOCTYPE html>
<html>
<head>
	<title>Humble Bundles</title>
	<link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.css" />
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
	<style>
	body {
		background-color: lavender;
		font-family: sans-serif;
	}
	table {
		background-color: floralwhite;
	}
	h2 {
		text-transform: capitalize;
	}
	h2 a {
		font-size: 80%;
	}
	</style>
</head>
<body>
<h1>Humble Bundle Offerings</h1>
<?php
error_reporting(E_ALL);
date_default_timezone_set('America/Toronto');

$cachedDataFilename = 'bundle.data';

$clearCache = array_key_exists('clear', $_GET);

// echo "Filedate is " . date('Y-m-d h:i:s', filectime($cachedDataFilename)) . "<br />";
// echo "Now is " . date('Y-m-d h:i:s', strtotime('now'));
$cacheIsStale =  filectime($cachedDataFilename) < strtotime('-1 day');
if ($cacheIsStale) {
	echo 'Cache is stale';
}

if ($clearCache || !file_exists($cachedDataFilename) || $cacheIsStale) {
	$rawData = file_get_contents('https://www.humblebundle.com/bundles');
	file_put_contents($cachedDataFilename, $rawData);
}

$cacheDate = filectime($cachedDataFilename);
echo "<h2>Updated " . date('F j, Y g:i A', $cacheDate) . ' <a href="/humble/?clear=1">Refresh</a></h2>';

$html = file($cachedDataFilename);

for ($i = 0; $i < count($html); $i++) {
	$line = $html[$i];
	if (strpos($line, 'landingPage-json-data') > 0) {
		$data = json_decode($html[$i+1], true);
		break;
	}
}

if ($data) {

	foreach (['books', 'games', 'software'] as $type) {
		renderTable($type, $data);
	}
	// print_r($data);
	// die();
} else {
	echo "No data!";
}
echo "\n";

function renderTable($type, $data) {
	echo '<h2>' . $type . '</h2>';
	echo '<table class="bundle-table display"><thead><tr><th>Title</th><th>Description</th><th>Start</th><th>End</th></tr></thead>';
	echo '<tbody>';
	foreach ($data['data'][$type]['mosaic'][0]['products'] as $product) {
		$data[] = renderItem($product);
		echo '<tr><td>' . implode('</td><td>', renderItem($product)) . '</td></tr>';
	}
	echo '</tbody></table>';
	print_r($data, true);
}

function renderItem($item) {
	$result = [];
	

	$start = strtotime($item["start_date|datetime"]);
	$end = formatTimeRemaining($item['end_date|datetime']);
	$result = [
		'tile_short_name' => '<a href="https://www.humblebundle.com' . $item['product_url'] . '" target="_blank">' . $item['tile_short_name'] . '</a>',
		'short_marketing_blurb' => $item['short_marketing_blurb'],
		'start' => date('Y-m-d', $start),
		'end' => $end,
	];
	return $result;	
}

function formatTimeRemaining($endTime) {
	$result = '';
	$endVal = strtotime($endTime) - strtotime('now');
	if ($endVal < 0) {
		return '<span style="color: red">Finished</span>';
	}
	
	$endDays = floor($endVal / 3600/24);
	$endHours = floor(($endVal - $endDays * 3600*24) / 3600);

	if ($endDays > 0) {
		$result .= $endDays . 'd ' . $endHours . 'h';
	} else {
		$result .= '<span style="color: orange">' . $endHours . 'h</span>';
	}

	return $result;
}
?>
<script>
$(document).ready(function() {
	let table = new DataTable('.bundle-table', { order: [[2, 'desc']], pageLength: 50 });
	
});
</script>
</body>
</html>