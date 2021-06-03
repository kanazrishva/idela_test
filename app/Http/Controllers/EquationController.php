<?php

namespace App\Http\Controllers;

use App\Dataset;
use App\Codebook;
use App\Equation;
use App\EquationMeta;
use App\DatasetMeta;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CodebookValueRequest;
use Illuminate\Support\Str;

class EquationController extends Controller
{
  //
  public function index()
  {
    return Inertia::render('Admin/Equations/Index', [
      'filters' => Request::all('search'),
      'equations' => Equation::filter(Request::only('search'))->orderBy('created_at')->paginate()
    ]);
  }

  public function create()
  {
    return Inertia::render('Admin/Equations/Create');
  }

  public function store()
  {
    $equation = Equation::create([
      'name' => Request::get('name'),
      'description' => Request::get('description'),
      'action' => Request::get('action'),
      'type' => Request::get('type')
    ]);

    return Redirect::route('equations.edit', $equation)->with('success', 'Equation added successfully');
  }

  public function edit(Equation $equation)
  {
    if ($equation->type == 'codebook') {
      $selection = Codebook::all();
    } else {
      $selection = Equation::all();
    }

    if ($equation->action === 'cnt') {
      $meta = EquationMeta::where('equation_id', $equation->id)->with(['codebook'])->first();
      $selected = (collect($meta)->has('codebook')) ? $meta->codebook : null;
    }

    if ($equation->action === 'distribution') {
      $meta = EquationMeta::where('equation_id', $equation->id)->with(['codebook'])->first();
      $selected = (collect($meta)->has('codebook')) ? $meta->codebook : null;
    }

    if ($equation->action === 'pct') {
      $meta = EquationMeta::where('equation_id', $equation->id)->get()->pluck('value');
      $selected = Codebook::whereIn('id', $meta)->get();
    }

    if ($equation->action === 'scatter') {
      $meta = EquationMeta::where('equation_id', $equation->id)->get()->pluck('value');
      $selected = Codebook::whereIn('id', $meta)->get();
    }

    if ($equation->action === 'pctgroupby') {
      $meta = EquationMeta::where('equation_id', $equation->id)->get()->pluck('value');
      $selected = Codebook::whereIn('id', $meta)->get();
    }

    if ($equation->action === 'avggroupby') {
      $meta = EquationMeta::where('equation_id', $equation->id)->get()->pluck('value');
      $selected = Codebook::whereIn('id', $meta)->get();
      $equation->value = Codebook::where('id', $equation->value)->first();
    }

    if ($equation->action === 'avggroupbytot') {
      $meta = EquationMeta::where('equation_id', $equation->id)->get()->pluck('value');
      $selected = Codebook::whereIn('id', $meta)->get();
      $equation->value = Codebook::where('id', $equation->value)->first();
    }

    if ($equation->action === 'cntgroupby') {
      $meta = EquationMeta::where('equation_id', $equation->id)->with(['codebook'])->first();
      $selected = (collect($meta)->has('codebook')) ? $meta->codebook : null;
      $equation->value = Codebook::where('id', $equation->value)->first();
    }


    return Inertia::render('Admin/Equations/Edit', [
      'equation' => $equation,
      'selection' => $selection,
      'selected' => $selected
    ]);
  }

