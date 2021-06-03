<?php

namespace App;

class Region extends Model
{
    //

  public function datasets()
  {
    return $this->hasMany(Dataset::class);
  }

  public function getTotalDatasetsAttribute() 
  {
    return $this->hasMany(Dataset::class)->count();
  }

  public function scopeFilter($query, array $filters) 
  {
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('name', 'like', '%'.$search.'%');
    });
  }
}
