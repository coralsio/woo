<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Mappers;

use Corals\Modules\Woo\Integration\WooCommerce\Models\Order;

class OrderMapper extends BaseMapper
{
    protected $options;

    public function init($options)
    {
        $this->options = $options;

        $modelClass = "\Corals\\Modules\\{$options["module"]}\\Models\Order";

        $this->setWooModelClass(Order::class);

        $this->setModelClass(new $modelClass);
    }

    public function getMappingArray(): array
    {
        $result = [
            'id' => 'object_reference',
            'number' => 'order_number',
            'total' => 'amount',
            'currency' => 'currency',

            'shipping.first_name' => 'shipping.shipping_address.first_name',
            'shipping.last_name' => 'shipping.shipping_address.last_name',
            'shipping.company' => 'shipping.shipping_address.company',
            'shipping.address_1' => 'shipping.shipping_address.address_1',
            'shipping.address_2' => 'shipping.shipping_address.address_2',
            'FIXED::shipping' => 'shipping.shipping_address.type',
            'shipping.city' => 'shipping.shipping_address.city',
            'shipping.state' => 'shipping.shipping_address.state',
            'shipping.postcode' => 'shipping.shipping_address.zip',
            'shipping.country' => 'shipping.shipping_address.country',

            'billing.first_name' => 'billing.billing_address.first_name',
            'billing.last_name' => 'billing.billing_address.last_name',
            'billing.email' => 'billing.billing_address.email',
            'billing.phone' => 'billing.billing_address.phone_number',
            'billing.company' => 'billing.billing_address.company',
            'billing.address_1' => 'billing.billing_address.address_1',
            'billing.address_2' => 'billing.billing_address.address_2',
            'FIXED::billing' => 'billing.billing_address.type',
            'billing.city' => 'billing.billing_address.city',
            'billing.state' => 'billing.billing_address.state',
            'billing.postcode' => 'billing.billing_address.zip',
            'billing.country' => 'billing.billing_address.country',
            'transaction_id' => 'billing.payment_reference',
            'payment_method' => 'billing.gateway',
            'date_created' => ['column' => 'created_at', 'cast_to' => 'carbon'],
            'date_modified' => ['column' => 'updated_at', 'cast_to' => 'carbon'],
        ];

        if (array_search('Marketplace', $this->options)) {

            $storeId = $this->getMapperOption('store_id');

            if (!$storeId) {
                throw new \Exception('Invalid Store Id');
            }

            $result ["FIXED::$storeId"] = 'store_id';
        }

        return $result;

    }

    public function preStoreUpdateModel(&$data, $wooModel)
    {
        $this->handleOrderStatus($data, $wooModel);
        $this->handleOrderCustomer($data, $wooModel);
    }

    /**
     * @param \Corals\Modules\Marketplace\Models\Order $model
     * @param $data
     * @param $wooModel
     * @throws \Exception
     */
    public function postStoreUpdateModel($model, $data, $wooModel)
    {
        $model->items()->delete();

        $orderItems = [];
        $lineItems = data_get($wooModel, 'line_items', []);

        $variationMapper = new VariationMapper($this->options,null, null);

        foreach ($lineItems as $lineItem) {
            $sku = $variationMapper->getModelIfExists(data_get($lineItem, 'variation_id'));

            if (!$sku) {
                $variationMapper->setProductIds(data_get($lineItem, 'product_id'));

                $sku = $variationMapper->fetchByIdAndMapping(data_get($lineItem, 'product_id'),
                    data_get($lineItem, 'variation_id'));
            }

            if (!$sku) {
                throw new \Exception('Error while loading item: ' . json_encode((array)$lineItem));
            }

            $orderItems[] = [
                'amount' => data_get($lineItem, 'subtotal') / data_get($lineItem, 'quantity'),
                'description' => data_get($lineItem, 'name'),
                'quantity' => data_get($lineItem, 'quantity'),
                'sku_code' => data_get($lineItem, 'sku'),
                'type' => 'Product',
                'item_options' => []
            ];
        }

        $taxLines = data_get($wooModel, 'tax_lines');

        foreach ($taxLines as $taxLine) {
            $orderItems[] = [
                'amount' => data_get($taxLine, 'tax_total') + data_get($taxLine, 'shipping_tax_total'),
                'description' => data_get($taxLine, 'label'),
                'quantity' => 1,
                'sku_code' => data_get($taxLine, 'rate_code'),
                'type' => 'Tax',
                'item_options' => []
            ];
        }

        $shippingLines = data_get($wooModel, 'shipping_lines');

        foreach ($shippingLines as $shippingLine) {
            $orderItems[] = [
                'amount' => data_get($shippingLine, 'total'),
                'description' => data_get($shippingLine, 'method_title'),
                'quantity' => 1,
                'sku_code' => data_get($shippingLine, 'id'),
                'type' => 'Shipping',
                'item_options' => []
            ];
        }

        $couponLines = data_get($wooModel, 'coupon_lines');

        foreach ($couponLines as $couponLine) {
            $orderItems[] = [
                'amount' => -1 * data_get($couponLine, 'discount'),
                'description' => data_get($couponLine, 'code'),
                'quantity' => 1,
                'sku_code' => data_get($couponLine, 'code'),
                'type' => 'Discount',
                'item_options' => []
            ];
        }

        $feeLines = data_get($wooModel, 'fee_lines');

        foreach ($feeLines as $feeLine) {
            $orderItems[] = [
                'amount' => data_get($feeLine, 'total'),
                'description' => data_get($feeLine, 'name'),
                'quantity' => 1,
                'sku_code' => data_get($feeLine, 'id'),
                'type' => 'Fee',
                'item_options' => []
            ];
        }

        $model->items()->createMany($orderItems);
    }

    protected function getValidationRules($data, $model): array
    {
        return [
            'user_id' => 'required',
            'order_number' => 'required',
            'status' => 'required',
        ];
    }

    protected function handleOrderStatus(&$data, $wooModel)
    {
        $orderStatus = 'pending';
        $paymentStatus = 'pending';

        switch ($wooModel['status']) {
            case 'cancelled':
            case 'refunded':
                $orderStatus = 'canceled';
                break;
            case 'shipped':
            case 'completed':
                $orderStatus = 'completed';
                break;
            case 'on-hold':
            case 'pending':
            case 'custom-pending':
                $orderStatus = 'pending';
                break;
            case 'failed':
                $orderStatus = 'failed';
                break;
            case 'processing':
                $orderStatus = 'processing';
                break;
        }

        if ($wooModel['status'] == 'refunded') {
            $paymentStatus = 'refunded';
        } elseif (data_get($wooModel, 'date_paid')) {
            $paymentStatus = 'paid';
        }

        $data['status'] = $orderStatus;

        data_set($data, 'billing.payment_status', $paymentStatus);
    }

    protected function handleOrderCustomer(&$data, $wooModel)
    {
        $customerMapper = new CustomerMapper();

        $customerModel = $customerMapper->getModelIfExists(data_get($wooModel, 'customer_id'));

        if (!$customerModel) {
            $customerModel = $customerMapper->fetchByIdAndMapping(data_get($wooModel, 'customer_id'));
        }

        if ($customerModel) {
            $data['user_id'] = $customerModel->id;
        }
    }
}
