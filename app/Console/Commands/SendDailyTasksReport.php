<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SendDailyTasksReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily tasks report to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::whereHas('tasks', function($query) {
            $query->where('status', '!=', 'completed');
        })->chunk(200, function ($users) {
            foreach ($users as $user) {
                dispatch(new \App\Jobs\SendDailyTasksPdfJob($user));
            }
        });

        $this->info('PDF reports have been queued!');
    }
}
