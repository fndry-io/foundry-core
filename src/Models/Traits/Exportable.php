<?php


namespace Foundry\Core\Models\Traits;


use Illuminate\Database\Eloquent\Builder;

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
                    $row[] = object_get($item, $key);
                }
                fputcsv($fh, $row);
            }
            $page++;
            $result = $query->simplePaginate($perPage, null, 'export', $page);
        }

        fclose($fh);

        return $file;

    }
}
