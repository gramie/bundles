<?php
require_once 'bundle.php';

class FanaticalBundle extends Bundle
{
    /**
     * Implement abstract method
     * 
     * @param string $url
     * @return bool|string
     */
    public function getRawData(string $url): string {
        $data = file_get_contents($url);
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
                'url' => 'en/' . $product['type'] . '/' . $product['slug'],
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
             date('Y-m-d', $item['end']),
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
     * @param int $bundleCount
     * @return array
     */
    public function getNewestBundles(int $bundleCount = 5): array {
        $result = [];

        foreach ($this->processedData as $type => $products) {
            foreach ($products as $product) {
                $result[$product['start']] = $product;
            }
        }
        ksort($result);
        return array_slice($result, -$bundleCount);
    }
}
