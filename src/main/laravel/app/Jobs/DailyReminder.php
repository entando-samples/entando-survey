<?php

namespace App\Jobs;

use App\Mail\DailyReminderMail;
use App\Models\Message;
use App\Models\PatientDocument;
use App\Models\User;
use App\Services\SurveyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DailyReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $surveyService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->surveyService = new SurveyService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $doctors = User::query()->doctorRole()->get();
        $totalUnreadMessage = Message::query()->unread()->count();
        $totalUnreadDocument = PatientDocument::query()->unreadDocuments()->count();

        foreach ($doctors as $doctor) {
            if ($doctor->email_report)
                Mail::to($doctor->email)
                    ->send(
                        new DailyReminderMail(
                            $doctor,
                            $this->surveyService->totalAlertableSurvey(),
                            $totalUnreadMessage,
                            $totalUnreadDocument,
                            $this->surveyService->incompletedSurvey(),  // totally incompleted survey count
                            $this->surveyService->partiallyCompletedSurvey(),  //partially completed survey
                        )
                    );
        }
    }
}
