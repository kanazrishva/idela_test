<?php

namespace App\Http\Controllers;

use App\Program;
use Inertia\Inertia;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProgramRequest;

class ProgramsController extends Controller
{
  //
  public function index() 
  {
    return Inertia::render('Admin/Programs/Index', [
      'filters' => Request::all('search'),
      'programs' => Program::orderBy('name')
        ->with(['datasets'])
        ->filter(Request::only('search'))
        ->paginate()
    ]);
  }

  public function store()
  {
    Request::validate([
      'name' => ['required', 'unique:programs,name']
    ]);

    Program::create([
      'name' => Request::get('name')
    ]);

    return Redirect::back()->with('success', 'Program added.');
  }

  public function update(ProgramRequest $request, Program $program)
  {
    $program->update($request->validated());

    return Redirect::back()->with('success', 'Program updated');
  }

  public function destroy(Program $program)
  {
    $program->delete();

    return Redirect::back()->with('success', 'Program has been removed.');
  }
}
