<?php
/**
 * Fetch data from a URL with cookies
 */
class Fetcher
{
    public $request_cookies = '';
    public $response_cookies = '';
    public $content = '';

    /**
     * Set the cookies when given them as JSON
     *
     * @param string $cookies
     * @return void
     */
    public function set_cookies_json(string $cookies) {
        $cookies_json = json_decode($cookies, true);
        $cookies_array = [];
        foreach ($cookies_json as $key => $value) {
            $cookies_array[] = $key . '=' . $value;
        }
        $this->request_cookies = 'Cookie: ' . join('; ', $cookies_array) . "\r\n";
    }

    /**
     * Set the cookies when given them as a string
     * 
     * @param string $cookies
     * @return void
     */
    public function set_cookies_string(string $cookies) {
        $this->request_cookies = 'Cookie: ' . $cookies . "\r\n";
    }

    /**
     * Parse out the cookies from a response
     * 
     * @param array $http_response_header
     * @return void
     */
    private function get_cookies(array $http_response_header) {
        $cookies_array = [];
        foreach ($http_response_header as $s) {
            if (preg_match('|^Set-Cookie:\s*([^=]+)=([^;]+);(.+)$|', $s, $parts)) {
                $cookies_array[] = $parts[1] . '=' . $parts[2];
            }
        }

        $this->response_cookies = 'Cookie: ' . join('; ', $cookies_array) . "\r\n";
    }

    /**
     * GET request
     * 
     * @param string $url
     * @param bool $copyCookies Use cookies that were saved in a previous GET/POST
     * 
     * @return bool|string
     */
    public function get(string $url, bool $copyCookies = false): string {
        if ($copyCookies) {
            $this->request_cookies = $this->response_cookies;
        }

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "Accept-language: en\r\n" . $this->request_cookies
            ]
            ];
        return $this->fetch($opts, $url);
    }

    /**
     * POST request

     * @param string $url
     * @param array $post_data
     * @param bool $copyCookies Use cookies that were saved in a previous GET/POST
     * 
     * @return bool|string
     */
    public function post(string $url, array $post_data, bool $copyCookies): string {
        if ($copyCookies) {
            $this->request_cookies = $this->response_cookies;
        }

        $post_content = [];
        foreach ($post_data as $key => $value) {
            $post_content[] = $key . '=' . $value;
        }

        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n" . $this->request_cookies,
                'content' => join('&', $post_content),
            ]
            ];

        return $this->fetch($opts, $url);
    }

    /**
     * Actually fetch the remote contents
     * 
     * @param array $opts
     * @param string $url
     * @return bool|string
     */
    private function fetch(array $opts, string $url) {
        $context = stream_context_create($opts);
        $this->content = file_get_contents($url, false, $context);
        $this->get_cookies($http_response_header);

        return $this->content;
    }
}
