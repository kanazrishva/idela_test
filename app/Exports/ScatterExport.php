<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ScatterExport implements FromArray, WithMultipleSheets
{

  protected $sheets;

  public function __construct(array $sheets) {
    $this->sheets = $sheets;
  }

  public function array(): array {
    return $this->sheets;
  }

  public function sheets(): array {
    $sheets = [];

    //dd($this->sheets);

    $seriesOfData = $this->sheets;

    foreach($seriesOfData as $sod) {

      $data = $sod['data'];

      $i = 0;
      foreach($data as $key => $sheet) {
        // Need to Transform the Data Here.
        $arr = [];
  
        $xVariable = collect($data[0]['x']);
        $yVariable = collect($data[0]['y']);
  
        foreach($xVariable as $key => $value) {
          $arr[] = ['x' => $value, 'y' => $yVariable[$key]];
        }
  
        $title = $sod['labels'][$i];
  
        $sheets[] = new SeriesExport($arr, $title);
        $i++;
      }
    }

    return $sheets;
  }

 
}