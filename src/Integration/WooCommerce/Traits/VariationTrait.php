<?php


namespace Corals\Modules\Woo\Integration\WooCommerce\Traits;


use Corals\Modules\Woo\Integration\WooCommerce\Facades\WooCommerce;

trait VariationTrait
{
    /**
     * @var
     */
    protected $options = [];

    /**
     * Retrieve all Items.
     *
     * @param int $product_id
     * @param array $options
     *
     * @return array
     */
    protected function all($product_id, $options = [])
    {
        return WooCommerce::all("products/{$product_id}/variations", $options);
    }

    /**
     * Retrieve single Item.
     *
     * @param int $product_id
     * @param int $id
     * @param array $options
     *
     * @return object
     */
    protected function find($product_id, $id, $options = [])
    {
        return WooCommerce::find("products/{$product_id}/variations/{$id}", $options);
    }

    /**
     * Create new Item.
     *
     * @param int $product_id
     * @param array $data
     *
     * @return object
     */
    protected function create($product_id, $data)
    {
        return WooCommerce::create("products/{$product_id}/variations", $data);
    }

    /**
     * Update Existing Item.
     *
     * @param int $product_id
     * @param int $id
     * @param array $data
     *
     * @return object
     */
    protected function update($product_id, $id, $data)
    {
        return WooCommerce::update("products/{$product_id}/variations/{$id}", $data);
    }

    /**
     * Destroy Item.
     *
     * @param int $product_id
     * @param int $id
     * @param array $options
     *
     * @return object
     */
    protected function delete($product_id, $id, $options = [])
    {
        return WooCommerce::delete("products/{$product_id}/variations/{$id}", $options);
    }

    /**
     * Batch Update.
     *
     * @param int $product_id
     * @param array $data
     *
     * @return object
     */
    protected function batch($product_id, $data)
    {
        return WooCommerce::create("products/{$product_id}/variations/batch", $data);
    }


    /**
     * Paginate results.
     * @param $product_id
     * @param $per_page
     * @param int $current_page
     * @return array
     * @throws \Exception
     */
    protected function paginate($product_id, $per_page, $current_page = 1)
    {
        try {
            $this->options['per_page'] = (int)$per_page;

            if ($current_page > 0) {
                $this->options['page'] = (int)$current_page;
            }

            $results = $this->all($product_id, $this->options);

            $totalResults = WooCommerce::countResults();
            $totalPages = WooCommerce::countPages();
            $currentPage = WooCommerce::current();
            $previousPage = WooCommerce::previous();
            $nextPage = WooCommerce::next();

            $pagination = [
                'total_results' => $totalResults,
                'total_pages' => $totalPages,
                'current_page' => $currentPage,
                'previous_page' => $previousPage,
                'next_page' => $nextPage,
                'first_page' => 1,
                'last_page' => $totalResults,
            ];

            $results['pagination'] = $pagination;

            return $results;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), 1);
        }
    }
}
