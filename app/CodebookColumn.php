<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodebookColumn extends Model
{
    //
    protected $guarded = ['id'];
    protected $table = 'codebookcolumns';

    public function codebook()
    {
      return $this->belongsTo(Codebook::class);
    }
}
