<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;


class Codebook extends Model
{
    //
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'codebook';
    //protected $appends = ['total_dataset_meta'];

    public function codebookValues()
    {
      return $this->hasMany(CodebookValue::class);
    }

    public function codebookColumns()
    {
      return $this->hasMany(CodebookColumn::class);
    }


    public function getTotalDatasetMetaAttribute()
    {
      return $this->hasMany(DatasetMeta::class)->count();
    }

    public function scopeFilter($query, array $filters)
    {
      $query->when($filters['search'] ?? null, function ($query, $search) {
          $query->where('matching_name', 'like', '%'.$search.'%');
      })->when($filters['trashed'] ?? null, function ($query, $trashed) {
          if ($trashed === 'with') {
              $query->withTrashed();
          } elseif ($trashed === 'only') {
              $query->onlyTrashed();
          }
      });
    }
}
