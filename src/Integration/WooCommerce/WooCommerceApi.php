<?php

namespace Corals\Modules\Woo\Integration\WooCommerce;

use Corals\Modules\Woo\Integration\Http\Client;
use Corals\Modules\Woo\Integration\WooCommerce\Traits\WooCommerceTrait;

class WooCommerceApi
{
    use WooCommerceTrait;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Build Woocommerce connection.
     *
     * @return void
     */
    public function __construct()
    {
        try {
            $this->headers = [
                'header_total' => config('wooconfig.header_total') ?? 'X-WP-Total',
                'header_total_pages' => config('wooconfig.header_total_pages') ?? 'X-WP-TotalPages',
            ];

            $this->client = new Client(
                config('wooconfig.store_url'),
                config('wooconfig.consumer_key'),
                config('wooconfig.consumer_secret'),
                [
                    'version' => 'wc/' . config('wooconfig.api_version'),
                    'wp_api' => config('wooconfig.wp_api_integration'),
                    'verify_ssl' => config('wooconfig.verify_ssl'),
                    'query_string_auth' => config('wooconfig.query_string_auth'),
                    'timeout' => config('wooconfig.timeout'),
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }
}
