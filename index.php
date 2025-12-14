<?php
require_once 'humble.php';
require_once 'fanatical.php';

$forceCacheClear = array_key_exists('clear', $_GET);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Humble Bundles</title>
	<link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.css" />
	<link rel="stylesheet" href="bundle.css" />
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
</head>
<body>
    <div class="tabset">
        <input type="radio" name="tabset" id="tab1" aria-controls="humble" checked>
        <label for="tab1">Humble</label>
        <input type="radio" name="tabset" id="tab2" aria-controls="fanatical">
        <label for="tab2">Fanatical</label>
        <div class="tab-panels">
            <section id="humble" class="tab-panel">
<?php
$url = 'https://www.humblebundle.com/bundles';
$hb = new HumbleBundle('humble', $url, $forceCacheClear);
echo $hb->renderBundles();
?>
            </section>
            <section id="fanatical" class="tab-panel">
<?php
try {
    $url = 'https://www.fanatical.com/api/algolia/bundles?altRank=false';
    $fb = new FanaticalBundle('fanatical', $url, $forceCacheClear);
    echo $fb->renderBundles();
} catch (Exception $e) {
    echo "<div>Blocked by a bot catcher. Please go to <a href='$url'>$url</a> in your browser and then reload this page.</div>";
}
?>
            </section>
        </div>
    </div>

<script>
$(document).ready(function() {
	let humbletable = new DataTable('.humblebundletable', { order: [[2, 'desc']], pageLength: 50, paging: false });
	let fanaticaltable = new DataTable('.fanaticalbundletable', { order: [[1, 'desc']], pageLength: 50, paging: false, info: false });
});
</script>

</body>
</html>