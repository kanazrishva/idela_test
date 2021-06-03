<?php

namespace App\Http\Controllers;

use App\Page;
use App\Region;
use App\Country;
use App\Organization;
use App\Program;
use App\Equity;
use Inertia\Inertia;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class FrontendController extends Controller
{
    //
  public function index() 
  {


    $meta = [];
    $meta['title'] = 'Welcome';
    $meta['description'] = "Analyse early childhood development data from around the world with Save the Children's interactive data visualization tool, the IDELA Data Explorer.";
    $meta['ogtitle'] = 'Welcome';
    $meta['ogdescription'] = "Analyse early childhood development data from around the world with Save the Children's interactive data visualization tool, the IDELA Data Explorer.";
    $meta['twittertitle'] = 'Welcome';
    $meta['twitterdescription'] = "Analyse early childhood development data from around the world with Save the Children's interactive data visualization tool, the IDELA Data Explorer.";
    $meta['twitterimage'] = 'public/meta/social-share.jpg';
    $meta['ogimage'] = 'public/meta/social-share.jpg';

    $slug = '';
      

    $listPages = [];
    $mapPages = [];
    $pages = Page::select('id', 'slug', 'title', 'hero')
      ->where('publish', 1)
      ->with(['datasets'])
      ->get();

    $country_ids = $pages->pluck('datasets')->map->pluck('country_id')->flatten()->unique()->sort()->values();
    $countries = Country::whereIn('id', $country_ids)->orderBy('country')->get();
    $countries = $countries->filter(function($model) {
      return $model->total_datasets > 0;
    });

    $region_ids = $pages->pluck('datasets')->map->pluck('region_id')->flatten()->unique()->sort()->values();
    $regions = Region::whereIn('id', $region_ids)->orderBy('name')->get();;
    $regions = $regions->filter(function($model) {
      return $model->total_datasets > 0;
    });

    // This is actually pretty cool on how to extract years & total size.
    $years = $pages->pluck('datasets')->map->pluck('dataset_year')->flatten()->unique()->sort()->values();
    $size = $pages->pluck('datasets')->map->pluck('total_rows')->flatten()->unique()->max();

    // Get all the organizations that are live though pages.
    $datasets = $pages->pluck('datasets')->map->pluck('id')->flatten()->unique();
    $organizations = Organization::with(['datasets' => function($query) use ($datasets) {
      $query->whereIn('datasets.id', $datasets);
    }])
    ->withCount(['datasets' => function($query) use ($datasets) {
      $query->whereIn('datasets.id', $datasets);
    }])
    ->orderBy('name', 'ASC')
    ->get()
    ->filter(function($item) {
      return $item->datasets_count > 0;
    });

    $equities = Equity::with(['datasets' => function($query) use ($datasets) {
      $query->whereIn('datasets.id', $datasets);
    }])
    ->withCount(['datasets' => function($query) use ($datasets) {
      $query->whereIn('datasets.id', $datasets);
    }])
    ->orderBy('name', 'ASC')
    ->get()
    ->filter(function($item) {
      return $item->datasets_count > 0;
    });

    $programs = Program::with(['datasets' => function($query) use ($datasets) {
      $query->whereIn('datasets.id', $datasets);
    }])
    ->withCount(['datasets' => function($query) use ($datasets) {
      $query->whereIn('datasets.id', $datasets);
    }])
    ->orderBy('name', 'ASC')
    ->get()
    ->filter(function($item) {
      return $item->datasets_count > 0;
    });


    // List Pages Based on Filter
    if (Request::get('regions') || 
      Request::get('countries') || 
      Request::get('years') || 
      Request::get('size') ||
      Request::get('programs') ||
      Request::get('equities') ||
      Request::get('organizations') ||
      (Request::get('caregiver') == 'true') || 
      Request::get('search') ) {
      
      $q = Page::select('id', 'slug', 'title', 'hero')
        ->where('publish', 1)
        ->with(['datasets' => function($query) {

          if (Request::get('regions')) {
            $query->whereIn('datasets.region_id', Request::get('regions'));
          }

          if (Request::get('countries')) {
            $query->whereIn('datasets.country_id', Request::get('countries'));
          }

          if (Request::get('years')) {
            $query->whereIn('datasets.dataset_year', Request::get('years'));
          }

          if (Request::get('organizations')) {
            $query->withCount(['organizations' => function($qorg) {
              $qorg->whereIn('organization_id', Request::get('organizations'));
            }])->having('organizations_count', '>', 0);
          }

          if (Request::get('equities')) {
            $query->withCount(['equities' => function($qorg) {
              $qorg->whereIn('equity_id', Request::get('equities'));
            }])->having('equities_count', '>', 0);
          }

          if (Request::get('programs')) {
            $query->withCount(['programs' => function($qorg) {
              $qorg->whereIn('program_id', Request::get('programs'));
            }])->having('programs_count', '>', 0);
          }
          if (Request::get('size')) {
            $query->whereBetween('datasets.total_rows', Request::get('size'));
          }

        }]);


        if (Request::get('caregiver') == "true") {
          $q->where('pages.caregiver', '!=', '[]');
        }

      

        if (Request::get('search')) {
          $search = Request::get('search');
          $q->where('pages.title', 'like', '%'.$search.'%');
          $q->orWhere([
            ['publish', 1],
            ['pages.description', 'like', '%'.$search.'%']
          ]);
          $q->orWhere([
            ['publish', 1],
            ['pages.hero', 'like', '%'.$search.'%']
            ]);
        }

        $listPages = $q->get()
        ->filter(function($item) {
          return $item->datasets->count() > 0;
        });
    } else {
      $listPages = $pages;
    }

    if (Request::get('map')) {
      $q = Page::select('id', 'slug', 'title', 'hero')
        ->where('publish', 1)
        ->with(['datasets' => function($query) {
         $query->withCount(['country' => function($query) {
           $query->where('countries.country', Request::get('map'));
         }]);
        }]);
    
      $pages = $q->get();

      $mapPages = [];
      foreach($pages as $page) {
        foreach($page->datasets as $dataset) {
          if ($dataset->country_count > 0) {
            $mapPages[] = $page;
          }
        }
      }

      $mapPages = collect($mapPages)->unique()->values();
    }


    return Inertia::render('Homepage', [
      'pages' => $pages,
      'size' =>  $size,
      'listPages' => $listPages,
      'mapPages' => $mapPages,
      'years' => $years,
      'countries' => $countries,
      'regions' => $regions,
      'organizations' => $organizations,
      'equities' => $equities,
      'programs' => $programs
    ])->withViewData(['meta' => $meta, 'slug' => $slug]);
  }

  public function page(Page $page) 
  {
    $slug = '';

    if ($page->slug != '') {
      $slug = '/data/'.$page->slug;
    }

    return Inertia::render('Page', [
      'page' => $page
    ])->withViewData(['meta' => $page->meta, 'slug' => $slug]);
  }
}
