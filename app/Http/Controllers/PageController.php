<?php

namespace App\Http\Controllers;

use App\Page;
use App\Graph;
use App\Country;
use App\Resource;
use App\Dataset;
use App\Codebook;
use App\Equation;
use Inertia\Inertia;

use Illuminate\Support\Facades\Request;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class PageController extends Controller
{
    //

    public function index() 
    {
      return Inertia::render('Admin/Pages/Index', [
        'filters' => Request::all('search'),
        'pages' => Page::orderBy('title')
          ->filter(Request::only('search'))
          ->paginate()
      ]);
    }

    public function create()
    { 
      return Inertia::render('Admin/Pages/Create');
    }

    public function show(Page $page) 
    {
      $slug = '';

      if ($page->slug != '') {
        $slug = '/data/'.$page->slug;
      }

      return Inertia::render('Page', [
        'page' => $page
      ])->withViewData(['meta' => $page->meta, 'slug' => $slug]);;
    }

    public function store()
    {

      $page = Page::create([
        'title' => Request::get('title'),
        'slug' => Str::slug(Request::get('title'), '-'),
        'description' => Request::get('description'),
        'publish' => false,
        'meta' => Request::get('meta'),
        'hero' => Request::get('hero'),

      ]);

      return Redirect::route('pages.edit', $page)->with('success', 'Page has been created.');
    }

    public function edit(Page $page)
    {
      $selected_datasets = collect($page->datasets->toArray());

      // Returns back and organized grouped by structure of Graphs
      $graphs = Graph::orderBy('created_at', 'desc')->where('publish', true)->whereNull('page_id')->get()->groupBy([function($item) {
        return $item['options']['chart']['type'];
      }]);

      // Consider refactoring to Collections
      $options = [];
      foreach($graphs as $key => $value) {
        $group = [
          'graphtype' => $key,
          'graphs' => $value
        ];

        $options[] = $group;
      }

      return Inertia::render('Admin/Pages/Edit', [
        'page' => $page,
        'selected_datasets' => $selected_datasets,
        'countries' => Country::orderBy('country')->get(),
        'resources' => Resource::orderBy('title')->get(),
        'graphs' => Graph::where('page_id', $page->id)->get(),
        'graphoptions' => $options,
        'datasets' => Dataset::where('status', 'imported')->orderBy('dataset_name')->get()->map->only('id', 'dataset_name'),
        'codebook' => Codebook::orderBy('matching_name')->get()->map->only('id', 'matching_name'),
        'equations' => Equation::orderBy('name')->get()->map->only('id', 'name'),
      ]);
    }

    public function copyImage() 
    {
      $upload = Request::all();

      Storage::copy(
        $upload['key'],
        str_replace('tmp/', 'public/'.$upload['directory'].'/', $upload['key'])
      );

      return Redirect::back();
    }

    public function duplicatepage(Page $page) 
    {
      return Inertia::render('Admin/Pages/Duplicate', [
        'page' => $page,
        'datasets' => Dataset::where('status', 'imported')->orderBy('dataset_name')->get()->map->only('id', 'dataset_name')
      ]);
    }

    public function duplicate() 
    { 

      $page = collect(Request::get('page'));
      $slug = Str::slug(Request::get('title'));
      $updated_dataset = Request::get('dataset');

      //dd(collect($page['meta']));

      $newpage = Page::create([
        'title' => Request::get('title'),
        'slug' => $slug,
        'description' => Request::get('description'), 
        'publish' => false,
        'meta' => $page['meta'],
        'hero' => $page['hero'],
        'blocks' => $page['blocks'],
        'caregiver' => $page['caregiver'],
        'footer' => $page['footer']
      ]);
      

      // Clone all the graphs
      $newblocks = $newpage->blocks;
      foreach ($newblocks as &$block) {
        if (isset($block['graph'])) {
          $graph = Graph::find($block['graph']);
          $newgraph = Graph::create($graph->toArray());
          $block['graph'] = $newgraph->id;

          $rows = $newgraph->rows;
          foreach($rows as &$graphrow) {
            if (isset($graphrow['dataset'])) {
              foreach($graphrow['dataset'] as &$dataset) {
                $dataset['id'] = $updated_dataset['id'];
                $dataset['dataset_name'] = $updated_dataset['dataset_name'];
              }
            }
          }

          $newgraph->update(['rows' => $rows, 'page_id' => $newpage->id]);
          
        }

        if ($block['component'] === 'DualGraph') {
          // Left Side
          $leftgraph = Graph::find($block['left']['graph']);
          $newleftgraph = Graph::create($leftgraph->toArray());
          
          $block['left']['graph'] = $newleftgraph->id;

          $rows = $newleftgraph->rows;
          foreach($rows as &$graphrow) {
            if (isset($graphrow['dataset'])) {
              foreach($graphrow['dataset'] as &$dataset) {
                $dataset['id'] = $updated_dataset['id'];
                $dataset['dataset_name'] = $updated_dataset['dataset_name'];
              }
            }
          }
          $newleftgraph->update(['rows' => $rows, 'page_id' => $newpage->id]);

          // Right Side
          $rightgraph = Graph::find($block['right']['graph']);
          $newrightgraph = Graph::create($rightgraph->toArray());
          
          $block['right']['graph'] = $newrightgraph->id;

          $rows = $newrightgraph->rows;
          foreach($rows as &$graphrow) {
            if (isset($graphrow['dataset'])) {
              foreach($graphrow['dataset'] as &$dataset) {
                $dataset['id'] = $updated_dataset['id'];
                $dataset['dataset_name'] = $updated_dataset['dataset_name'];
              }
            }
          }
          $newrightgraph->update(['rows' => $rows, 'page_id' => $newpage->id]);
        }

      }

      $newcaregiver = $newpage->caregiver;
      foreach ($newcaregiver as &$block) {
        if (isset($block['graph'])) {
          $graph = Graph::find($block['graph']);
          $newgraph = Graph::create($graph->toArray());
          $block['graph'] = $newgraph->id;

          $rows = $newgraph->rows;
          foreach($rows as &$graphrow) {
            if (isset($graphrow['dataset'])) {
              foreach($graphrow['dataset'] as &$dataset) {
                $dataset['id'] = $updated_dataset['id'];
                $dataset['dataset_name'] = $updated_dataset['dataset_name'];
              }
            }
          }
          $newgraph->update(['rows' => $rows, 'page_id' => $newpage->id]);
        }

        if ($block['component'] === 'DualGraph') {
          // Left Side
          $leftgraph = Graph::find($block['left']['graph']);
          $newleftgraph = Graph::create($leftgraph->toArray());
          
          $block['left']['graph'] = $newleftgraph->id;

          $rows = $newleftgraph->rows;
          foreach($rows as &$graphrow) {
            if (isset($graphrow['dataset'])) {
              foreach($graphrow['dataset'] as &$dataset) {
                $dataset['id'] = $updated_dataset['id'];
                $dataset['dataset_name'] = $updated_dataset['dataset_name'];
              }
            }
          }
          $newleftgraph->update(['rows' => $rows, 'page_id' => $newpage->id]);

          // Right Side
          $rightgraph = Graph::find($block['right']['graph']);
          $newrightgraph = Graph::create($rightgraph->toArray());
          
          $block['left']['graph'] = $newrightgraph->id;

          $rows = $newrightgraph->rows;
          foreach($rows as &$graphrow) {
            if (isset($graphrow['dataset'])) {
              foreach($graphrow['dataset'] as &$dataset) {
                $dataset['id'] = $updated_dataset['id'];
                $dataset['dataset_name'] = $updated_dataset['dataset_name'];
              }
            }
          }
          $newrightgraph->update(['rows' => $rows, 'page_id' => $newpage->id]);
        }

      }

      $newpage->update(['blocks' => $newblocks]);
      $newpage->update(['caregiver' => $newcaregiver]);




      return Redirect::back()->with('success', 'Page has been duplicated.');
    }

    public function publish(Page $page)
    {

      // If the page is being set to publish, make sure a dataset is set.
      if (count($page->datasets) == 0) {
        Request::validate([
          'datasets' => ['required']
        ]);
      }

      $page->update(Request::except('_method', 'uploads', 'datasets'));

      return Redirect::back()->with('success', 'Page has been updated');
    }

    public function update(Page $page)
    {
      // Request::validate([
      //   'datasets' => ['required']
      // ]);

      $page->update(Request::except('_method', 'uploads', 'datasets'));
      $dataset = collect(Request::get('datasets'))->pluck('id');


      $page->datasets()->sync($dataset);
      

      return Redirect::back()->with('success', 'Page has been updated');
    }

    public function destroy(Page $page)
    {
      Graph::where('page_id', $page->id)->forceDelete();
      $page->datasets()->detach();
      $page->forceDelete();
      return Redirect::back()->with('success', 'Pages has been removed.');
    }
}
