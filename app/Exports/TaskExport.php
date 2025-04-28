<?php

namespace App\Exports;

use App\Models\Task;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Collection;

class TaskExport
{
    protected Collection $tasks;

    public function __construct(Collection $tasks)
    {
        $this->tasks = $tasks;
    }

    public function saveToFile(string $filePath): bool
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ID', 'Name', 'Status', 'Дата создания'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($this->tasks as $task) {
            $sheet->setCellValue('A' . $row, $task->id);
            $sheet->setCellValue('B' . $row, $task->name);
            $sheet->setCellValue('C' . $row, $task->status->label());
            $sheet->setCellValue('D' . $row, $task->created_at->format('Y-m-d H:i:s'));
            $row++;
        }

        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return file_exists($filePath);
    }
}
