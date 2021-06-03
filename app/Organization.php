<?php

namespace App;

class Organization extends Model
{
    //
  protected $guarded = ['id'];
  protected $table = 'organizations';

  public function datasets()
  {
    return $this->belongsToMany(Dataset::class, 'datasets_organizations');
  }

  public function getTotalDatasetsAttribute() 
  {
    return $this->belongsToMany(Dataset::class, 'datasets_organizations')->count();
  }

  public function scopeFilter($query, array $filters) 
  {
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('name', 'like', '%'.$search.'%');
    });
  }
}
