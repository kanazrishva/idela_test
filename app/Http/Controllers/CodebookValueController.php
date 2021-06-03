<?php

namespace App\Http\Controllers;

use App\Codebook;
use App\CodebookValue;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CodebookValueRequest;

class CodebookValueController extends Controller
{
  //
  public function store(Codebook $codebook) 
  {

    Request::validate([
      'label' => ['required'],
      'value' => ['required']
    ]);

    CodebookValue::create([
      'codebook_id' => $codebook->id,
      'label' => Request::get('label'),
      'value' => Request::get('value')
    ]);

    return Redirect::back()->with('success', 'Value added.');
  }

  public function update(CodebookValueRequest $request, CodebookValue $codebookvalue)
  {

    $codebookvalue->update($request->validated());

    return Redirect::back()->with('success', 'Value updated.');
  }

  public function destroy(CodebookValue $codebookvalue)
  {
    $codebookvalue->delete();

    return Redirect::back()->with('success', 'Value removed.');
  }
}
