<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Mappers;


use Corals\Modules\Woo\Integration\WooCommerce\Models\Variation;
use Illuminate\Support\Arr;

class VariationMapper extends BaseMapper
{
    use ProductVariationTrait;

    protected $productObjectRef;
    protected $productModelId;
    protected $attributes;
    protected $attributeOptions;

    protected $options;

    /**
     * VariationMapper constructor.
     * @param null $productObjectRef
     * @param null $productModelId
     * @throws \Exception
     */
    public function __construct( $options = [],$productObjectRef = null, $productModelId = null)
    {
        $this->setProductIds($productObjectRef, $productModelId);

        parent::__construct($options);
    }

    public function init($options)
    {
        $this->options = $options;

        $modelClass = "\Corals\\Modules\\{$this->options["module"]}\\Models\\SKU";
        $this->setWooModelClass(Variation::class);
        $this->setModelClass(new $modelClass);
    }

    /**
     * @param $productObjectRef
     * @param null $productModelId
     * @throws \Exception
     */
    public function setProductIds($productObjectRef, $productModelId = null)
    {
        if (!$productObjectRef && !$productModelId) {
            return;
        }

        $this->productObjectRef = $productObjectRef;

        if (!$productModelId) {
            $module = "\Corals\Modules\\{$this->options['module']}\\Models\\Product";

            $product = $module::getByObjectReference(self::GATEWAY, $productObjectRef);

            if (!$product) {
                $productMapper = new ProductMapper($this->options);

                $productMapper->setMapperOption('loadDependencies', false);

                $product = $productMapper->fetchByIdAndMapping($productObjectRef);

                if (!$product) {
                    throw new \Exception('Load variation product first: ' . $productObjectRef);
                }
            }
            $productModelId = $product->id;
        }

        $this->productModelId = $productModelId;
    }

    /**
     * @param $nextPage
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function paginateResult($nextPage, $options = [])
    {
        if (!$this->productObjectRef) {
            throw new \Exception('Invalid variation product reference');
        }

        return $this->wooModelClass::paginate($this->productObjectRef, $this->getMapperOption('perPage', 30),
            $nextPage ?? 1);
    }

    public function getMappingArray(): array
    {
        return [
            'id' => 'object_reference',
            'sku' => 'code',
            'regular_price' => 'regular_price',
            'sale_price' => 'sale_price',
        ];
    }

    /**
     * @param $data
     * @param $wooModel
     */
    public function preStoreUpdateModel(&$data, $wooModel)
    {
        $data['status'] = data_get($wooModel, 'status') == 'publish' ? 'active' : 'inactive';

        $data['product_id'] = $this->productModelId;

        $this->setStockDetails($data, $wooModel);

        $this->setShippingDetails($data, $wooModel);
    }


    /**
     * @param $model
     * @param $data
     * @param $wooModel
     * @throws \Exception
     */
    public function postStoreUpdateModel($model, $data, $wooModel)
    {
        $this->handleImageFromURL($model, data_get($wooModel, 'image.src'));
        $this->handleSKUAttributes($model, $wooModel);
    }

    protected function getValidationRules($data, $model): array
    {
        $tableName = (new $this->modelClass)->getTableName();

        return [
            'code' => "required|unique:" . "$tableName,code" . ($model ? (',' . $model->id) : ''),
        ];
    }

    /**
     * @param $model
     * @param $wooModel
     * @throws \Exception
     */
    protected function handleSKUAttributes($model, $wooModel)
    {
        $attributes = data_get($wooModel, 'attributes');

        $model->options()->delete();

        $options = [];

        foreach ($attributes as $attribute) {
            $attribute = (array)$attribute;

            if (Arr::has($this->attributes, $attribute['id'])) {
                $attributeModel = data_get($this->attributes, $attribute['id']);
            } else {
                $module = "\Corals\Modules\\{$this->options['module']}\\Models\\Attribute";

                $attributeModel = $module::getByObjectReference(self::GATEWAY, $attribute['id']);
                data_set($this->attributes, $attribute['id'], $attributeModel);
            }

            if (!$attributeModel) {
                throw new \Exception('Attribute not found: ' . $attribute['id']);
            }

            if (Arr::has($this->attributeOptions, $attribute['option'])) {
                $optionModel = data_get($this->attributeOptions, $attribute['option']);
            } else {
                $optionModel = $attributeModel->options()->where('option_value', $attribute['option'])->first();
                data_set($this->attributeOptions, $attribute['option'], $optionModel);
            }

            if (!$optionModel) {
                throw new \Exception('Attribute option not found: ' . $attribute['option']);
            }

            $options[] = [
                'attribute_id' => $attributeModel->id,
                'value' => $optionModel->id
            ];
        }

        if ($options) {
            $model->options()->createMany($options);
        }
    }
}
