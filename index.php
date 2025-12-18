<?php
require_once 'humble.php';
require_once 'fanatical.php';

define('NEWESTCOUNT', 8);

$forceCacheClear = array_key_exists('clear', $_GET);

$humbleUrl = 'https://www.humblebundle.com/bundles';
$hb = new HumbleBundle('humble', $humbleUrl, $forceCacheClear, NEWESTCOUNT);
$fanaticalUrl = 'https://www.fanatical.com/api/algolia/bundles?altRank=false';
$fb = new FanaticalBundle('fanatical', $fanaticalUrl, $forceCacheClear, NEWESTCOUNT);

$tabs = [
    'humble' => $hb->renderBundles(),
    'fanatical' => $fb->renderBundles(),
];
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
<?php
    foreach (array_keys($tabs) as $idx => $id) {
        $checked = ($idx == 0) ? 'checked' : '';
        echo "<input type='radio' name='tabset' id='tab$id' aria-controls='$id' $checked>";
        echo "<label for='tab$id'>$id</label>";
    }
?>
        <div class="tab-panels">
            <?php 
            foreach ($tabs as $id => $content) {
                    echo "<section id='$id' class='tab-panel'>";
                    echo $content;
                    echo "</section>";
            } 
            ?>
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