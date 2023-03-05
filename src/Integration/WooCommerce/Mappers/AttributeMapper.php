<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Mappers;


use Corals\Modules\Woo\Integration\WooCommerce\Models\Attribute;

class AttributeMapper extends BaseMapper
{
    protected $options;

    public function init($options)
    {
        $this->options = $options;
        $modelClass = "\Corals\\Modules\\{$options["module"]}\\Models\\Attribute";

        $this->setWooModelClass(Attribute::class);

        $this->setModelClass(new $modelClass);
    }

    public function getMappingArray(): array
    {
        return [
            'id' => 'object_reference',
            'name' => 'label',
            'slug' => 'code',
            'type' => 'type',
        ];
    }

    public function setAttributeOptions($options = [])
    {
        $this->options = $options;
    }

    /**
     * @param $data
     * @param $model
     * @return string[]
     */
    protected function getValidationRules($data, $model): array
    {
        $tableName = (new $this->modelClass)->getTableName();

        return [
            'code' => "required|unique:" . "$tableName,code" . ($model ? (',' . $model->id) : ''),
        ];
    }

    /**
     * @param $model
     * @param $data
     * @param $wooModel
     */
    public function postStoreUpdateModel($model, $data, $wooModel)
    {
        $this->updateOrCreateOptions($model);
    }

    public function updateOrCreateOptions($model)
    {
        if ($this->options) {
            foreach ($this->options as $option) {
                $model->options()->updateOrCreate(['option_value' => $option], [
                    'option_order' => 1,
                    'option_display' => $option,
                ]);
            }
        }
    }
}
