<?php

namespace App;

class Page extends Model
{
  //
  protected $casts = [
    'blocks' => 'array',
    'hero' => 'array',
    'caregiver' => 'array',
    'footer' => 'array',
    'meta' => 'array'
  ];

  public function datasets() 
  {
    return $this->belongsToMany(Dataset::class);
  }

  public function graphs() 
  {
    return $this->hasMany(Graph::class);
  }

  public function scopeSearch($query, $search) 
  {
    $query->when($search ?? null, function ($query, $search) {
        $query->where('title', 'like', '%'.$search.'%');
        // $query->where('description', 'like', '%'.$search.'%');
        // $query->where('blocks', 'like', '%'.$search.'%');
        // $query->where('caregiver', 'like', '%'.$search.'%');
    });
  }

  public function scopeFilter($query, array $filters) 
  {
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('title', 'like', '%'.$search.'%');
    })->when($filters['trashed'] ?? null, function ($query, $trashed) {
        if ($trashed === 'with') {
            $query->withTrashed();
        } elseif ($trashed === 'only') {
            $query->onlyTrashed();
        }
    });
  }
}
