<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Mappers;


trait ProductVariationTrait
{
    protected function setStockDetails(&$data, $wooModel)
    {
        $manage_stock = data_get($wooModel, 'manage_stock');
        $stock_quantity = data_get($wooModel, 'stock_quantity');
        $stock_status = data_get($wooModel, 'stock_status');

        if ($manage_stock) {
            if ($stock_quantity) {
                $data['inventory'] = 'finite';
                $data['inventory_value'] = $stock_quantity;
            } else {
                $data['inventory'] = 'bucket';
                $data['inventory_value'] = $stock_status == 'instock' ? 'in_stock' : 'out_of_stock';
            }
        } else {
            $data['inventory'] = 'infinite';
            $data['inventory_value'] = null;
        }
    }

    protected function setShippingDetails(&$data, $wooModel)
    {
        $weight = data_get($wooModel, 'weight');
        $width = data_get($wooModel, 'dimensions.width');
        $height = data_get($wooModel, 'dimensions.height');
        $length = data_get($wooModel, 'dimensions.length');

        $data['shipping'] = [
            'shipping_option' => 'calculate_rates',
            'enabled' => $width && $height && $length && $weight ? 1 : 0,
            'width' => $width,
            'height' => $height,
            'length' => $length,
            'weight' => $weight,
        ];
    }
}
