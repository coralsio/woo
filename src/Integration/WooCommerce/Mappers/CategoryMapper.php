<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Mappers;


use Corals\Modules\Woo\Integration\WooCommerce\Models\Category;

class CategoryMapper extends BaseMapper
{

    protected $options;

    public function init($options)
    {
        $this->options = $options;
        $modelClass = "\Corals\\Modules\\{$options['module']}\\Models\\Category";

        $this->setWooModelClass(Category::class);
        $this->setModelClass(new $modelClass);
    }

    public function getMappingArray(): array
    {
        return [
            'id' => ['object_reference', 'external_id'],
            'name' => 'name',
            'slug' => 'slug',
            'description' => 'description',
            'date_created' => ['column' => 'created_at', 'cast_to' => 'carbon'],
            'date_modified' => ['column' => 'updated_at', 'cast_to' => 'carbon'],
        ];
    }

    protected function idGetValue(&$data, $wooModel, $mappedTo)
    {
        $id = data_get($wooModel, 'id');

        foreach ($mappedTo as $column) {
            $data[$column] = $id;
        }
    }

    protected function getValidationRules($data, $model): array
    {
        $tableName = (new $this->modelClass)->getTableName();
        return [
            'name' => "required|unique:" . "$tableName,name" . ($model ? (',' . $model->id) : ''),
            'slug' => "required|unique:" . "$tableName,slug" . ($model ? (',' . $model->id) : ''),
        ];
    }

    /**
     * @param $model
     * @param $data
     * @param $wooModel
     */
    public function postStoreUpdateModel($model, $data, $wooModel)
    {
        if ($parent = data_get($wooModel, 'parent')) {
            $mapper = new CategoryMapper($this->options);

            $mapper->setMapperOption('doUpdateIfExists', false);

            $parentCategory = $mapper->fetchByIdAndMapping($parent);

            if ($parentCategory) {
                $model->update(['parent_id' => $parentCategory->id]);
            }
        }

        $image = data_get($wooModel, 'image.src');

        if ($image) {
            $this->handleImageFromURL($model, $image);
        }
    }
}