  public function update(Equation $equation)
  {
    EquationMeta::where('equation_id', $equation->id)->delete();

    $values = Request::get('values');

    switch ($equation->action) {
      case 'cnt':
        EquationMeta::create([
          'equation_id' => $equation->id,
          'value' => $values['id']
        ]);

        $equation->update([
          'value' => Request::get('value'),
          'name' => Request::get('name'),
          'description' => Request::get('description'),
        ]);
      break;
      case 'distribution':
        EquationMeta::create([
          'equation_id' => $equation->id,
          'value' => $values['id']
        ]);

        $equation->update([
          'value' => Request::get('value'),
          'name' => Request::get('name'),
          'description' => Request::get('description'),
        ]);
      break;
      case 'pct':
        foreach($values as $value) {
          $value = collect($value);
          EquationMeta::create([
            'equation_id' => $equation->id,
            'value' => $value['id']
          ]);
        };

        $equation->update([
          'value' => Request::get('value'),
          'name' => Request::get('name'),
          'description' => Request::get('description'),
        ]);
      break;
      case 'pctgroupby':

        EquationMeta::create([
          'equation_id' => $equation->id,
          'value' => $values['id']
        ]);

        $equation->update([
          'value' => Request::get('value'),
          'name' => Request::get('name'),
          'description' => Request::get('description'),
        ]);
      break;
      case 'avggroupby':
        foreach($values as $value) {
          $value = collect($value);
          EquationMeta::create([
            'equation_id' => $equation->id,
            'value' => $value['id']
          ]);
        };

        $groupby = Request::get('value');

        $equation->update([
          'value' => $groupby['id'],
          'name' => Request::get('name'),
          'description' => Request::get('description'),
        ]);
      break;
      case 'cntgroupby':
      case 'avggroupbytot':
        EquationMeta::create([
          'equation_id' => $equation->id,
          'value' => $values['id']
        ]);

        $groupby = Request::get('value');

        $equation->update([
          'value' => $groupby['id'],
          'name' => Request::get('name'),
          'description' => Request::get('description'),
        ]);
      break;
      case 'scatter':
        EquationMeta::create([
          'equation_id' => $equation->id,
          'value' => $values['id']
        ]);

        $groupby = Request::get('value');

        $equation->update([
          'value' => $groupby['id'],
          'name' => Request::get('name'),
          'description' => Request::get('description'),
        ]);
      break;
    }



    return Redirect::back()->with('success', 'Equation successfully updated!');
  }

  public function show(Equation $equation)
  {
    // Variables Needed
    //$dataset = Dataset::find(5);
    $codebook = $equation->meta->pluck('value')->implode(', ');

    var_dump('Age is greater than 5, gender is female, datasets in 2020');
    $global_sql1 =   $this->sqlGlobalFilter($equation, $codebook);
    var_dump($global_sql1);

    var_dump('Global for shape percent no filters');
    $global_sql2 =   $this->sqlGlobal($equation, $codebook);
    var_dump($global_sql2);


    // return Inertia::render('Admin/Equations/Show', [
    //   'datasets' => Dataset::orderBy('dataset_name')->get()->map->only('id', 'dataset_name'),
    //   'equation' => $equation
    // ]);
  }

  public function destroy(Equation $equation)
  {
    $equation->meta()->delete();
    $equation->delete();
    return Redirect::back()->with('success', 'Equation removed.');
  }

  public function generate(Equation $equation, Dataset $dataset)
  {
    $result = $this->sqlDataset($equation, $dataset);
    return $result;
  }

  // If used in a Global Sense
  private function sqlGlobal(Equation $equation, $codebooks, $filters = null)
  {
    // Variables Needed, this will need tochange to published and the global flag
    $dataset_query =  Dataset::where('status', 'imported');

    if ($filters) {
      $dataset_query = $dataset_query->where('dataset_year', '2020');
    }

    $datasets = $dataset_query->get();
    $slug = Str::slug($equation->name, '_');
    $results = [];

    if ($filters) {
      $years  = $datasets->pluck('id');
      $gender = DatasetMeta::select('datasets_id', 'row')->whereIn('datasets_id', $years)->where('row', '!=', 'header')->where('codebook_id', 11)->where('value', 1)->get()->groupBy('datasets_id');
      $age    = DatasetMeta::select('datasets_id', 'row')->whereIn('datasets_id', $years)->where('row', '!=', 'header')->where('codebook_id', 12)->where('value', '>', 5)->get()->groupBy('datasets_id');
    }

    foreach($datasets as $dataset) {
      $sql = "";

      if ($filters !== null) {

        // Get the Intersect of Rows
        $collection_gender = collect($gender[$dataset->id])->pluck('row');
        $collection_age = collect($age[$dataset->id])->pluck('row');
        $intersection = $collection_gender->intersect($collection_age);
        $rows = $intersection->implode(',');

        $sql .= "(";
        $sql .= $this->equationAction($equation->action, 'result', 4, $intersection->count());
        $sql .= " FROM datasets_meta WHERE codebook_id IN ($codebooks)";
        $sql .= " AND datasets_meta.row IN ($rows)";
        $sql .= " AND datasets_id = $dataset->id";
        $sql .= ")";
        $results[] = $sql;
      } else {
        $sql .= "(";
        $sql .= $this->equationAction($equation->action, 'result', 4, $dataset->meta_rows_max);
        $sql .= " FROM datasets_meta WHERE codebook_id IN ($codebooks)";
        $sql .= " AND datasets_id = $dataset->id";
        $sql .= " AND datasets_meta.row != 'header'";
        $sql .= ")";
        $sql = DB::select($sql);
        $results[] = $sql[0]->result;
      }
    }

    $collection_results = collect($results);

    if ($filters) {
      if ($equation->action === 'pct') {
        $count = $collection_results->count();
        $collection_sql = "(SELECT SUM(";
        $collection_sql .= $collection_results->implode(' + ');
        $collection_sql .= ") / $count AS $slug)";
        $result = collect(DB::select($collection_sql));

        $return_result = $result[0]->{$slug};
      }
    } else {
      $return_result = $collection_results->avg();
    }



    return $return_result;
  }

