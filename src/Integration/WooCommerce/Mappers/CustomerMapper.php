<?php


namespace Corals\Modules\Woo\Integration\WooCommerce\Mappers;


use Corals\Modules\Woo\Integration\WooCommerce\Models\Customer;
use Corals\User\Models\User;
use Illuminate\Support\Str;

class CustomerMapper extends BaseMapper
{

    public function init($option = [])
    {
        $this->setWooModelClass(Customer::class);
        $this->setModelClass(User::class);
    }

    public function getMappingArray(): array
    {
        return [
            'id' => 'object_reference',
            'email' => 'email',
            'first_name' => 'name',
            'last_name' => 'last_name',
            'date_created' => ['column' => 'created_at', 'cast_to' => 'carbon'],
            'date_modified' => ['column' => 'updated_at', 'cast_to' => 'carbon'],
        ];
    }

    /**
     * @param $data
     * @param $wooModel
     */
    public function preStoreUpdateModel(&$data, $wooModel)
    {
        $data['password'] = Str::random(8);

        $data['address'] = [
            'billing' => [
                "address_1" => data_get($wooModel, 'billing.address_1'),
                "address_2" => data_get($wooModel, 'billing.address_2'),
                "city" => data_get($wooModel, 'billing.city'),
                "state" => data_get($wooModel, 'billing.state'),
                "zip" => data_get($wooModel, 'billing.postcode'),
                "country" => data_get($wooModel, 'billing.country'),
            ],
            'shipping' => [
                "address_1" => data_get($wooModel, 'shipping.address_1'),
                "address_2" => data_get($wooModel, 'shipping.address_2'),
                "city" => data_get($wooModel, 'shipping.city'),
                "state" => data_get($wooModel, 'shipping.state'),
                "zip" => data_get($wooModel, 'shipping.postcode'),
                "country" => data_get($wooModel, 'shipping.country'),
            ],
        ];
    }

    /**
     * @param $model
     * @param $data
     * @param $wooModel
     */
    public function postStoreUpdateModel($model, $data, $wooModel)
    {
        $model->assignRole('member');
    }

    protected function getValidationRules($data, $model): array
    {
        return [
//            'name' => 'required|max:191',
//            'last_name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users,email' . ($model ? (',' . $model->id) : ''),
        ];
    }
}
