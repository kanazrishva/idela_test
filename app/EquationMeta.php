<?php

namespace App;

class EquationMeta extends Model
{
    //
    protected $table = 'equations_meta';

    public function codebook() {
      return $this->belongsTo(Codebook::class, 'value');
    }
}
