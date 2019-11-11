<?php


namespace Foundry\Core\Models\Traits;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait Exportable
{
    /**
     * Exports the records to a CSV file
     *
     * @param $columns
     * @param Builder $query
     * @return false|string
     */
    static function export($columns, Builder $query)
    {
        //ini_set('max_execution_time', 600);
        $file = tempnam(storage_path('temp'), 'export-');
        $fh = fopen($file, 'w+');

        $perPage = 500;
        $page = 1;

        $result = $query->simplePaginate($perPage, null, 'export', $page);

        $headers = array_values($columns);
        fputcsv($fh, $headers);

        $fields = array_keys($columns);
        while($items = $result->items()) {
            foreach ($items as $item) {
                $row = [];
                foreach ($fields as $key) {
                    $value = object_get($item, $key);
                    if ($value instanceof Arrayable) {
                        $value = implode(";", $value->toArray());
                    } elseif (is_array($value)) {
                        $value = implode(";", $value);
                    }
                    $row[] = $value;
                }
                try {
                    fputcsv($fh, $row);
                } catch(\Throwable $e) {
                    Log::error($e->getMessage(), $e->getTrace());
                }
            }
            $page++;
            $result = $query->simplePaginate($perPage, null, 'export', $page);
        }

        fclose($fh);

        return $file;

    }
}
