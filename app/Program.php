<?php

namespace App;


class Program extends Model
{
  //
  protected $guarded = ['id'];
  protected $table = 'programs';

  public function datasets()
  {
    return $this->belongsToMany(Dataset::class, 'datasets_programs');
  }

  public function getTotalDatasetsAttribute() 
  {
    return $this->belongsToMany(Dataset::class, 'datasets_programs')->count();
  }

  public function scopeFilter($query, array $filters) 
  {
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('name', 'like', '%'.$search.'%');
    });
  }
}
