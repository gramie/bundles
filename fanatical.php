<?php
require_once 'bundle.php';
require_once 'Fetcher.php';

class FanaticalBundle extends Bundle
{
    /**
     * Implement abstract method
     * 
     * @param string $url
     * @return bool|string
     */
    public function getRawData(string $url): string {
        // Fanatical requires a cookie to be set (by visiting the bundle page) before 
        // grabbing the bundle data
        $fetcher = new Fetcher();
        $bundlePage = $fetcher->get('https://www.fanatical.com/en/bundle');
        
        $data = $fetcher->get($url, true);
        return $data;
    }

    /**
     * Implement abstract method
     *  
     * @return void
     */
    public function processRawData(string $rawData): array {
        $data = json_decode($rawData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die(json_last_error_msg());
        }

        $bundles = [];
        foreach ($data as  $product) {
            $bundles[$product['display_type']][] = [
                'title' => $product['name'],
                'start' => $product['available_valid_from'],
                'end' => $product['available_valid_until'],
                'url' => 'https://www.fanatical.com/en/' . $product['type'] . '/' . $product['slug'],
            ];
        }
        return $bundles;
    }

    /**
     * Implement abstract method
     * 
     * @param array $item
     * @return string[]
     */
    public function renderBundleItem(array $item): array {
        return [
            '<a href="' . $item['url'] . '" target="_new">' . $item['title'] . '</a>',
            date('Y-m-d', $item['start']),
            $this->getEndDays($item['end']),
        ];
    }

    /**
     * Implement abstract method
     * 
     * @return string[]
     */
    public function getBundleColumns(): array {
        return ['Title', 'Start', 'End'];
    }

    /**
     * Implement abstract method
     * 
     * @return array
     */
    public function getNewestBundles(): array {
        $result = [];

        foreach ($this->processedData as $type => $products) {
            foreach ($products as $product) {
                $result[$product['start']] = $product;
            }
        }
        usort($result, function($a, $b) {
            return $a['start'] - $b['start'];
        });
        return array_slice($result, -$this->newestCount);
    }
}
