<?php

namespace App\Http\Controllers;

use App\Country;
use Inertia\Inertia;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\CountryRequest;

class CountryController extends Controller
{
  //

  public function index() 
  {
    return Inertia::render('Admin/Countries/Index', [
      'filters' => Request::all('search'),
      'countries' => Country::orderBy('country')
        ->with(['datasets'])
        ->filter(Request::only('search'))
        ->paginate()
    ]);
  }

  public function store()
  {
    Request::validate([
      'country' => ['required', 'unique:countries,country']
    ]);

    Country::create([
      'country' => Request::get('country')
    ]);

    return Redirect::back()->with('success', 'Country added.');
  }

  public function update(CountryRequest $request, Country $country)
  {
    $country->update($request->validated());

    return Redirect::back()->with('success', 'Country Updated');
  }

  public function destroy(Country $country)
  {
    $country->delete();

    return Redirect::back()->with('success', 'Country has been removed.');
  }
}
