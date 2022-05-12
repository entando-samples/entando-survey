<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class AlertSurveyMail extends Mailable
{
    use Queueable, SerializesModels;
    public $survey, $patient;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($survey, $patient)
    {
        $this->survey = $survey;
        $this->patient = $patient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url =  url("/patients/{$this->patient->id}/surveys/{$this->survey->id}");
        return $this->markdown('mail.alert-survey-mail')
            ->subject('Alert answer survey by ' . Str::limit($this->patient->name, 5, '...'))
            ->with([
                'url' => $url,
            ]);
    }
}
