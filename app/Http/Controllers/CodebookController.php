<?php

namespace App\Http\Controllers;

use App\Codebook;
use App\DatasetMeta;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CodebookController extends Controller
{
  //
  public function index()
  {
    return Inertia::render('Admin/Codebook/Index', [
      'filters' => Request::all('search', 'trashed'),
      'codebook' => Codebook::orderBy('matching_name')
        ->with(['codebookColumns'])
        ->filter(Request::only('search', 'trashed'))
        ->paginate()
    ]);
  }

  public function create() 
  {
    return Inertia::render('Admin/Codebook/Create');
  }

  public function store() 
  {
    $codebook = Codebook::create(
      Request::validate([
        'type' => ['required'],
        'matching_name' => ['required'],
        'matching_description' => ['nullable', 'max:150']
      ])
    );

    return Redirect::route('codebook.edit', $codebook)->with('success', 'Codebook Item Added');
  }

  public function edit(Codebook $codebook) 
  {
    return Inertia::render('Admin/Codebook/Edit', [
      'codebook' => [
        'id' => $codebook->id,
        'type' => $codebook->type,
        'matching_name' => $codebook->matching_name,
        'matching_description' => $codebook->matching_description,
        'deleted_at' => $codebook->deleted_at,
        'codebookvalues' => $codebook->codebookValues()->get()->map->only('id', 'label', 'value'),
        //'codebookcolumns' => $codebook->codebookColumns()->get()->map->only('id', 'column_name')
      ],
      'codebookcolumns' => $codebook->codebookColumns()->get()->map->only('id', 'column_name')
    ]);
  }

  public function update(Codebook $codebook) 
  {
    $codebook->update(
      Request::validate([
        'type' => ['required'],
        'matching_name' => ['required'],
        'matching_description' => ['nullable', 'max:150']
      ])
    );

    return Redirect::back()->with('success', 'Codebook updated.');
  }

  public function remove(Codebook $codebook) 
  {
    //DatasetMeta::where('codebook_id', $codebook->id)->update(['codebook_id' => null]);
    $codebook->codebookValues()->delete();
    $codebook->codebookColumns()->delete();
    
    $codebook->delete();

    return Redirect::back()->with('success', 'Codebook deleted.');
  }

  public function destroy(Codebook $codebook) 
  {
    $codebook->forceDelete();

    return Redirect::back()->with('success', 'Codebook permanently deleted.');
  }

  public function restore(Codebook $codebook) 
  {
    $codebook->restore();

    return Redirect::back()->with('success', 'Codebook  restored.');
  }
}
