<?php

namespace App\Http\Controllers;

use App\Codebook;
use App\CodebookValue;
use App\CodebookColumn;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CodebookColumnRequest;

class CodebookColumnController extends Controller
{
//
  public function store(Codebook $codebook) 
  {

    Request::validate([
      'column_name' => ['required', 'unique:codebookcolumns,column_name'],
    ]);

    CodebookColumn::create([
      'codebook_id' => $codebook->id,
      'column_name' => Request::get('column_name'),
    ]);

    return Redirect::back()->with('success', 'Value added.');
  }

  public function update(CodebookColumnRequest $request, CodebookColumn $codebookcolumn)
  {

    $codebookcolumn->update($request->validated());

    return Redirect::back()->with('success', 'Value updated.');
  }

  public function destroy(CodebookColumn $codebookcolumn)
  {
    $codebookcolumn->delete();

    return Redirect::back()->with('success', 'Value removed.');
  }
}
