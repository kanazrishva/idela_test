<?php

namespace App\Http\Controllers;

use App\Dataset;
use App\Country;
use App\Region;
use App\Organization;
use App\Equity;
use App\Program;
use App\Codebook;
use App\CodebookColumn;
use App\DatasetMeta;
use App\Imports\DatasetImport;
use App\Jobs\ProcessDataset;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DatasetController extends Controller
{
  protected $temp_headers;

  public function index()
  {
    return Inertia::render('Admin/Datasets/Index', [
      'filters' => Request::all('search', 'trashed'),
      'datasets' => Dataset::orderBy('created_at')
        ->with(['country'])
        ->filter(Request::only('search', 'trashed'))
        ->paginate()
    ]);
  }

  public function show(Dataset $dataset)
  {
    $start = 1;
    $end = 20;

    if ($page = Request::get('page')) {
      $start = ($page * 20) + 1;
      $end = $start + 19;
    }

    // Get the headers
    $headers = DatasetMeta::where('datasets_id', $dataset->id)
      ->with(['codebook'])
      ->where('value', 'header')
      ->orderBy('column_name')
      ->get();

    // How many rows are there
    $totalrows = DatasetMeta::where('datasets_id', $dataset->id)->max('row');
    $totalpages = $totalrows / 20;

    $results = DatasetMeta::where('datasets_id', $dataset->id)
      ->whereBetween('row', [$start, $end])
      ->orderBy('column_name')
      ->get()
      ->groupBy('row')
      ->sort();

    return Inertia::render('Admin/Datasets/Show', [
      'dataset' => $dataset,
      'headers' => $headers,
      'totalrows' => $totalrows,
      'totalpages' => $totalpages,
      'results' => $results->values()->all(),
      'page' => (Int) $page
    ]);
  }

  public function create()
  {
    return Inertia::render('Admin/Datasets/Create', [
      'countries' => Country::orderBy('country')->get()->map->only('id', 'country'),
      'regions' => Region::orderBy('name')->get()->map->only('id', 'name'),
      'organizations' => Organization::orderBy('name')->get()->map->only('id', 'name'),
      'programs' => Program::orderBy('name')->get()->map->only('id', 'name'),
      'equities' => Equity::orderBy('name')->get()->map->only('id', 'name'),
    ]);
  }

  public function store()
  {

    Request::validate([
      'dataset_name' => ['required'],
      'dataset_year' => ['required'],
      'region_id' => ['required'],
      'country_id' => ['required'],
      'dataset' => ['required'],
      'verify1' => ['required'],
      'verify2' => ['required'],
      'verify3' => ['required'],
      'verify4' => ['required'],
      'verify5' => ['required'],
      'verify6' => ['required'],
      'verify7' => ['required'],
    ]);

    // You have to set the extension to be explicit otherwise it will upload as a zip
    $upload = Request::input('dataset');
    $filename = $upload['uuid'].'.'.$upload['extension'];
    Storage::copy($upload['key'], 'datasets/'.$filename);

    $dataset = Dataset::create([
      'dataset_name' => Request::get('dataset_name'),
      'dataset_description' => Request::get('dataset_description'),
      'dataset_year' => Request::get('dataset_year'),
      'region_id' => Request::get('region_id'),
      'country_id' => Request::get('country_id'),
      'dataset' => 'datasets/'.$filename ,
      'status' => 'uploaded'
    ]);

    $dataset->organizations()->sync(Request::get('organization'));
    $dataset->equities()->sync(Request::get('equity'));
    $dataset->programs()->sync(Request::get('program'));

    return Redirect::route('datasets')->with('success', 'Dataset added succefully');
  }

  public function edit(Dataset $dataset)
  {
    return Inertia::render('Admin/Datasets/Edit', [
      'countries' => Country::orderBy('country')->get()->map->only('id', 'country'),
      'regions' => Region::orderBy('name')->get()->map->only('id', 'name'),
      'organizations' => Organization::orderBy('name')->get()->map->only('id', 'name'),
      'programs' => Program::orderBy('name')->get()->map->only('id', 'name'),
      'equities' => Equity::orderBy('name')->get()->map->only('id', 'name'),
      'dataset' => [
        'id' => $dataset->id,
        'organizations' => $dataset->organizations->pluck('id'),
        'equities' => $dataset->equities->pluck('id'),
        'programs' => $dataset->programs->pluck('id'),
        'region_id' => $dataset->region_id,
        'dataset_name' => $dataset->dataset_name,
        'dataset_description' => $dataset->dataset_description,
        'dataset_year' => $dataset->dataset_year,
        'country_id' => $dataset->country_id
      ]
    ]);
  }

  public function update(Dataset $dataset)
  {

    Request::validate([
      'dataset_name' => ['required'],
      'dataset_year' => ['required'],
      'region_id' => ['required'],
      'country_id' => ['required']
    ]);

    $dataset->update(Request::except(['organization', 'equity', 'program']));

    $dataset->organizations()->sync(Request::get('organization'));
    $dataset->equities()->sync(Request::get('equity'));
    $dataset->programs()->sync(Request::get('program'));

    return Redirect::back()->with('success', 'Dataset information updated');
  }

  public function destroy(Dataset $dataset)
  {

    // Remove the file from the system

    // Check to see if the table was created, if so, remove it

    // Remove all meta information (we'll move this to permanent deletion)
    // DatasetMeta::where('datasets_id', $dataset->id)->delete();

    // Remove all other associations
    // $dataset->equities()->detach();
    // $dataset->organizations()->detach();
    // $dataset->programs()->detach();

    $dataset->delete();
    return Redirect::back()->with('success', 'Dataset removed.');
  }

  /**
   * when dataset status is uploaded not imported
   */
  public function process(Dataset $dataset)
  {
    ini_set('max_execution_time', 3600);

    //import excel file from storage
    $headers = (new HeadingRowImport())->toCollection($dataset->dataset);
    $codebook = Codebook::select('id', 'matching_name', 'matching_description')->orderBy('matching_name')->get();
    $columns = CodebookColumn::with('codebook')->get();

    $this->temp_headers = [];

    // $header[0][0] first collection item from first sheet = headers of first sheets
    foreach ($headers[0][0] as $header)
    {
      $slug = Str::slug($header, '_'); // slugifying header with underscore
      $propheader = $this->checkArray($slug, $header, $this->temp_headers);

      $codebook_item = $columns->firstWhere('column_name', $propheader);

      $excel[] = (object) [
        'header' => $propheader,
        'orig_header' => $header,
        'codebook' => (collect($codebook_item)->has('codebook')) ? $codebook_item->codebook : null
      ];
    }

    return Inertia::render('Admin/Datasets/Process', [
      'codebook' => $codebook,
      'excel' => $excel,
      'dataset' => $dataset
    ]);
  }

  public function import(Dataset $dataset)
  {

    $excel['excel'] = Request::get('excel');



    $validator = Validator::make($excel, [
      'excel.*.codebook.id' => 'distinct'
    ]);

    if ($validator->fails()) {
      return Redirect::route('datasets.process', $dataset)->withErrors($validator);
    }


    //Update the process to processing
    $dataset->update([
      'status' => 'importing'
    ]);

    // Offload to a query system, don't forge to start this :)
    ProcessDataset::dispatch($dataset, Request::all());

   return Redirect::route('datasets')->with('success', 'Processing the import.');
  }

  public function codebook(Dataset $dataset)
  {
    return Inertia::render('Admin/Datasets/Code', [
      'search' => Request::all(),
      'dataset' => $dataset,
      'filters' => Request::all('search'),
      'codebook' => Codebook::orderBy('matching_name')->get()->map->only('id', 'matching_name'),
      'headers' => DatasetMeta::where('value', 'header')
        ->filter(Request::only('search'))
        ->with(['codebook'])
        ->where('datasets_id', $dataset->id)
        ->orderBy('column_name')
        ->paginate()
    ]);
  }

  public function codebookUpdate(DatasetMeta $datasetmeta, Dataset $dataset)
  {

    if (Request::get('codebook_id') !== null) {
      Request::validate([
        'codebook_id' => ['sometimes',
          Rule::unique('datasets_meta', 'codebook_id')->where(function($query) use ($dataset) {
            return $query->where('datasets_id', $dataset->id);
          })
        ]
      ]);
    }

    DatasetMeta::where('datasets_id', $dataset->id)
      ->where('column_name', $datasetmeta->column_name)
      ->update(['codebook_id' => Request::get('codebook_id')]);

    return Redirect::back()->with('success', 'Dataset column updated with codebook.');
  }

  /**
   * creates postfix numbered headers in case of repeating header names
   */
  private function checkArray($slug, $value, $arr, $i = 0)
  {
    if (in_array($slug, $arr)) {
      $i++;
      $slugged = Str::slug($value . ' ' . $i, '_');
      return $this->checkArray($slugged, $value, $this->temp_headers, $i);
    } else {
      if ($i !== 0) {
        $slugged = Str::slug($value . ' ' . $i, '_');
        $this->temp_headers[] = $slugged;
        return $slugged;
      } else {
        $slugged = $slug;
        $this->temp_headers[] = $slug;
      }
      return $slugged;
    }
  }
}
