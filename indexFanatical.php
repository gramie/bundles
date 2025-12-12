<?php
error_reporting(E_ALL);
date_default_timezone_set('America/Toronto');


$data = file_get_contents('https://www.fanatical.com/api/algolia/bundles?altRank=false');
$bundleData = json_decode($data, true);

$bundles = [];
foreach ($bundleData as $bundle) {
	$bundles[$bundle['display_type']][] = [
		'title' => $bundle['name'],
		'from' => $bundle['available_valid_from'],
		'to' => $bundle['available_valid_until'],
		'url' => 'en/' . $bundle['type'] . '/' . $bundle['slug'],
	];
}
?>
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
<h1>Fanatical Offerings</h1>
<?php
foreach ($bundles as $bundleType => $bundle) {
	renderTable($bundleType, $bundle);
}
function renderTable($type, $data) {
	echo '<h2>' . $type . '</h2>';
	echo '<table class="bundle-table display"><thead><tr><th>Title</th><th>Start</th><th>End</th></tr></thead>';
	echo '<tbody>';
	foreach ($data as $product) {
		echo '<tr><td>' . implode('</td><td>', renderItem($product)) . '</td></tr>';
	}
	echo '</tbody></table>';
}

function renderItem($item) {
	$result = [];
	

	$end = formatTimeRemaining($item['to']);
	$result = [
		'tile_short_name' => '<a href="https://www.fanatical.com' . $item['url'] . '" target="_blank">' . $item['title'] . '</a>',
		'start' => date('Y-m-d', $item['from']),
		'end' => $end,
	];
	return $result;	
}

function formatTimeRemaining($endTime) {
	$result = '';
	$endVal = $endTime - strtotime('now');
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
	let table = new DataTable('.bundle-table', { order: [[1, 'desc']], pageLength: 50 });
	
});
</script>
</body>
</html>