  private function sqlGlobalFilter(Equation $equation, $codebooks)
  {
    // Variables Needed, this will need tochange to published and the global flag
    $datasets =  Dataset::where('status', 'imported')->where('dataset_year', '2020')->get();
    $slug     = Str::slug($equation->name, '_');
    $years    = $datasets->pluck('id');
    $gender   = DatasetMeta::select('datasets_id', 'row')->whereIn('datasets_id', $years)->where('row', '!=', 'header')->where('codebook_id', 11)->where('value', 1)->get()->groupBy('datasets_id');
    $age      = DatasetMeta::select('datasets_id', 'row')->whereIn('datasets_id', $years)->where('row', '!=', 'header')->where('codebook_id', 12)->where('value', '>', 5)->get()->groupBy('datasets_id');
    $results  = [];

    foreach($datasets as $dataset) {
      $sql = "";

      // Get the Intersect of Rows
      $collection_gender = collect($gender[$dataset->id])->pluck('row');
      $collection_age = collect($age[$dataset->id])->pluck('row');
      $intersection = $collection_gender->intersect($collection_age);
      $rows = $intersection->implode(',');

      $sql .= "(";
      $sql .= $this->equationAction($equation->action, 'result', 4, $intersection->count());
      $sql .= " FROM datasets_meta WHERE codebook_id IN ($codebooks)";
      $sql .= " AND datasets_meta.row IN ($rows)";
      $sql .= " AND datasets_id = $dataset->id";
      $sql .= ")";
      $results[] = $sql;
    }

    $collection_results = collect($results);

    if ($equation->action === 'pct') {
      $count = $collection_results->count();
      $collection_sql = "(SELECT SUM(";
      $collection_sql .= $collection_results->implode(' + ');
      $collection_sql .= ") / $count AS $slug)";
      $result = collect(DB::select($collection_sql));

      $return_result = $result[0]->{$slug};
    }

    return $return_result;
  }


  // Determine what to return based on the action selected by the equation
  private function equationAction($action, $slug, $num = 0, $max = 0)
  {
    $sql = '';
    // Update based on the action
    if ($action === 'pct') {
      $sql = "SELECT ((SUM(value) / $num) / $max) AS $slug";
    }

    return $sql;
  }









  // Single Dataset
  private function sqlDataset(Equation $equation, Dataset $dataset)
  {
    // Variables Needed
    $codebooks = $equation->meta->pluck('value')->implode(', ');
    $sql = "";

    $sql .= $this->equationAction($equation->action, 'result', 4, $dataset->meta_rows_max);
    $sql .= " FROM datasets_meta WHERE codebook_id IN ($codebooks)";
    $sql .= " AND datasets_id = $dataset->id";
    $sql .= " AND datasets_meta.row != 'header'";

    $results = DB::select($sql);
    $return_result = $results[0]->result;

    $collection_results = collect($return_result);
    $return_result = ($collection_results->avg()) * 100;

    return $return_result;
  }

  // Single Dataset Filtered
  private function sqlFilter(Equation $equation, $codebooks, $dataset, $rows)
  {
    // Variables Needed
    $slug = Str::slug($equation->name, '_');
    $sql = "";

    $sql .= $this->equationAction($equation->action, $slug, 4,  $dataset->meta_rows);
    //$sql .= " FROM datasets_meta WHERE (codebook_id, datasets_meta.row) IN ($rows)";
    $sql .= " FROM datasets_meta WHERE (codebook_id) IN ($codebooks) AND datasets_meta.row IN ($rows)";
    $sql .= " AND datasets_id = $dataset->id";

    return $sql;
  }
}
