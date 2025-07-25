<?php namespace VaahCms\Modules\BlogSystem\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use WebReinvent\VaahCms\Models\VaahModel;
use WebReinvent\VaahCms\Models\User;
use WebReinvent\VaahCms\Traits\CrudWithUuidObservantTrait;

class Seo extends VaahModel {

	  use SoftDeletes;
    use CrudWithUuidObservantTrait;

    //-------------------------------------------------
    protected $table = 'bs_seos';
    //-------------------------------------------------
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    //-------------------------------------------------
    protected $fillable = [
        'uuid',
        'seo_title',
        'seo_description',
        'seo_metatag',
        'seoable_id',
        'seoable_type',
        'slug',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    //-------------------------------------------------
    protected $casts = [
        'seo_metatag' => 'array', // Automatically cast as array
    ];

    //-------------------------------------------------
    protected $appends  = [
    ];
    //-------------------------------------------------
   protected function serializeDate(DateTimeInterface $date)
    {
        $date_time_format = config('settings.global.datetime_format');
        return $date->format($date_time_format);
    }
    //-------------------------------------------------
    //seo
    public function seoable()
    {
        return $this->morphTo();
    }

    //-------------------------------------------------

    public function createdByUser()
    {
        return $this->belongsTo(User::class,
            'created_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function updatedByUser()
    {
        return $this->belongsTo(User::class,
            'updated_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    //-------------------------------------------------
    public function deletedByUser()
    {
        return $this->belongsTo(User::class,
            'deleted_by', 'id'
        )->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }
    //-------------------------------------------------
    public function getTableColumns()
        {
            return $this->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($this->getTable());
        }

        //-------------------------------------------------
        public function scopeExclude($query, $columns)
        {
            return $query->select(array_diff($this->getTableColumns(), $columns));
        }

        //-------------------------------------------------
        public function scopeBetweenDates($query, $from, $to)
        {

            if ($from) {
                $from = \Carbon::parse($from)
                    ->startOfDay()
                    ->toDateTimeString();
            }

            if ($to) {
                $to = \Carbon::parse($to)
                    ->endOfDay()
                    ->toDateTimeString();
            }

            $query->whereBetween('updated_at', [$from, $to]);
        }

    //-------------------------------------------------

}
