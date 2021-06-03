<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Graph extends Model
{
  use SoftDeletes;
  //
  protected $guarded = ['id'];
  protected $casts = [
    'rows' => 'array',
    'options' => 'array'
  ];

  public function scopeFilter($query, array $filters) 
  {
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('name', 'like', '%'.$search.'%');
    })->when($filters['trashed'] ?? null, function ($query, $trashed) {
        if ($trashed === 'with') {
            $query->withTrashed();
        } elseif ($trashed === 'only') {
            $query->onlyTrashed();
        }
    });
  }
}
