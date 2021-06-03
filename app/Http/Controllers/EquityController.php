<?php

namespace App\Http\Controllers;

use App\Equity;
use Inertia\Inertia;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\EquityRequest;

class EquityController extends Controller
{
  //
  public function index() 
  {
    return Inertia::render('Admin/Equity/Index', [
      'filters' => Request::all('search'),
      'equities' => Equity::orderBy('name')
        ->with(['datasets'])
        ->filter(Request::only('search'))
        ->paginate()
    ]);
  }

  public function store()
  {
    Request::validate([
      'name' => ['required', 'unique:equities,name']
    ]);

    Equity::create([
      'name' => Request::get('name')
    ]);

    return Redirect::back()->with('success', 'Equity added.');
  }

  public function update(EquityRequest $request, Equity $equity)
  {
    $equity->update($request->validated());

    return Redirect::back()->with('success', 'Equity updated');
  }

  public function destroy(Equity $equity)
  {
    $equity->delete();

    return Redirect::back()->with('success', 'Equity has been removed.');
  }
}
