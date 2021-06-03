<?php

namespace App\Http\Controllers;

use App\Dataset;
use App\Codebook;
use App\Equation;
use App\EquationMeta;
use App\DatasetMeta;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Generate extends Controller
{
    //
    // Single Dataset 
    public function dataset(Equation $equation, $dataset, $filters = null)
    {
      // Variables Needed
      $codebooks = $equation->meta->pluck('value')->implode(', ');
      $sql = "";

      switch($equation->action) {
        case 'cnt':
          $sql = $this->equationCount($equation, $dataset);
        break;
        case 'distribution':
          $sql = $this->equationDistribution($equation, $dataset, $filters);
        break;
        case 'pct':
          $sql = $this->equationPercent($equation, $dataset, $filters);
        break;
        case 'pctgroupby':
          $sql = $this->equationPercentGroupBy($equation, $dataset, $filters);
        break;
        case 'avggroupby':
          $sql = $this->avgGroupBy($equation, $dataset, $filters);
        break;
        case 'avggroupbytot':
          $sql = $this->avgGroupByTot($equation, $dataset);
        break;
        case 'cntgroupby':
          $sql = $this->cntGroupBy($equation, $dataset);
        break;
        case 'scatter':
          $sql = $this->scatter($equation, $dataset,  $filters);
        break;
        default: 

        break;
      }

      return $sql;
    }


    // Helper function to determine what datasets to pull from
    private function getDatasets($datasets) {
      $datasets_id = collect($datasets)->implode('id', ', ');
      $sql = " AND t1.datasets_id";

      return $sql .= ((count($datasets) === 1)) ?  " = $datasets_id" : " IN ($datasets_id)";
    }

    // Generate the Global Overlay for IDELA Total
    private function globalAverageScatter($equation, $minx, $maxx) 
    {
      $num = $equation->value;
      $codebooks = $equation->meta->pluck('value')->first();

      //dd($codebooks);

      $query = DatasetMeta::query();
      $query->selectRaw("t1.value as yaxis, t2.value as xaxis");
      $query->from('datasets_meta as t1');
      $query->join('datasets_meta as t2', function($q) {
        $q->on('t1.datasets_id', '=', 't2.datasets_id')
          ->on('t1.row', '=', 't2.row');
      });
      $query->where([
        ['t1.codebook_id', $codebooks],
        ['t2.codebook_id', $num],
        ['t2.value', '!=', 'header'],
        ['t1.value', '!=', 'header']
      ]);

      $results = $query->get();

      $x = [];
      $y = [];

      foreach($results as $result) {
        $x[] = floatval($result->xaxis);
        $y[] = floatval($result->yaxis);
        $xtimesy[] = floatval($result->xaxis) * floatval($result->yaxis);
        $xsquared[] = pow(floatval($result->xaxis), 2);
        $ysquared[] = pow(floatval($result->yaxis), 2);
      }


      //$minx = collect($x)->min(); // Minimum X - Axis Value
      //$maxx = collect($x)->max(); // Maximum X - Axis Value
      $sumx = collect($x)->sum(); // Sum of all X Axis Values
      $sumy = collect($y)->sum(); // Sum or all Y Axis Values
      $sumxtimesy = collect($xtimesy)->sum(); // The Sum of all X Axis * Y Axis Values
      $sumxsquared = collect($xsquared)->sum(); // The Sum of all X Axis values squared
      $sumysquared = collect($ysquared)->sum(); // The Sum of all Y Axis values squared
      $rows = $results->count(); // Total rows of data

      $intercept = (($sumy * $sumxsquared) - ($sumx * $sumxtimesy))/($rows * $sumxsquared - pow($sumx, 2));
      $slope = (($rows * $sumxtimesy) - ($sumx * $sumy))/($rows * $sumxsquared - pow($sumx, 2));

      // Need to get the min/max X point
      $stepsX = ($maxx - $minx) / 100;
      $linearX[] = $minx;
      for($i = 1; $i < 100; $i++) {
        $linearX[] = $minx + ($stepsX * $i);
      }
      $linearX[] = $maxx;

      $stepsY = ((($maxx * $slope) + $intercept) - (($minx * $slope) + $intercept)) / 100;

      $linearY[] = (($minx * $slope) + $intercept);
      for($i = 1; $i < 100; $i++) {
        $linearY[] = (($minx * $slope) + $intercept) + ($stepsY * $i);
      }
      $linearY[] = (($maxx * $slope) + $intercept);

      //dd($linearY);

      //$linearX = $//[$minx, $maxx];
      ///$linearY = //[(($minx * $slope) + $intercept), (($maxx * $slope) + $intercept)];

      $rnumerator = $rows * ($sumxtimesy) - ($sumx * $sumy);
      $rdenominatorleft = ($rows * $sumxsquared) - pow($sumx, 2);
      $rdenominatorright = ($rows * $sumysquared) - pow($sumy, 2);

      // This is the R-Squared Value
      $rValue = pow(($rnumerator / sqrt($rdenominatorleft * $rdenominatorright)), 2);

      // Generation of the P-Value
      $degreesOfFreedom = $rows - 2;
      $mean = $sumx / $rows;

      foreach($results as $result) {
        $predictive[] = pow($result->yaxis - (($result->xaxis * $slope) + $intercept), 2);
        $observed[] = pow($result->xaxis - $mean, 2);
      }

      
      // Generating the Standard Error
      $predictiveSum = collect($predictive)->sum();
      $predictiveWDoF = sqrt($predictiveSum / $degreesOfFreedom);
      $observedSum = collect($observed)->sum();
      $observedSqrt = sqrt($observedSum);
      $standardError = $predictiveWDoF / $observedSqrt;

      $tValue = $slope / $standardError;
      $pValue = $this->pValueTable($tValue, $degreesOfFreedom);


      return ['x' => $linearX, 'y' => $linearY, 'slope' => $slope, 'rValue' => $rValue, 'tValue' => $tValue, 'pValue' => $pValue];
    }

    private function scatter($equation, $datasets, $filters) 
    {

      $num = $equation->value;
      //$codebooks = $equation->meta->pluck('value')->implode(', ');

      $codebooks = $equation->meta->pluck('value')->first();
      $datasets = collect($datasets)->pluck('id');

      $query = DatasetMeta::query();
      $query->selectRaw("t1.value as yaxis, t2.value as xaxis, t1.row as rowData");
      $query->from('datasets_meta as t1');
      $query->join('datasets_meta as t2', function($q) {
        $q->on('t1.datasets_id', '=', 't2.datasets_id')
          ->on('t1.row', '=', 't2.row');
      });
      $query->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('t1.datasets_id', $datasets[0]);
      });
      $query->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('t1.datasets_id', $datasets);
      });
      $query->where([
        ['t1.codebook_id', $codebooks],
        ['t2.codebook_id', $num],
        ['t2.value', '!=', 'header'],
        ['t1.value', '!=', 'header']
      ]);

      

      // Gender should always be a 0 or 1
      // ID of 11 is Gender
      // ID of 297 is Child age in years
      if ($filters) {

        $query = $query->get();
        
        // Get the results of a filter with Gender Selection
        if (isset($filters['filterSelectionGender'])) {
          $genderResults = $this->filterGenders($query, $datasets, $filters['filterSelectionGender']);
          $query = $genderResults;
        }

        // Get the results of a filter with Age Selection
        if (isset($filters['filterSelectionAge'])) {
          $ageResults = $this->filterAges($query, $datasets, $filters['filterSelectionAge']);
          $query = $ageResults;
        }

        // Get the results of a filter with Location
        if (isset($filters['filterSelectionLocation'])) {
          $ageResults = $this->filterLocations($query, $datasets, $filters['filterSelectionLocation']);
          $query = $ageResults;
        }

        // Get the filter for a compareBy with Treatment
        if (isset($filters['treatment'])) {
          $treatmentResults = $this->filterTreatment($query, $datasets, $filters['treatment']);
          $query = $treatmentResults;
        }

        // Get the filter for a compareBy with Gender
        if (isset($filters['gender'])) {
          $genderCompareResults = $this->filterCompareByGenders($query, $datasets, $filters['gender']);
          $query = $genderCompareResults;
        }

        $results = $query;
       //$max = $query->count();
      } else {
        $results = $query->get();
      }

      $x = [];
      $y = [];

      foreach($results as $result) {
        $x[] = floatval($result->xaxis);
        $y[] = floatval($result->yaxis);
        $xtimesy[] = floatval($result->xaxis) * floatval($result->yaxis);
        $xsquared[] = pow(floatval($result->xaxis), 2);
        $ysquared[] = pow(floatval($result->yaxis), 2);
      }


      $minx = collect($x)->min(); // Minimum X - Axis Value
      $maxx = collect($x)->max(); // Maximum X - Axis Value
      $sumx = collect($x)->sum(); // Sum of all X Axis Values
      $sumy = collect($y)->sum(); // Sum or all Y Axis Values
      $sumxtimesy = collect($xtimesy)->sum(); // The Sum of all X Axis * Y Axis Values
      $sumxsquared = collect($xsquared)->sum(); // The Sum of all X Axis values squared
      $sumysquared = collect($ysquared)->sum(); // The Sum of all Y Axis values squared
      $rows = $results->count(); // Total rows of data

      $intercept = (($sumy * $sumxsquared) - ($sumx * $sumxtimesy))/($rows * $sumxsquared - pow($sumx, 2));
      $slope = (($rows * $sumxtimesy) - ($sumx * $sumy))/($rows * $sumxsquared - pow($sumx, 2));

      // Need to get the min/max X point
      //$linearX = [$minx, $maxx];
      //$linearY = [(($minx * $slope) + $intercept), (($maxx * $slope) + $intercept)];

      // Need to get the min/max X point
      $stepsX = ($maxx - $minx) / 100;
      $linearX[] = $minx;
      for($i = 1; $i < 100; $i++) {
        $linearX[] = $minx + ($stepsX * $i);
      }
      $linearX[] = $maxx;

      $stepsY = ((($maxx * $slope) + $intercept) - (($minx * $slope) + $intercept)) / 100;

      $linearY[] = (($minx * $slope) + $intercept);
      for($i = 1; $i < 100; $i++) {
        $linearY[] = (($minx * $slope) + $intercept) + ($stepsY * $i);
      }
      $linearY[] = (($maxx * $slope) + $intercept);



      $rnumerator = $rows * ($sumxtimesy) - ($sumx * $sumy);
      $rdenominatorleft = ($rows * $sumxsquared) - pow($sumx, 2);
      $rdenominatorright = ($rows * $sumysquared) - pow($sumy, 2);

      // This is the R-Squared Value
      $rValue = pow(($rnumerator / sqrt($rdenominatorleft * $rdenominatorright)), 2);

      // Generation of the P-Value
      $degreesOfFreedom = $rows - 2;
      $mean = $sumx / $rows;

      foreach($results as $result) {
        $predictive[] = pow($result->yaxis - (($result->xaxis * $slope) + $intercept), 2);
        $observed[] = pow($result->xaxis - $mean, 2);
      }

      
      // Generating the Standard Error
      $predictiveSum = collect($predictive)->sum();
      $predictiveWDoF = sqrt($predictiveSum / $degreesOfFreedom);
      $observedSum = collect($observed)->sum();
      $observedSqrt = sqrt($observedSum);
      $standardError = $predictiveWDoF / $observedSqrt;

      $tValue = $slope / $standardError;
      $pValue = $this->pValueTable($tValue, $degreesOfFreedom);


      $global = $this->globalAverageScatter($equation, $minx, $maxx);

      return [
        'x' => $x, 
        'y' => $y, 
        'linearX' => $linearX, 
        'linearY' => $linearY, 
        'rValue' => $rValue, 
        'pValue' => $pValue,
        'tValue' => $tValue,
        'slope' => $slope,
        'globalX' => $global['x'],
        'globalY' => $global['y'],
        'globalT' => $global['tValue'],
        'globalR' => $global['rValue'],
        'globalS' => $global['slope'],
        'globalP' => $global['pValue']
      ];

    }

    // Converted to Eloquent - Significantly Faster
    private function equationCount($equation, $datasets) 
    {
      $codebooks = $equation->meta->pluck('value')->first();
      $datasets = collect($datasets)->pluck('id');

      $query = DatasetMeta::query();
      $query->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('datasets_id', $datasets[0]);
      });
      $query->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('datasets_id', $datasets);
      });
      $query->where([
            ['codebook_id', $codebooks],
            ['value', $equation->value],
            ['value', '!=', 'header']
          ]);
      
      $results = $query->count();

      return $results;
    }

    // Converted to Eloquent - Significantly Faster
    private function equationDistribution($equation, $datasets, $filters)
    {
      $max = 0;
      $nums = explode(',', $equation->value);
      $codebooks = $equation->meta->pluck('value')->first();
      $datasets = collect($datasets)->pluck('id');

      foreach($datasets as $dataset) {
        $max += Dataset::find($dataset)->meta_rows_max; // Find a Way to Reduce This. Possibly in the Graph
      }

      $query = DatasetMeta::query();
      $query->selectRaw('t1.value, t1.row as rowData');
      $query->from('datasets_meta as t1');
      $query->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('datasets_id', $datasets[0]);
      });
      $query->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('datasets_id', $datasets);
      });
      $query->where([
        ['codebook_id', $codebooks],
        ['value', '>=', (float) $nums[0]],
        ['value', '<', (float) $nums[1]],
        ['value', '!=', 'header'],
      ]);

      if ($filters) {

        $query = $query->get();
        
        // Get the results of a filter with Gender Selection
        if (isset($filters['filterSelectionGender'])) {
          $genderResults = $this->filterGenders($query, $datasets, $filters['filterSelectionGender']);
          $query = $genderResults;
        }

        // Get the results of a filter with Age Selection
        if (isset($filters['filterSelectionAge'])) {
          $ageResults = $this->filterAges($query, $datasets, $filters['filterSelectionAge']);
          $query = $ageResults;
        }

        // Get the results of a filter with Location
        if (isset($filters['filterSelectionLocation'])) {
          $ageResults = $this->filterLocations($query, $datasets, $filters['filterSelectionLocation']);
          $query = $ageResults;
        }

        // Get the filter for a compareBy with Treatment
        if (isset($filters['treatment'])) {
          $treatmentResults = $this->filterTreatment($query, $datasets, $filters['treatment']);
          $query = $treatmentResults;
        }

        // Get the filter for a compareBy with Gender
        if (isset($filters['gender'])) {
          $genderCompareResults = $this->filterCompareByGenders($query, $datasets, $filters['gender']);
          $query = $genderCompareResults;
        }

        //$max = $query->count();
      }


      $results = $query->count();
      $qty = $results;
      if ($max != 0) {
        $results = $results/$max;
        $results = ($results <= 1) ? round($results * 100, 2) : round($results, 2);
      }

      // $r = str_replace(array('?'), array('\'%s\''), $query->toSql());
      // $r = vsprintf($r, $query->getBindings());

      return [
        'result' => $results,
        'qty' => $qty
      ];
    }

    // Converted to Eloquent - Significantly Faster
    private function equationPercent($equation, $datasets, $filters)
    {
      $max = 0;
      $num = $equation->value;
      $codebooks = $equation->meta->pluck('value')->first();
      $datasets = collect($datasets)->pluck('id');

      foreach($datasets as $dataset) {
        //var_dump($dataset);
        $max += Dataset::find($dataset)->meta_rows_max; // Find a Way to Reduce This. Possibly in the Graph
        //var_dump($max);
      }
      //dd();

      $query = DatasetMeta::query();
      $query->selectRaw('t1.value, t1.row as rowData');
      $query->from('datasets_meta as t1');
      $query->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('datasets_id', $datasets[0]);
      });
      $query->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('datasets_id', $datasets);
      });
      $query->where([
        ['codebook_id', $codebooks],
        ['value', '!=', 'header']
      ]);

      // Gender should always be a 0 or 1
      // ID of 11 is Gender
      // ID of 297 is Child age in years
      if ($filters) {

        $query = $query->get();
        
        // Get the results of a filter with Gender Selection
        if (isset($filters['filterSelectionGender'])) {
          $genderResults = $this->filterGenders($query, $datasets, $filters['filterSelectionGender']);
          $query = $genderResults;
        }

        // Get the results of a filter with Age Selection
        if (isset($filters['filterSelectionAge'])) {
          $ageResults = $this->filterAges($query, $datasets, $filters['filterSelectionAge']);
          $query = $ageResults;
        }

        // Get the results of a filter with Location
        if (isset($filters['filterSelectionLocation'])) {
          $locResults = $this->filterLocations($query, $datasets, $filters['filterSelectionLocation']);
          $query = $locResults;
        }

        //dd($query);

        // Get the filter for a compareBy with Treatment
        if (isset($filters['treatment'])) {
          $treatmentResults = $this->filterTreatment($query, $datasets, $filters['treatment']);
          $query = $treatmentResults;
        }

        // Get the filter for a compareBy with Gender
        if (isset($filters['gender'])) {
          $genderCompareResults = $this->filterCompareByGenders($query, $datasets, $filters['gender']);
          $query = $genderCompareResults;
        }

        

        $max = $query->count();
      }

      $results = $query->sum('value');

      //dd($results);

      if ($results != 0) {
        $results = (!$num) ? $results/$max : ($results/$num)/$max;
        $results = ($results <= 1) ? round($results * 100, 2) : round($results, 2);
      }

      return ['result' => $results, 'qty' => $max];
    }

    // Converted to Eloquent - Significantly Faster
    private function equationPercentGroupBy($equation, $datasets, $filters)
    {
      $nums = explode(',', $equation->value);
      $codebooks = $equation->meta->pluck('value')->first();
      $datasets = collect($datasets)->pluck('id');

      $query = DatasetMeta::query();
      //$query->selectRaw("(SUM(t1.value) / COUNT(t2.value)) AS result, COUNT(t2.value) as qty, t2.value AS grp");
      $query->selectRaw("t1.value AS result, t2.value as grp, t1.row as rowData");
      $query->from('datasets_meta as t1');
      $query->join('datasets_meta as t2', function($q) {
        $q->on('t1.datasets_id', '=', 't2.datasets_id')
          ->on('t1.row', '=', 't2.row');
      });
      $query->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('t1.datasets_id', $datasets[0]);
      });
      $query->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('t1.datasets_id', $datasets);
      });
      $query->where([
        ['t1.codebook_id', $codebooks],
        ['t2.codebook_id', $nums[0]],
        ['t2.value', $nums[1]],
        ['t1.value', '!=', 'header']
      ]);

      $query = $query->get();

      
      // Gender should always be a 0 or 1
      // ID of 11 is Gender
      // ID of 297 is Child age in years
      if ($filters) {

        //$query = $query->get();
        
        // Get the results of a filter with Gender Selection
        if (isset($filters['filterSelectionGender'])) {
          $genderResults = $this->filterGenders($query, $datasets, $filters['filterSelectionGender']);
          $query = $genderResults;
        }

        // Get the results of a filter with Age Selection
        if (isset($filters['filterSelectionAge'])) {
          $ageResults = $this->filterAges($query, $datasets, $filters['filterSelectionAge']);
          $query = $ageResults;
        }

        // Get the results of a filter with Location
        if (isset($filters['filterSelectionLocation'])) {
          $ageResults = $this->filterLocations($query, $datasets, $filters['filterSelectionLocation']);
          $query = $ageResults;
        }

        // Get the filter for a compareBy with Treatment
        if (isset($filters['treatment'])) {
          $treatmentResults = $this->filterTreatment($query, $datasets, $filters['treatment']);
          $query = $treatmentResults;
        }

        // Get the filter for a compareBy with Gender
        if (isset($filters['gender'])) {
          $genderCompareResults = $this->filterCompareByGenders($query, $datasets, $filters['gender']);
          $query = $genderCompareResults;
        }

        //$qty = $query->count();
      }

     //dd($query);


      $query = $query->map(function ($item) {
        return $item->only(['result']);
      })->flatten();

      if ($query->count() > 0) {
        $results = $query->sum() / $query->count();
      } else {
        $results = 0;
      }

      // $results = $query->groupBy('grp')->sortBy(function($item, $key) {
      //   return $item['grp'];
      // })->skipWhile(function ($item) {
      //   return $item['grp'] == null;
      // });

      //dd($results);

      // foreach($results as $result) {
      //   $result->result = ($result->result  <= 1) ? round($result->result  * 100, 2) : round($result->result , 2);
      // }

      $result = ($results  <= 1) ? round($results  * 100, 2) : round($results , 2);
      
      $results = [
        'result' => $result,
        'qty' => $query->count()
      ];

      //dd($results);

      return $results;
    }

    // Converted to Eloquent - Significantly Faster
    private function avgGroupBy($equation, $datasets, $filters) 
    {
      $codebooks = $equation->meta->pluck('value')->first();
      $datasets = collect($datasets)->pluck('id');

      $query = DatasetMeta::query();
      //$query->selectRaw("(SUM(t1.value) / COUNT(t2.value)) AS result, COUNT(t2.value) as qty, t2.value AS grp");
      $query->selectRaw("t1.value AS result, t1.row as rowData, t2.value AS grp");
      $query->from('datasets_meta as t1');
      $query->join('datasets_meta as t2', function($q) {
        $q->on('t1.datasets_id', '=', 't2.datasets_id')
          ->on('t1.row', '=', 't2.row');
      });
      $query->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('t1.datasets_id', $datasets[0]);
      });
      $query->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('t1.datasets_id', $datasets);
      });
      $query->where([
        ['t1.codebook_id', $codebooks],
        ['t2.codebook_id', $equation->value],
        ['t1.value', '!=', 'header'],
      ]);

      $query = $query->get();
      // Gender should always be a 0 or 1
      // ID of 11 is Gender
      // ID of 297 is Child age in years

      if ($filters) {

        //$query = $query->groupBy('t2.value')->get();

        //dd($query);

        
        // Get the results of a filter with Gender Selection
        if (isset($filters['filterSelectionGender'])) {
          $genderResults = $this->filterGenders($query, $datasets, $filters['filterSelectionGender']);
          $query = $genderResults;
        }

        // Get the results of a filter with Age Selection
        if (isset($filters['filterSelectionAge'])) {
          $ageResults = $this->filterAges($query, $datasets, $filters['filterSelectionAge']);
          $query = $ageResults;
        }

        // Get the results of a filter with Location
        if (isset($filters['filterSelectionLocation'])) {
          $ageResults = $this->filterLocations($query, $datasets, $filters['filterSelectionLocation']);
          $query = $ageResults;
        }

        // Get the filter for a compareBy with Treatment
        if (isset($filters['treatment'])) {
          $treatmentResults = $this->filterTreatment($query, $datasets, $filters['treatment']);
          $query = $treatmentResults;
        }

        // Get the filter for a compareBy with Gender
        if (isset($filters['gender'])) {
          $genderCompareResults = $this->filterCompareByGenders($query, $datasets, $filters['gender']);
          $query = $genderCompareResults;
        }

       
       //$max = $query->count();
      }

      $count = $query->count();
      
      //$results = $query->groupBy('grp')->get();

      $query = $query->map(function ($item) {
        return $item->only(['result', 'grp']);
      })->groupBy('grp');


      foreach($query as $key => $q) {
        $results[] = (object) [
          'result' => $q->sum('result') / $q->count(),
          'qty' => $q->count(),
          'grp' => $key
        ];
      }


      // dd($query->count());

      // $results = $query->groupBy('t2.value')->get()->sortBy(function($item, $key) {
      //   return $item['grp'];
      // })->skipWhile(function ($item) {
      //   return $item['grp'] == null;
      // });

      //dd(collect($results));
      

      foreach($results as $result) {
        $result->result = ($result->result  <= 1) ? round($result->result  * 100, 2) : round($result->result , 2);
      }

      return $results;
    }

    // Converted to Eloquent - Significantly Faster
    private function avgGroupByTot($equation, $datasets) 
    {
      $max = 0;
      $codebooks = $equation->meta->pluck('value')->first();
      $datasets = collect($datasets)->pluck('id');

      foreach($datasets as $dataset) {
        $max += Dataset::find($dataset)->meta_rows_max; // Find a Way to Reduce This. Possibly in the Graph
      }

      $query = DatasetMeta::query();
      $query->selectRaw("(COUNT(t1.value) / $max) AS result, COUNT(t2.value) as qty, t2.value AS grp");
      $query->from('datasets_meta as t1');
      $query->join('datasets_meta as t2', function($q) {
        $q->on('t1.datasets_id', '=', 't2.datasets_id')
          ->on('t1.row', '=', 't2.row');
      });
      $query->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('t1.datasets_id', $datasets[0]);
      });
      $query->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('t1.datasets_id', $datasets);
      });
      $query->where([
        ['t1.codebook_id', $codebooks],
        ['t2.codebook_id', $equation->value],
        ['t1.value', '!=', 'header']
      ]);
      $results = $query->groupBy('t2.value')->get()->sortBy(function($item, $key) {
        return $item['grp'];
      })->skipWhile(function ($item) {
        return $item['grp'] == null;
      });

      foreach($results as $result) {
        $result->result = ($result->result  <= 1) ? round($result->result  * 100, 2) : round($result->result , 2);
      }

      return $results;
    }

    private function cntGroupBy($equation, $dataset) 
    {
      $max = $dataset->meta_rows_max;
      
      $codebooks = $equation->meta->pluck('value')->implode(', ');

      $sql = "SELECT COUNT(t1.value) AS result, t2.value AS grp";
      $sql .= " FROM datasets_meta AS t1";
      $sql .= " JOIN datasets_meta AS t2 ON t1.datasets_id = t2.datasets_id AND t1.row = t2.row";
      $sql .= " WHERE t1.datasets_id = $dataset->id";
      $sql .= " AND t1.codebook_id = $codebooks";
      $sql .= " AND t2.codebook_id = $equation->value";
      $sql .= " AND t1.value != 'header'";
      $sql .= " GROUP BY t2.value";

      $results = DB::select($sql);

      return $results;

    }


    //Filtering equtions
    private function filterGenders($query, $datasets, $genders) 
    {
      $gQuery = DatasetMeta::query();
      $gQuery->select('row');
      $gQuery->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('datasets_id', $datasets[0]);
      });
      $gQuery->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('datasets_id', $datasets);
      });

      $i = 0;
      foreach($genders as $gender) {
        if ($i == 0) {
          $gQuery->where([
            ['codebook_id', 11],
            ['value', '!=', 'header'],
            ['value', $gender]
          ]);
        } else {
          $gQuery->orWhere([
            ['datasets_id', $datasets[0]],
            ['codebook_id', 11],
            ['value', '!=', 'header'],
            ['value', $gender]
          ]);
        };
        $i++;
      }
        
      //$results = $gQuery->get();
      //$intersect = $query->intersectByKeys($results);
      $results = $gQuery->pluck('row')->toArray();
      $intersect = $query->whereIn('rowData', $results);

      return $intersect;
    }

    private function filterAges($query, $datasets, $ages)
    {
      $ageQuery = DatasetMeta::query();
      $ageQuery->select('row');
      $ageQuery->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('datasets_id', $datasets[0]);
      });
      $ageQuery->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('datasets_id', $datasets);
      });

      $i = 0;
      foreach($ages as $age) {
        if ($i == 0) {
          $ageQuery->where([
            ['codebook_id', 297],
            ['value', '!=', 'header'],
            ['value', $age]
          ]);
        } else {
          $ageQuery->orWhere([
            ['datasets_id', $datasets[0]],
            ['codebook_id', 297],
            ['value', '!=', 'header'],
            ['value', $age]
          ]);
        };
        $i++;
      }
      
      // $results = $ageQuery->get();
      // $intersect = $query->intersectByKeys($results);
      $results = $ageQuery->pluck('row')->toArray();
      $intersect = $query->whereIn('rowData', $results);
    
      return $intersect;
    }

    private function filterLocations($query, $datasets, $locations)
    {
      $locationQuery = DatasetMeta::query();
      $locationQuery->select('row');
      $locationQuery->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('datasets_id', $datasets[0]);
      });
      $locationQuery->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('datasets_id', $datasets);
      });

      $i = 0;
      foreach($locations as $location) {
        if ($i == 0) {
          $locationQuery->where([
            ['codebook_id', 454],
            ['value', '!=', 'header'],
            ['value', $location]
          ]);
        } else {
          $locationQuery->orWhere([
            ['datasets_id', $datasets[0]],
            ['codebook_id', 454],
            ['value', '!=', 'header'],
            ['value', $location]
          ]);
        };
        $i++;
      }
      
      // $results = $locationQuery->get();
      // $intersect = $query->intersectByKeys($results);
      $results = $locationQuery->pluck('row')->toArray();

      //dd($results);

      $intersect = $query->whereIn('rowData', $results);

      //dd($intersect);
    
      return $intersect;
    }

    private function filterTreatment($query, $datasets, $treatment)
    {
      $treatmentQuery = DatasetMeta::query();
      $treatmentQuery->select('row');
      $treatmentQuery->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('datasets_id', $datasets[0]);
      });
      $treatmentQuery->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('datasets_id', $datasets);
      });

      $treatmentQuery->where([
        ['codebook_id', 422],
        ['value', '!=', 'header'],
        ['value', $treatment]
      ]);
      
      // $results = $treatmentQuery->get();
      // $intersect = $query->intersectByKeys($results);
      $results = $treatmentQuery->pluck('row')->toArray();
        
      //dd($results);

      $intersect = $query->whereIn('rowData', $results);
    
      return $intersect;
    }

    private function filterCompareByGenders($query, $datasets, $gender)
    {
      $genderQuery = DatasetMeta::query();
      $genderQuery->select('row');
      $genderQuery->when((count($datasets) === 1), function($q) use ($datasets) {
        return $q->where('datasets_id', $datasets[0]);
      });
      $genderQuery->when((count($datasets) > 1), function($q) use ($datasets) {
        return $q->whereIn('datasets_id', $datasets);
      });

      $genderQuery->where([
        ['codebook_id', 11],
        ['value', '!=', 'header'],
        ['value', $gender]
      ]);
      
      // $results = $genderQuery->get();
      // $intersect = $query->intersectByKeys($results);
      $results = $genderQuery->pluck('row')->toArray();
      $intersect = $query->whereIn('rowData', $results);
    
      return $intersect;
    }

    private function pValueTable($tValue, $df) 
    {

      $row = 0;
      $values = [.1, .05, .01, .001];

      $table = [
        [1.6641,	1.9901,	2.6387,	3.4164], // 80
        [1.6602,	1.9840,	2.6259,	3.3905], // 100
        [1.6577,	1.9799,	2.6174,	3.3735], // 120
        [1.6558,	1.9771,	2.6114,	3.3614], // 140
        [1.6525,	1.9719,	2.6007,	3.3398]  // 200
      ];

      // Determine the looping row
      if ($df > 100) {
        $row = 1;
        if ($df > 120) {
          $row = 2;
          if ($df > 140) {
            $row = 3;
            if ($df > 200) {
              $row = 4;
            }
          }
        }
      }

      $col = 0;
      foreach($table[$row] as $key => $val) {
        
        //var_dump((float)($tValue));
        //var_dump((float)($val));
        if ((float)$tValue > (float)$val) {
          $col = $key;
        }
      }

      return $values[$col];

    }
}
