<?php

namespace App\Jobs;

use App\Exports\TaskExport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendTaskExportEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $directory = 'exports';
        $fileName = "tasks_export_{$this->user->id}_" . now()->format('Ymd_His') . '.xlsx';

        $filePath = storage_path("app/{$directory}/{$fileName}");

        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $export = new TaskExport($this->user->tasks);
        $fileCreated = $export->saveToFile($filePath);

        if (!$fileCreated) {
            throw new \RuntimeException("Не удалось создать файл по пути: " . $filePath);
        }

        Mail::send('emails.task_export', ['user' => $this->user], function ($message) use ($filePath, $fileName) {
            $message->to($this->user->email)
                ->subject('Выгрузка задач')
                ->attach($filePath, [
                    'as' => $fileName,
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ]);
        });

    }
}
