<?php

namespace App\Http\Controllers;

use App\Dataset;
use App\DatasetMeta;
use App\Codebook;
use App\Equation;
use App\Graph;
use App\Page;
use App\CodebookValue;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CodebookValueRequest;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScatterExport;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GraphController extends Controller
{
  //
  public function index() 
  {
    return Inertia::render('Admin/Graphs/Index', [
      'filters' => Request::all('search', 'trashed'),
      'datasets' => Dataset::where('status', 'imported')->get(),
      'graphs' => Graph::orderBy('created_at', 'desc')
        ->whereNull('page_id')
        ->filter(Request::only('search', 'trashed'))
        ->paginate()
    ]);
  }

  public function create() 
  {
    return Inertia::render('Admin/Graphs/Create');
  }

  public function store() 
  {
    Request::validate([
      'name' => ['required']
    ]);

    $graph = Graph::create(Request::all());

    return Redirect::route('graphs.edit', $graph)->with('success', 'Graph has been added successfully.');
  }

  public function edit(Graph $graph)
  {
    return Inertia::render('Admin/Graphs/Edit', [
      'datasets' => Dataset::where('status', 'imported')->orderBy('dataset_name')->get()->map->only('id', 'dataset_name'),
      'codebook' => Codebook::orderBy('matching_name')->get()->map->only('id', 'matching_name'),
      'equations' => Equation::orderBy('name')->get()->map->only('id', 'name'),
      'graph' => $graph
    ]);
  }

  public function duplicate() 
  {
    $graph = Graph::find(Request::get('graph'));
    $page = Page::find(Request::get('page_id'));

    $graph = Graph::create([
      'name' => $graph->name,
      'page_id' => $page->id,
      'description' => $graph->description,
      'rows' => $graph->rows,
      'options' => $graph->options,
      'publish' => true,
    ]);

    //Update the graph with the datasets in the page?
    

    return ['graph' => $graph, 'graphs' => Graph::all()];
  }

  public function update(Graph $graph)
  {
    $graph->update(Request::all());
    return Redirect::back()->with('success', 'Graph successfully updated');
  }

  public function destroy(Graph $graph)
  { 
    $graph->delete();
    return Redirect::back()->with('success', 'Graph removed.');
  }

  public function restore(Graph $graph)
  {
    $graph->restore();
    $graph->update(['publish' => 0]);

    return Redirect::back()->with('success', 'Graph restored.');
    
  }

  public function export(Request $request) 
  {
    //dd(Request::get('plotly'));
    $export = new ScatterExport(Request::get('plotly'));
    $timestamp = Carbon::now()->timestamp;
    $name = Str::slug(Request::get('title').' - '.$timestamp);

    Excel::store($export, 'tmp/'.$name.'.xlsx', 's3');
    //dd(Storage::url('tmp/scatter.xlsx'));

    return Storage::url('tmp/'.$name.'.xlsx');

  }


  // This is used for the preview of the graph on both the edit and the create screens
  public function generate()
  {

    $rows = Request::get('rows');
    $chart = Request::get('type');
    $rowsCount = count($rows); // How many series of data do we have.
    $generate = new Generate();
    $series = [];

    // Loop through the rows of series data & get all the information
    foreach($rows as $row) {
      
      $seriesData = [];
      $seriesLabels = [];
      $seriesQty = [];
      
      // Generate the dataLabel off of each row
      $dataLabel = [];
      


      // Loop through and get the equations and generate the totals
      foreach ($row['data'] as $data) {
        $equation = Equation::find($data['item']['id']);
        $result = $generate->dataset($equation, $row['dataset']);
        
        // Generate the dataLabel off of each row
        $dataLabel[] = $data['label'];

        switch ($equation->action) {
          case 'avggroupby':
          case 'avggroupbytot':

            
            foreach(collect($result)->pluck('grp') as $mapped) {
              $hasLabel = CodebookValue::where('codebook_id', $equation->value)->where('value', $mapped)->first();
              if ($hasLabel) {
                $seriesLabels[] = $hasLabel->label;
              } else {
                $seriesLabels[] = $mapped;
              }
            }

            $seriesQty = collect($result)->pluck('qty');
            $seriesData = collect($result)->pluck('result');
          break;
          case 'cntgroupby':
            $seriesLabels = collect($result)->pluck('grp');
            $seriesData = collect($result)->pluck('result');
          break;
          case 'distribution':
            $seriesData[] = $result['result'];
            $seriesQty[] = $result['qty'];
            $seriesLabels[] = $data['label'];
          break;
          case 'pctgroupby':
            $seriesLabels[] = $data['label'];
            $seriesQty[] = $result['qty'];
            $seriesData[] = $result['result'];
          break;
          case 'cnt':
            $seriesLabels[] = $data['label'];
            $seriesQty[] = $result;
            $seriesData[] = $result;
          break;
          case 'scatter':
            $seriesLabels[] = $data['label'];
            $seriesQty[] = $result;
            $seriesData[] = $result;
          break;
          default: 
            $seriesData[] = $result['result'];
            $seriesQty[] = $result['qty'];
            $seriesLabels[] = $data['label'];
          break;
        }

      }

      //dd($seriesLabels);

      // Transform the data based on the type of graph
      $series[] = [
        'equation' => $equation,
        'data' =>  $seriesData,
        'labels' => $seriesLabels,
        'qty' => $seriesQty,
        'name' => $row['name']
      ];
    }

    return $series;
  }

  public function frontend(Graph $graph)
  {
    $filters = Request::all();    // Determine if there are Filters or Compare By
    $rows = $graph->rows;         // Get all the rows (series) and the data points within it.
    $rowsCount = count($rows);    // How many series of data do we have.
    $generate = new Generate();   // Create an instance of the equation generator
    $series = [];                 // Determine the final series of data to be used.

    // If there is one row and a compare by, lets generate that data here
    $compareByIntervention = isset($filters['compareByIntervetion']);
    $compareByGender = isset($filters['compareByGender']);

    // Determine if the graph is eligble for the compare by.
    if ($rowsCount === 1 && ($compareByIntervention || $compareByGender) == true) {
      if ($filters['compareByIntervetion'] == 'true') {
        // Generates the Intervention Series of Data
        return $this->intervention($graph, $filters);
      }
   
      if ($filters['compareByGender'] == 'true') {
        // Generates the Gender Series of Data
        return $this->gender($graph, $filters);
      }
    }

    // Loop through the rows of series data & get all the information
    foreach($rows as $row) {
      $seriesData = [];
      $seriesLabels = [];
      $seriesQty = [];

      // Generate the dataLabel off of each row
      $dataLabel = [];

      // Loop through and get the equations and generate the totals
      foreach ($row['data'] as $data) {
        $equation = Equation::find($data['item']['id']);

        // Is there an intervention status?
        if (isset($filters['compareByIntervetion'])) {
          if ($filters['compareByIntervetion'] == 'true') {
            $interventionNone = $generate->dataset($equation, $row['dataset'], $filters);
            $interventionWith = $generate->dataset($equation, $row['dataset'], $filters);
          } else {
            $result = $generate->dataset($equation, $row['dataset'], $filters);
          };
        } else {
          $result = $generate->dataset($equation, $row['dataset'], $filters);
        }

        // Generate the dataLabel off of each row
        $dataLabel[] = $data['label'];

        switch ($equation->action) {
          case 'avggroupby':
          case 'avggroupbytot':

            foreach(collect($result)->pluck('grp') as $mapped) {
              $hasLabel = CodebookValue::where('codebook_id', $equation->value)->where('value', $mapped)->first();
              if ($hasLabel) {
                $seriesLabels[] = $hasLabel->label;
              } else {
                $seriesLabels[] = $mapped;
              }
            }

            //$seriesLabels = collect($result)->pluck('grp');
            $seriesQty = collect($result)->pluck('qty');
            $seriesData = collect($result)->pluck('result');
          break;
          case 'cntgroupby':
            $seriesLabels = collect($result)->pluck('grp');
            $seriesData = collect($result)->pluck('result');
          break;
          case 'distribution':
            $seriesData[] = $result['result'];
            $seriesQty[] = $result['qty'];
            $seriesLabels[] = $data['label'];
          break;
          case 'pctgroupby':
            $seriesLabels[] = $data['label'];
            $seriesQty[] = $result['qty'];
            $seriesData[] = $result['result'];
          break;
          case 'cnt':
            $seriesLabels[] = $data['label'];
            $seriesQty[] = $result;
            $seriesData[] = $result;
          break;
          case 'scatter':
            $seriesLabels[] = $data['label'];
            $seriesQty[] = $result;
            $seriesData[] = $result;
          break;
          default: 
            $seriesData[] = $result['result'];
            $seriesQty[] = $result['qty'];
            $seriesLabels[] = $data['label'];
          break;
        }

      }

      // Transform the data based on the type of graph
      $series[] = [
        'equation' => $equation,
        'data' =>  $seriesData,
        'labels' => $seriesLabels,
        'qty' => $seriesQty,
        'name' => $row['name']
      ];
    }

    $graph->series = $series;

    return $graph;
  }


  // Get the filterable information from the specific graph
  public function filters(Graph $graph)
  {
    // Get all the datasets from the graph
    $datasets = [];
    foreach($graph->rows as $row) {
      if (isset($row['dataset'])) {
        foreach($row['dataset'] as $dataset) {
          $datasets[] = $dataset['id'];
        }
      }
    } 

    // Get the unique values of gender from the datasets.
    $genders = DatasetMeta::select('value')
      ->whereIn('datasets_id', $datasets)
      ->where('codebook_id', 11)
      ->where('value', '!=', 'header')
      ->groupBy('value')
      ->get()
      ->map->only('value')
      ->flatten();

    // Map values of gender to Male/Female
    $gendermapping = [];
    foreach($genders->toArray() as $gender) {
      if ($gender == 1 || strtolower($gender) == 'girl' || strtolower($gender) == 'female') {
        $gendermapping['Girls'] = $gender;
      } else {
        $gendermapping['Boys'] = $gender;
      }
    }

    // Get the unique values of age from the datasets.
    $ages = DatasetMeta::select('value')
    ->whereIn('datasets_id', $datasets)
    ->where('codebook_id', 297)
    ->where('value', '!=', 'header')
    ->groupBy('value')
    ->orderBy('value', 'ASC')
    ->get()
    ->map->only('value')
    ->flatten();

    // Get the unique values of locations.
    $districts = DatasetMeta::select('value')
    ->whereIn('datasets_id', $datasets)
    ->where('codebook_id', 454)
    ->where([['value', '!=', 'header'], ['value', '!=', '']])
    ->groupBy('value')
    ->get()
    ->map->only('value')
    ->flatten()
    ->sort()
    ->values();

    // Does it have a treatment column
    $treatment = DatasetMeta::select('value')
    ->whereIn('datasets_id', $datasets)
    ->where('value', 'header')
    ->where('codebook_id', 422)->count();

    // How many series does the graph have?

    return [
      'genders' => $gendermapping, 
      'ages' => $ages, 
      'treatment' => $treatment,
      'districts' => $districts,
      'seriesCount' => count($graph->rows),
      'graphType' => $graph->options['chart']['type']
    ];

  }

  private function intervention($graph, $filters)
  {
    // This is the data we'll need to compare by
    $rows = $graph->rows;
    $data = $rows[0]['data'];
    $datasets = $rows[0]['dataset'];
    $generate = new Generate();

    // Get results for no Intervention
    $filters['treatment'] = 0;
    $series[] = $this->compareByProcess($generate, $data, $datasets, $filters, 'No Intervention');

    $filters['treatment'] = 1;
    $series[] = $this->compareByProcess($generate, $data, $datasets, $filters, 'Intervention');

    //dd($graph->options);
    $options = $graph->options;
    $options['dataLabels']['enabled'] = true;
    $options['dataLabels']['textAnchor'] = 'start';
    $options['dataLabels']['style']['fontFamily'] = 'Oswald';
    $options['dataLabels']['style']['colors'] = ['#fff'];
    $options['dataLabelsFormat'] = '{seriesName}';

    $graph->options = $options;
    $graph->series = $series;
    return $graph;
  }

  private function gender($graph, $filters)
  {
    // This is the data we'll need to compare by
    $rows = $graph->rows;
    $data = $rows[0]['data'];
    $datasets = $rows[0]['dataset'];
    $generate = new Generate();

    // Get results for no Intervention
    $filters['gender'] = 0;
    $series[] = $this->compareByProcess($generate, $data, $datasets, $filters, 'Boys');

    $filters['gender'] = 1;
    $series[] = $this->compareByProcess($generate, $data, $datasets, $filters, 'Girls');

    //dd($graph->options);
    $options = $graph->options;
    $options['dataLabels']['enabled'] = true;
    $options['dataLabels']['textAnchor'] = 'start';
    $options['dataLabels']['style']['fontFamily'] = 'Oswald';
    $options['dataLabels']['style']['colors'] = ['#fff'];
    $options['dataLabelsFormat'] = '{seriesName}';

    $graph->options = $options;
    $graph->series = $series;
    return $graph;
  }

  // Generate the call and return values we expect from the equations
  private function compareByProcess($generate, $data, $datasets, $filters, $seriesName) {
    $seriesData = [];
    $seriesLabels = [];
    $seriesQty = [];

    foreach($data as $item) {
      $equation = Equation::find($item['item']['id']);
      $results = $generate->dataset($equation, $datasets, $filters);

      // Assign the results to the first series of data
      $seriesData[] = $results['result'];
      $seriesQty[] = $results['qty'];
      $seriesLabels[] = $item['label'];
    }

    // Add to series
    return [
      //'equation' => $equation,
      'data' =>  $seriesData,
      'labels' => $seriesLabels,
      'qty' => $seriesQty,
      'name' => $seriesName
    ];
  }

  
}

