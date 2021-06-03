<?php

namespace App\Http\Controllers;

use App\Organization;
use Inertia\Inertia;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\OrganizationRequest;

class OrganizationController extends Controller
{
  //
  public function index() 
  {
    return Inertia::render('Admin/Organizations/Index', [
      'filters' => Request::all('search'),
      'organizations' => Organization::orderBy('name')
        ->with(['datasets'])
        ->filter(Request::only('search'))
        ->paginate()
    ]);
  }

  public function store()
  {
    Request::validate([
      'name' => ['required', 'unique:organizations,name']
    ]);

    Organization::create([
      'name' => Request::get('name')
    ]);

    return Redirect::back()->with('success', 'Organization added.');
  }

  public function update(OrganizationRequest $request, Organization $organization)
  {
    $organization->update($request->validated());

    return Redirect::back()->with('success', 'Organization updated');
  }

  public function destroy(Organization $organization)
  {
    $organization->delete();

    return Redirect::back()->with('success', 'Organization has been removed.');
  }
}
