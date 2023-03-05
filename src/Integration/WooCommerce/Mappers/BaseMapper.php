<?php


namespace Corals\Modules\Woo\Integration\WooCommerce\Mappers;


use Carbon\Carbon;
use Corals\Modules\Woo\Models\FetchRequest;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Class BaseMapper
 * @package Corals\Modules\Woo\Integration\WooCommerce\Mappers
 *
 * @method preStoreUpdateModel(&$data, $wooModel)
 * @method postStoreUpdateModel($model, $data, $wooModel)
 * @method xColumnGetValue(&$data, $wooModel, $mappedTo)
 */
abstract class BaseMapper
{
    protected $modelClass;
    protected $wooModelClass;

    protected $mapperOptions = [
        'loadDependencies' => true,
        'fetchTranslation' => false,
        'doUpdateIfExists' => true,
        'perPage' => 30,
    ];

    //const
    const GATEWAY_STATUS = 'NA';
    const GATEWAY = 'woocommerce';
    const OBJECT_REFERENCE_KEY = 'object_reference';

    public function __construct($options = [])
    {
        $this->setMapperOptions($options);
        $this->init($options);
    }

    abstract public function init($options);

    /**
     * @return array
     */
    public function getMapperOptions(): array
    {
        return $this->mapperOptions;
    }

