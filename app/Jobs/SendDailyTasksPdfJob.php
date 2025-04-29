<?php

namespace App\Jobs;

use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDailyTasksPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tasks = $this->user->tasks()
            ->where('status', '!=', 'completed')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getDefaultRowDimension()->setRowHeight(15);

        $this->fillDataWithValidation($sheet, $tasks);

        $pdfContent = $this->generateSafePdf($spreadsheet);

        $this->sendEmailWithPdf($pdfContent);
    }

    private function fillDataWithValidation($sheet, $tasks): void
    {
        try {
            $sheet->mergeCells('A1:C1');
            $sheet->setCellValue('A1', 'Ежедневный отчет по задачам')
                ->getStyle('A1')
                ->getFont()
                ->setBold(true)
                ->setSize(16);

            $sheet->setCellValue('A6', 'ФИО:');
            $sheet->setCellValue('B6', $this->user->name);
            $sheet->setCellValue('A7', 'Email:');
            $sheet->setCellValue('B7', $this->user->email);
            $sheet->setCellValue('A8', 'Дата выгрузки:');
            $sheet->setCellValue('B8', now()->format('d.m.Y H:i'));

            $sheet->getParent()->getDefaultStyle()->applyFromArray([
                'font' => [
                    'name' => 'DejaVu Sans',
                    'size' => 12,
                ],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ]
            ]);

            $sheet->fromArray(['Название', 'Статус', 'Дата создания'], null, 'A1');

            $row = 2;
            foreach ($tasks as $task) {
                $sheet->setCellValue("A{$row}", $task->name ?? '');
                $sheet->setCellValue("B{$row}", $task->status->label() ?? '');
                $sheet->setCellValue("C{$row}", $task->created_at?->format('d.m.Y H:i') ?? '');
                $row++;
            }

            if ($row > 2) {
                $sheet->getStyle('A1:C1')->getFont()->setBold(true);
                foreach (range('A', 'C') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        } catch (\Exception $e) {
            logger()->error('Ошибка заполнения данных: '.$e->getMessage());
            throw $e;
        }
    }

    private function generateSafePdf($spreadsheet): string
    {
        try {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf($spreadsheet);
            $writer->setFont('dejavu sans');

            ob_start();
            $writer->save('php://output');
            return ob_get_clean();
        } catch (\Exception $e) {
            logger()->error('Ошибка генерации PDF: '.$e->getMessage());
            throw $e;
        }
    }

    private function sendEmailWithPdf($pdfContent): void
    {
        try {
            Mail::send('emails.daily_tasks', ['user' => $this->user], function ($message) use ($pdfContent) {
                $message->to($this->user->email)
                    ->subject('Отчет по задачам')
                    ->attachData($pdfContent, 'tasks_report.pdf', [
                        'mime' => 'application/pdf'
                    ]);
            });
        } catch (\Exception $e) {
            logger()->error('Ошибка отправки письма: ' . $e->getMessage());
            throw $e;
        }
    }
}
