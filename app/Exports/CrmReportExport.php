<?php

namespace App\Exports;

use App\Http\Controllers\CrmController;
use Maatwebsite\Excel\Concerns\FromArray;

class CrmReportExport implements FromArray
{
    private $rows;
    private $type;
    private $columns;
    private $title;

    public function __construct($rows, $type, $columns, $title)
    {
        $this->rows = $rows;
        $this->type = $type;
        $this->columns = array_merge(['SL'], $columns);
        $this->title = $title;
    }

    public function array(): array
    {
        $controller = app(CrmController::class);
        $result = [[$this->title], ['Generated at', now()->format('Y-m-d H:i:s')], $this->columns];
        foreach ($this->rows as $index => $row) $result[] = array_merge([$index + 1], $controller->rowValues($row, $this->type));
        return $result;
    }
}
