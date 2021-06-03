<?php

namespace App;

class Equation extends Model
{
  //
  protected $table = 'equations';

  public function meta()
  {
    return $this->hasMany(EquationMeta::class);
  }

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
