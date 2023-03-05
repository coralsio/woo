<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Mappers;

use Corals\Modules\Woo\Integration\WooCommerce\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductMapper extends BaseMapper
{
    use ProductVariationTrait;

    protected $categories = [];
    protected $attributes = [];
    protected $tags = [];

    protected $options;

    public function init($options)
    {
        $this->options = $options;
        $modelClass = "\Corals\\Modules\\{$options["module"]}\\Models\\Product";

        $this->setWooModelClass(Product::class);
        $this->setModelClass(new $modelClass);

    }

    public function getMappingArray(): array
    {
        $result = [
            'id' => 'object_reference',
            'name' => 'name',
            'description' => 'description',
            'short_description' => 'caption',
            'sku' => 'product_code',
            'featured' => 'is_featured',
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

    /**
     * @param $data
     * @param $wooModel
     */
    public function preStoreUpdateModel(&$data, $wooModel)
    {
        $data['status'] = data_get($wooModel, 'status') == 'publish' ? 'active' : 'inactive';

        $data['type'] = data_get($wooModel, 'type') == 'variable' ? 'variable' : 'simple';

        $data['slug'] = ($slug = data_get($wooModel, 'slug')) ? $slug : (Str::slug(data_get($data,
                'name') . ' ' . data_get($wooModel, 'id')));

        $data['caption'] = ($caption = data_get($data, 'caption')) ? $caption : Str::limit(strip_tags(data_get($data,
            'description')));

        $this->setShippingDetails($data, $wooModel);

        if (data_get($data, 'type') === 'simple') {
            unset($data['product_code']);
        }
    }

    /**
     * @param $model
     * @param $data
     * @param $wooModel
     * @throws \Exception
     */
    public function postStoreUpdateModel($model, $data, $wooModel)
    {
        $this->handleAttributes($model, $wooModel);

        if (!$this->getMapperOption('loadDependencies')) {
            return;
        }

        $this->handleCategories($model, $wooModel);
        $this->handleTags($model, $wooModel);

        $this->handleProductImages($model, $wooModel);

        if ($model->type === 'simple') {
            $this->handleSimpleProduct($model, $wooModel);
        } else {
            $this->handleVariableProduct($model, $wooModel);
        }
    }

    protected function handleCategories($model, $wooModel)
    {
        $categories = data_get($wooModel, 'categories', []);

        $categoryMapper = new CategoryMapper($this->options);

        $productCategories = [];

        foreach ($categories ?? [] as $category) {
            $category = (array)$category;

            if (Arr::has($this->categories, $category['id'])) {
                $productCategories[] = data_get($this->categories, $category['id']);
            }

            $categoryModel = $categoryMapper->getModelIfExists($category['id']);

            if (!$categoryModel) {
                $categoryModel = $categoryMapper->fetchByIdAndMapping($category['id']);
            }

            if ($categoryModel) {
                data_set($this->categories, $category['id'], $categoryModel->id);
                $productCategories[] = $categoryModel->id;
            }
        }

        if ($productCategories) {
            $model->categories()->sync($productCategories);
        }
    }

    protected function handleTags($model, $wooModel)
    {
        $tags = data_get($wooModel, 'tags', []);

        $productTags = [];

        if ($tags) {
            $tagMapper = new TagMapper($this->options);

            foreach ($tags as $tag) {
                $tag = (array)$tag;

                if (Arr::has($this->tags, $tag['id'])) {
                    $productTags[] = data_get($this->tags, $tag['id']);
                }

                $tagModel = $tagMapper->getModelIfExists($tag['id']);

                if (!$tagModel) {
                    $tagModel = $tagMapper->fetchByIdAndMapping($tag['id']);
                }

                if ($tagModel) {
                    data_set($this->tags, $tag['id'], $tagModel->id);
                    $productTags[] = $tagModel->id;
                }
            }
        }

        if ($productTags) {
            $model->tags()->sync($productTags);
        }
    }

    protected function handleAttributes($model, $wooModel)
    {
        $attributes = data_get($wooModel, 'attributes');

        if ($attributes) {
            $attributeMapper = new AttributeMapper($this->options);

            $productAttributes = [];

            foreach ($attributes as $attribute) {
                $attribute = (array)$attribute;
                $attributeMapper->setAttributeOptions($attribute['options'] ?? []);

                if (Arr::has($this->attributes, $attribute['id'])) {
                    $attributeModel = data_get($this->attributes, $attribute['id']);
                    $attributeMapper->updateOrCreateOptions($attributeModel);
                } else {
                    $attributeModel = $attributeMapper->fetchByIdAndMapping($attribute['id']);
                }

                if ($attributeModel) {
                    data_set($this->attributes, $attribute['id'], $attributeModel);
                    $productAttributes[$attributeModel->id] = [
                        'sku_level' => true,
                    ];
                }
            }

            if ($productAttributes) {
                $model->attributes()->sync($productAttributes);
            }
        }
    }


    protected function handleSimpleProduct($model, $wooModel)
    {
        $skuData = [
            'regular_price' => data_get($wooModel, 'regular_price'),
            'sale_price' => data_get($wooModel, 'sale_price'),
            'code' => data_get($wooModel, 'sku'),
            'allowed_quantity' => 0
        ];

        $this->setStockDetails($skuData, $wooModel);

        if ($model->sku->first()) {
            $model->sku->first()->update($skuData);
        } else {
            $model->sku()->create($skuData);
        }
    }

    protected function getValidationRules($data, $model): array
    {
        $tableName = (new $this->modelClass)->getTableName();

        return [
            'name' => 'required|max:191',
            'caption' => 'required',
            'status' => 'required',
            'type' => 'required|in:simple,variable',
            'product_code' => "required_if:type,variable|unique:" . "$tableName,product_code" . ($model ? (',' . $model->id) : ''),
        ];
    }

    /**
     * @param $model
     * @param $wooModel
     * @throws \Exception
     */
    protected function handleVariableProduct($model, $wooModel)
    {
        $variationMapper = new VariationMapper($this->options, data_get($wooModel, 'id'), $model->id);

        $variationMapper->doMapping();
    }

    protected function handleProductImages($model, $wooModel)
    {
        foreach (data_get($wooModel, 'images', []) ?? [] as $i => $image) {
            if ($i === 0) {
                $model->clearMediaCollection($model->galleryMediaCollection);
            }

            $image = (array)$image;

            $src = data_get($image, 'src');

            $this->handleImageFromURL($model, $src, $i === 0 ? ['featured' => true] : [], null,
                $model->galleryMediaCollection);
        }
    }
}