    /**
     * @param $options
     */
    public function setMapperOptions($options)
    {
        foreach ($options as $key => $value) {
            $this->mapperOptions[$key] = $value;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function setMapperOption($key, $value)
    {
        data_set($this->mapperOptions, $key, $value);
    }

    /**
     * @param $key
     * @param null $default
     * @return array|mixed
     */
    public function getMapperOption($key, $default = null)
    {
        return data_get($this->mapperOptions, $key, $default);
    }

    /**
     * @param $message
     * @param array $data
     */
    public function logError($message, array $data = [])
    {
        $this->logProcess($message, $data, 'error');
    }

    /**
     * @param $message
     * @param array $data
     * @param string $type
     */
    public function logProcess($message, array $data = [], $type = 'info')
    {
        if (!config('wooconfig.process_log_enabled') && $type !== 'error') {
            return;
        }

        $class = class_basename($this);

        if (is_array($message)) {
            $message = json_encode($message);
        }
        if ($data) {
            $message .= ' | data: ' . json_encode($data);
        }

        logger()->{$type}("$class: $message");
    }

    /**
     * @param $model
     * @param $imageURL
     * @param array $customProperties
     * @param null $root
     * @param null $collection
     */
    protected function handleImageFromURL($model, $imageURL, $customProperties = [], $root = null, $collection = null)
    {
        try {
            if (!$imageURL) {
                return;
            }
            if (!$collection) {
                $collection = $model->mediaCollectionName;
            }

            if (!$root) {
                $class = strtolower(class_basename($this));
                $root = $class . '_media';
            }

            $model->addMediaFromUrl($imageURL)
                ->withCustomProperties(array_merge([
                    'root' => $root
                ], $customProperties))->toMediaCollection($collection);
        } catch (\Exception $exception) {
            $this->logError($exception->getMessage(), ['method' => 'handleImageFromURL']);
        }
    }

    /**
     * @param $wooModelClass
     */
    public function setWooModelClass($wooModelClass)
    {
        $this->wooModelClass = $wooModelClass;
    }

    /**
     * @param $modelClass
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return array
     */
    abstract public function getMappingArray(): array;

    /**
     * @param $integrationId
     * @return mixed
     */
    protected function getModelIfExists($integrationId)
    {
        return $this->modelClass::getByObjectReference(self::GATEWAY, $integrationId);
    }

    /**
     * @param mixed ...$args
     * @return mixed|null
     */
    public function fetchByIdAndMapping(...$args)
    {
        try {
            $this->logProcess('fetchByIdAndMapping: ' . json_encode($args));

            $wooModel = $this->wooModelClass::find(...$args);

            $model = $this->storeUpdateModel($wooModel);

            $this->logProcess('fetchByIdAndMapping completed');

            return $model;
        } catch (Exception $exception) {
            $this->logError($exception->getMessage(), ['method' => 'fetchByIdAndMapping']);
        }
    }

    /**
     * @param $castTo
     * @param $value
     * @return mixed
     */
    protected function castTo($castTo, $value)
    {
        switch ($castTo) {
            case 'carbon':
                $value = Carbon::parse($value);
                break;
        }

        return $value;
    }

    /**
     * @param null $result
     * @param array $options
     * @throws Exception
     */
    public function doMapping($result = null, $options = [])
    {
        $this->logProcess('doMapping started');

        if (is_null($result)) {
            $this->fetchAll();
        }

        if (is_array($result)) {
            if (!empty($result['pagination'])) {
                $this->handlePaginatedResult($result, $options);
            } else {
                $this->handleResult($result);
            }
        }

        if ($result && $result instanceof \stdClass) {
            $this->logProcess('storeUpdateModel', (array)$result);
            $this->storeUpdateModel($result);
        }

        $this->logProcess('doMapping completed');
    }

    /**
     * @param array $options
     * @throws Exception
     */
    public function fetchAll($options = [])
    {
        $this->logProcess('fetchAll started');

        $this->handlePaginatedResult(null, $options);

        $this->logProcess('fetchAll ended');
    }

    /**
     * @param $nextPage
     * @param array $options
     * @return mixed
     */
    public function paginateResult($nextPage, $options = [])
    {
        return $this->wooModelClass::paginate($this->getMapperOption('perPage', 30), $nextPage ?? 1, $options);
    }

    /**
     * @param array $result
     * @throws Exception
     */
    public function handleResult(array $result)
    {
        $this->logProcess('handleResult started');
        unset($result['pagination']);

        foreach ($result as $wooModel) {
            $this->storeUpdateModel($wooModel);
        }
        $this->logProcess('handleResult completed');
    }

    /**
     * @param $wooModel
     * @param null $model
     * @return mixed
     * @throws Exception
     */
    public function storeUpdateModel($wooModel)
    {
        try {
            $this->logProcess('------storeUpdateModel START------');

            $data = [];

            $wooModel = (array)$wooModel;

            foreach ($this->getMappingArray() as $key => $mappedTo) {
                if (method_exists($this, $key . 'GetValue')) {
                    $this->{$key . 'GetValue'}($data, $wooModel, $mappedTo);
                    continue;
                } elseif (is_array($mappedTo)) {
                    $value = $this->castTo($mappedTo['cast_to'] ?? null, data_get($wooModel, $key));

                    $mappedTo = $mappedTo['column'];
                } elseif (Str::contains($key, '::')) {
                    $value = Str::after($key, '::');
                } else {
                    $value = data_get($wooModel, $key);
                }

                data_set($data, $mappedTo, $value);
            }

            $data = array_filter($data);

            $objectReference = Arr::pull($data, self::OBJECT_REFERENCE_KEY);

            if (!$objectReference) {
                throw new Exception('Invalid Object Reference.');
            }

            $this->logProcess('Integration Id: ' . $objectReference);


            $model = $this->getModelIfExists($objectReference);

            if ($model && !$this->getMapperOption('doUpdateIfExists')) {
                $this->handleWhenModelExistUpdateUnneeded($model, $wooModel);
                $this->logProcess('fetchByIdAndMapping: Model exists and NO update required');
                return $model;
            }

            if (method_exists($this, 'preStoreUpdateModel')) {
                $this->preStoreUpdateModel($data, $wooModel);
            }

            $wooModelKeys = array_keys($wooModel);

            $dataKeys = array_keys($data);

            foreach (array_diff($wooModelKeys, $dataKeys) as $key) {
                Arr::set($data, "properties.$key", data_get($wooModel, $key));
            }

            $this->validateModel($data, $model);

            if ($model) {
                $model->update($data);
            } else {
                $model = $this->modelClass::query()->create($data);
            }

            $model->setGatewayStatus(self::GATEWAY, self::GATEWAY_STATUS, null, $objectReference);

            if (method_exists($this, 'postStoreUpdateModel')) {
                $this->postStoreUpdateModel($model, $data, $wooModel);
            }

            if ($this->getMapperOption('fetchTranslation')) {
                $this->addTranslationToFetchRequest($model, $objectReference);
            }

            $this->logProcess('Loaded model id: ' . $model->id);

            return $model;
        } catch (ValidationException $exception) {
            $this->logError(json_encode($exception->errors()), ['method' => 'storeUpdateModel']);
        } catch (Exception $exception) {
            $this->logError($exception->getMessage(), ['method' => 'storeUpdateModel']);
        } finally {
            $this->logProcess('------storeUpdateModel END------');
        }

        return null;
    }

    protected function addTranslationToFetchRequest($model, $objectReference)
    {
        //fixme:: translation not implemented for now
        return;
        if (!FetchRequest::query()->where([
            'integration_id' => $objectReference,
            'mapper' => get_class($this)
        ])->exists()) {
            FetchRequest::query()->create([
                'integration_id' => $objectReference,
                'mapper' => get_class($this),
                'status' => 'fetched',
            ]);
        }

        foreach ($model->getProperty('translations', []) as $lang => $id) {
            if ($id == $objectReference || FetchRequest::query()->where([
                    'integration_id' => $id,
                    'mapper' => get_class($this)
                ])->exists()) {
                continue;
            }

            FetchRequest::query()->create([
                'integration_id' => $id,
                'mapper' => get_class($this),
                'status' => 'pending',
                'properties' => [
                    'model_id' => $model->id,
                    'integration_id' => $objectReference,
                    'lang' => $model->lang,
                    'to_be_fetched_lang' => $lang,
                    'product_id' => $model->product_id,
                ]
            ]);
        }
    }

    /**
     * @param $result
     * @param array $options
     * @throws Exception
     */
    protected function handlePaginatedResult($result, $options = [])
    {
        do {
            if (is_null($result)) {
                $result = $this->paginateResult($nextPage ?? 1, $options);
            }

            $pagination = Arr::pull($result, 'pagination') ?? [];

            $this->logProcess('pagination', $pagination);

            $nextPage = $pagination['next_page'] ?? null;

            $this->handleResult($result);

            $result = null;
        } while ($nextPage);
    }

    /**
     * @param $data
     * @param $model
     * @return array
     */
    protected function getValidationRules($data, $model): array
    {
        return [];
    }

    /**
     * @param array $data
     * @param null $model
     * @throws ValidationException
     */
    protected function validateModel(array $data, $model = null)
    {
        $rules = $this->getValidationRules($data, $model);

        if (empty($rules)) {
            return;
        }

        $validator = Validator::make($data, $rules);

        $validator->validate();
    }

    /**
     * @param $model
     * @param array $wooModel
     */
    protected function handleWhenModelExistUpdateUnneeded($model, array $wooModel)
    {
    }
}
