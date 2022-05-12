<?php

namespace App\Jobs;

use App\Mail\AlertSurveyMail;
use App\Models\Survey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AlertableSurveyReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $doctor, $survey, $patient;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($doctor, $patient, $surveyId)
    {
        $this->doctor = $doctor;
        $this->survey = Survey::find($surveyId);
        $this->patient = $patient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->doctor)
            Mail::to($this->doctor->email)->send(new AlertSurveyMail($this->survey, $this->patient));
    }
}
