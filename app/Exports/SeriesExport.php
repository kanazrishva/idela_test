<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;


class SeriesExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{

  protected $rows;
  protected $title;

  public function __construct(array $rows, $title) {
    //dd($rows);
    $this->rows = $rows;
    $this->title = $title;
  }

  public function array(): array {
    //dd($this->rows);
    return $this->rows;
  }

  // public function map($row): array
  // { 
  //   return [
  //       $row,
  //   ];
  // }

  public function headings(): array 
  {
    return [
      'XAxis',
      'YAxis'
    ];
  }

  public function title(): string
  {
      return $this->title;
  }

}