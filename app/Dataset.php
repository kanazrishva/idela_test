<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Dataset extends Model
{
  use SoftDeletes;

  protected $guarded = ['id'];
  protected $table = 'datasets';

  public function country() {
    return $this->belongsTo(Country::class, 'country_id');
  }

  public function meta() {
    return $this->hasMany(DatasetMeta::class, 'datasets_id');
  }

  public function getMetaRowsMaxAttribute() 
  {
    return $this->hasMany(DatasetMeta::class, 'datasets_id')->max('row');
  }

  public function organizations() 
  {
    return $this->belongsToMany(Organization::class, 'datasets_organizations');
  }

  public function equities() 
  {
    return $this->belongsToMany(Equity::class, 'datasets_equities');
  }

  public function programs() 
  {
    return $this->belongsToMany(Program::class, 'datasets_programs');
  }

  // Get rows for in with each dataset returned
  public function getMetaRowsAttribute()
  {
    return $this->hasMany(DatasetMeta::class, 'datasets_id')
      ->where('row', '!=', 'header')
      ->groupBy('row')
      ->pluck('row')
      ->implode(',');
  }


  public function scopeFilter($query, array $filters) 
  {
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('dataset_name', 'like', '%'.$search.'%');
    })->when($filters['trashed'] ?? null, function ($query, $trashed) {
        if ($trashed === 'with') {
            $query->withTrashed();
        } elseif ($trashed === 'only') {
            $query->onlyTrashed();
        }
    });
  }
}
