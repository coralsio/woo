<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Mappers;


use Corals\Modules\Woo\Integration\WooCommerce\Models\Tag;

class TagMapper extends BaseMapper
{
    public function init($options = [])
    {
        $modelClass = "\Corals\\Modules\\{$options["module"]}\\Models\\Tag";

        $this->setWooModelClass(Tag::class);
        $this->setModelClass(new $modelClass);
    }

    public function getMappingArray(): array
    {
        return [
            'id' => 'object_reference',
            'name' => 'name',
            'slug' => 'slug',
        ];
    }

    protected function getValidationRules($data, $model): array
    {
        $tableName = (new $this->modelClass)->getTableName();

        return [
            'name' => "required|unique:" . "$tableName,name" . ($model ? (',' . $model->id) : ''),
            'slug' => "required|unique:" . "$tableName,slug" . ($model ? (',' . $model->id) : ''),
        ];
    }
}
