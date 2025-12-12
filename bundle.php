<?php
abstract class Bundle {
    var $bundleSource;
    var $rawData;
    var $processedData;
    var $updateTime;

    var $defaultSortCol = 0;

    public function __construct($name, $url, $clearCache = false) {
        $this->bundleSource = $name;

        $cacheFilename = "$name.json";
        $cacheDateLimit = strtotime('-1 day', time());

        if (file_exists($cacheFilename) && ($clearCache || (filectime($cacheFilename) < $cacheDateLimit))) {
            unlink($cacheFilename);
        }

        if (file_exists($cacheFilename)) {
            $this->processedData = json_decode(file_get_contents($cacheFilename), true);
        } else {
            $this->rawData = $this->getRawData($url);
            $this->processedData = $this->processRawData($this->rawData);
            file_put_contents($cacheFilename, json_encode($this->processedData));
        }
        $this->updateTime = filectime($cacheFilename);
    }

    /**
     * Render all the bundles from a single source (e.g. Humble Bundles, Fanatical)
     * 
     * @return string
     */
    public function renderBundles(): string {
        $result = '';

        // Show a "5 newest bundles"
        $result .= $this->renderSingleBundle('newestbundles', $this->getNewestBundles());

        foreach ($this->processedData as $name => $bundle) {
            $result .= $this->renderSingleBundle($name, $bundle) . "\n";
        }

        return $result;
    }

        /**
     * Render the products in a single bundle
     * 
     * @return string
     */
    private function renderSingleBundle(string $bundleType, array $bundleData) : string {
        $result = "<h2>$bundleType</h2>"
            . '<table id="' . $bundleType . '-table" class="bundle-table display ' . $this->bundleSource . 'bundletable">'
            . '<thead><tr><th>'
            . implode('</th><th>', $this->getBundleColumns())
            . '</th></tr></thead>'
            . '<tbody>';
        
        foreach ($bundleData as $item) {
            $result .= '<tr><td>' . implode('</td><td>', $this->renderBundleItem($item)) . '</td></tr>';
        }

        $result .= '</tbody></table>';
        return $result;
    }

    /**
     * Get data from an external source (humblebundles.com, etc.)
     * 
     * @param string $url
     * @return string
     */
    abstract public function getRawData(string $url) : string;

    /**
     * Take the raw data and convert it into an array of products
     * 
     * @param string $rawData
     * @return array
     */
    abstract public function processRawData(string $rawData) : array;

    /**
     * Get the columns that are displayed for this bundle source's products
     * @return array
     */
    abstract public function getBundleColumns() : array;

    /**
     * Render a single item from a bundle
     * 
     * @param array $item
     * @return array
     */
    abstract public function renderBundleItem(array $item) : array;

    /**
     * Go through a bundle source's bundles and get the newest 5
     * @return array
     */
    abstract public function getNewestBundles(int $bundleCount = 5) : array;
}