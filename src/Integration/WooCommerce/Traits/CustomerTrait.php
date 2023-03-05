<?php


namespace Corals\Modules\Woo\Integration\WooCommerce\Traits;


trait CustomerTrait
{
    protected function downloads($id)
    {
        $this->endpoint = "customers/{$id}/downloads";

        return self::all();
    }
}
