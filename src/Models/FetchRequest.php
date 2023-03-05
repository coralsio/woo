<?php


namespace Corals\Modules\Woo\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class FetchRequest extends BaseModel
{
    protected $table = 'wc_fetch_requests';

    use PresentableTrait, LogsActivity;

    protected $guarded = ['id'];
    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'woo.models.fetch_request';

    protected $casts = [
        'properties' => 'json',
    ];

    public function scopePending($query)
    {
        $query->where('status', 'pending');
    }

    public function markAsFetched()
    {
        $this->fill([
            'status' => 'fetched',
        ])->save();
    }
}
