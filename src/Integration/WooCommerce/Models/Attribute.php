<?php

namespace Corals\Modules\Woo\Integration\WooCommerce\Models;

use Corals\Modules\Woo\Integration\WooCommerce\Traits\QueryBuilderTrait;

/**
 * Class Attribute
 * @package Corals\Modules\Woo\Integration\WooCommerce\Models
 * @method static all($options = [])
 * @method static find($id, $options = [])
 * @method static create($data)
 * @method static update($id, $data)
 * @method static delete($id, $options = [])
 * @method static batch($data)
 * @method static get()
 * @method static first()
 * @method static options($parameters)
 * @method static where(...$parameters)
 * @method static orderBy($name, $direction = 'desc')
 * @method static paginate($per_page, $current_page = 1, $parameters = [])
 * @method static count()
 *
 * Attribute methods
 * @method static getTerms($attribute_id, $options = [])
 * @method static getTerm($attribute_id, $term_id, $options = [])
 * @method static addTerm($attribute_id, $data)
 * @method static updateTerm($attribute_id, $term_id, $data)
 * @method static deleteTerm($attribute_id, $term_id, $options = [])
 * @method static batchTerm($attribute_id, $data)
 *
 */
class Attribute extends BaseModel
{
    use QueryBuilderTrait;

    protected $endpoint = 'products/attributes';
}
