<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

use App\Page;
use App\Resource;
use Inertia\Inertia;

class ResourceController extends Controller
{
    //
  public function index() 
  {
    return Inertia::render('Admin/Resources/Index', [
      'filters' => Request::all('search'),
      'resources' => Resource::orderBy('created_at')
        ->filter(Request::only('search'))
        ->paginate()
    ]);
  }

  public function create() 
  {
    return Inertia::render('Admin/Resources/Create', [
      'pages' => Page::select('id', 'title', 'slug', 'description', 'hero')->where('publish', 1)->get()
    ]);
  }

  public function store()
  {
    $resource = Resource::create([
      'title' => Request::get('title'),
      'excerpt' => Request::get('excerpt'),
      'link' => Request::get('link'),
      'country' => Request::get('country'),
      'publish_date' => Request::get('publish_date'),
      'category' => Request::get('category'),
      'image' => Request::get('image'),
    ]);

    return Redirect::route('resources.edit', $resource)->with('success', 'Resource added.');
  }

  public function edit(Resource $resource) 
  {
    return Inertia::render('Admin/Resources/Edit', [
      'resource' => $resource,
    ]);
  }

  public function update(Resource $resource)
  {
    $resource->update(Request::all());

    return Redirect::back()->with('success', 'Resource updated');
  }

  public function show(Resource $resource)
  {
    return $resource;
  }

  public function destroy(Resource $resource)
  {
    $resource->delete();

    return Redirect::back()->with('success', 'Resource has been removed.');
  }
}
