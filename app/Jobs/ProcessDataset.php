<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Dataset;
use App\DatasetMeta;
use App\Imports\DatasetImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Events\DatasetEvent;
use Illuminate\Support\Facades\Log;

class ProcessDataset implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $request;
    protected $dataset;

    public function __construct(Dataset $dataset, $request)
    {
        //
      $this->dataset = $dataset;
      $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        $headers = $this->request['excel']; //Request::get('excel');
        $dataset = $this->dataset;
        $data = (new DatasetImport)->toCollection($dataset->dataset);
        $data = $data[0];

        $insert = [];
        $codebook = [];

        // Add a headers row to the meta table
        foreach ($headers as $header) {
          $row = [];
          $row['datasets_id'] = $dataset->id;
          $row['column_name'] = $header['header'];
          $row['value'] = 'header';
          $row['codebook_id'] = null;
          $row['row'] = 0;

          if ($header['codebook'] !== null) {
            $row['codebook_id'] = $header['codebook']['id'];
            $codebook[] = $row;
          }

          $insert[] = $row;
        }

        $i = 1;
        foreach($data as $item) {
          foreach ($item as $key => $value) {
            $row = [];
            $row['datasets_id'] = $dataset->id;
            
            // Match the key of the row item to the orig_header of the headers array to set the right column name
            foreach ($headers as $header) {
              if ($header['orig_header'] == $key) {
                $row['column_name'] = $header['header'];
              }
            }

            //$row['column_name'] = $key;
            
            // Clean and test the value length
            $value = (strlen(trim($value)) > 200) ? substr($value, 0, 200) : $value;

            $row['value'] = $value;
            $row['row'] = $i;
            $row['codebook_id'] = null;

            foreach($codebook as $code) {
              if ($key === $code['column_name']) {
                $row['codebook_id'] = $code['codebook_id'];
              }
            }
            $insert[] = $row;
          }
          $i++;
        }

        //$inserts = collect($insert)->chunk(500);

        //Log::info('inserts array is created');


        \DB::disableQueryLog();
        //Log::info('query log is disabled');

        //Log::info($insert);
        
        //DatasetMeta::insert($insert);

        //Log::info('all rows entered at once');

        $chunks = collect($insert)->chunk(1000);

        foreach ($chunks as $chunk)
        {
          \DB::table('datasets_meta')->insert($chunk->toArray());
        }

        //Log::info('completed inserts');

      //  foreach(array_chunk($insert, 1000) as $meta) {
      //     //DatasetMeta::insert($meta->toArray());
      //   }

        $dataset->total_rows = ($i - 1);
        $dataset->status = 'imported';
        $dataset->save();

        event(new DatasetEvent($dataset));

        return;
    }

    public function failed()
    { 

      $this->dataset->update([
        'status' => 'failed'
      ]);

      event(new DatasetEvent($this->dataset));
      return;

    }
}
