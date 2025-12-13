<?php
require_once 'bundle.php';

class HumbleBundle extends Bundle
{
    /**
     * Implement abstract method
     * 
     * @param string $url
     * @return string
     */
    public function getRawData(string $url): string
    {
        $data = file($url);

        for ($i = 0; $i < count($data); $i++) {
            $line = $data[$i];

            if (strpos($line, 'landingPage-json-data') > 0) {
                return $data[$i + 1];
            }
        }

        return '';
    }

    /**
     * Get data into associative array 
     * 
     * @return void
     */
    public function processRawData(string $rawData): array {
        $data = json_decode($rawData, true);

        $bundles = [];
        foreach ($data['data'] as $type => $products) {
            foreach ($products['mosaic'][0]['products'] as $product) {
                $bundles[$type][] = [
                    'title' => $product['tile_short_name'],
                    'url' => 'https://www.humblebundle.com' . $product['product_url'],
                    'description' => $product['short_marketing_blurb'],
                    'start' => strtotime($product['start_date|datetime']),
                    'end' => strtotime($product['end_date|datetime']),
                ];
            }
        }
        return $bundles;
    }

    /**
     * Implement abstract method
     * 
     * @param array $item
     * @return array<mixed|string>
     */
    public function renderBundleItem(array $item): array {
        return [
            '<a href="' . $item['url'] . '" target="_new">' . $item['title'] . '</a>',
            $item['description'],
            date('Y-m-d', $item['start']),
            $this->getEndDays($item['end']),
        ];
    }

    public function getBundleColumns(): array {
        return ['Title', 'Description', 'Start', 'End'];
    }

    /**
     * Implement abstract method
     * 
     * @param int $bundleCount
     * @return array
     */
    public function getNewestBundles(int $bundleCount = 5): array {
        $result = [];

        foreach ($this->processedData as $type => $products) {
            foreach ($products as $product) {
                $result[] = $product;
            }
        }
        usort($result, function($a, $b) {
            return $a['start'] - $b['start'];
        });
        return array_slice($result, -$bundleCount);
    }
}
