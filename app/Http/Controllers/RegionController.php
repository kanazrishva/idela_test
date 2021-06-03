<?php

namespace App\Http\Controllers;

use App\Region;
use Inertia\Inertia;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\RegionRequest;

class RegionController extends Controller
{
  //
  public function index() 
  {
    return Inertia::render('Admin/Regions/Index', [
      'filters' => Request::all('search'),
      'regions' => Region::orderBy('name')
        ->with(['datasets'])
        ->filter(Request::only('search'))
        ->paginate()
    ]);
  }

  public function store()
  {
    Request::validate([
      'name' => ['required', 'unique:regions,name']
    ]);

    Region::create([
      'name' => Request::get('name')
    ]);

    return Redirect::back()->with('success', 'Region added.');
  }

  public function update(RegionRequest $request, Region $region)
  {
    $region->update($request->validated());

    return Redirect::back()->with('success', 'Region updated');
  }

  public function destroy(Region $region)
  {
    $region->delete();

    return Redirect::back()->with('success', 'Region has been removed.');
  }
}
