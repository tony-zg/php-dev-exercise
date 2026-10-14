<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use Cake\Http\Client;
use RuntimeException;

class DinggoApiClient
{
    private Client $http;

    public function __construct(?Client $http = null)
    {
        $this->http = $http ?? new Client();
    }

    public function fetchCars(): array
    {
        $baseUrl = Configure::read('Dinggo.baseUrl');
        $username = Configure::read('Dinggo.username');
        $key = Configure::read('Dinggo.key');

        foreach ([$baseUrl, $username, $key] as $setting) {
            if (!is_string($setting) || trim($setting) === '') {
                throw new RuntimeException('Set the Dinggo API URL, username and key before fetching cars.');
            }
        }

        try {
            // Passing an array sends the credentials as form fields in the POST body.
            $response = $this->http->post(rtrim($baseUrl, '/') . '/cars', [
                'username' => $username,
                'key' => $key,
            ], [
                'timeout' => 15,
                'redirect' => false,
            ]);
        } catch (\Exception) {
            throw new RuntimeException('Could not reach the Dinggo API. Please try again.');
        }

        if (!$response->isOk()) {
            throw new RuntimeException('Dinggo cars request failed (HTTP ' . $response->getStatusCode() . ').');
        }

        $data = $response->getJson();
        if (!is_array($data)) {
            throw new RuntimeException('Dinggo returned an invalid JSON response.');
        }

        if (($data['success'] ?? null) !== 'ok') {
            throw new RuntimeException('Dinggo did not report a successful cars request.');
        }

        if (!isset($data['cars']) || !is_array($data['cars']) || !array_is_list($data['cars'])) {
            throw new RuntimeException('Dinggo returned an invalid cars list.');
        }

        return $data['cars'];
    }
}
