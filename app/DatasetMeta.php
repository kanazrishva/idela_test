<?php

namespace App;

use Rennokki\QueryCache\Traits\QueryCacheable;

class DatasetMeta extends Model
{

  use QueryCacheable;
  //
  protected $guarded = ['id'];
  protected $table = 'datasets_meta';

  public $cacheFor = 31536000; // One Year

  public function codebook() 
  {
    return $this->belongsTo(Codebook::class);
  }

    public function scopeFilter($query, array $filters) 
  {
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('column_name', 'like', '%'.$search.'%');
    });
  }
}
