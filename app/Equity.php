<?php

namespace App;

class Equity extends Model
{
  //
  protected $guarded = ['id'];
  protected $table = 'equities';

  public function datasets()
  {
    return $this->belongsToMany(Dataset::class, 'datasets_equities');
  }

  public function getTotalDatasetsAttribute() 
  {
    return $this->belongsToMany(Dataset::class, 'datasets_equities')->count();
  }

  public function scopeFilter($query, array $filters) 
  {
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('name', 'like', '%'.$search.'%');
    });
  }
}
