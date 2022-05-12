<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\PatientAnswer;
use App\Models\PatientDocument;
use App\Models\Survey;
use App\Services\SurveyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DailyReminderMail extends Mailable
{
    use Queueable, SerializesModels;
    public $surveyService;
    public $doctor;
    protected
        $alertableSurveyTotal,
        $totalUnreadMessage,
        $totalUnreadDocument,
        $totalIncompleSurvey,
        $partialSurveyCount;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $doctor,
        $alertableSurveyTotal,
        $totalUnreadMessage,
        $totalUnreadDocument,
        $totalIncompleSurvey,
        $partialSurveyCount
    ) {
        $this->doctor = $doctor;
        $this->alertableSurveyTotal = $alertableSurveyTotal;
        $this->totalUnreadMessage = $totalUnreadMessage;
        $this->totalUnreadDocument = $totalUnreadDocument;
        $this->totalIncompleSurvey = $totalIncompleSurvey;
        $this->partialSurveyCount = $partialSurveyCount;
        $this->surveyService = new SurveyService();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $allPatientId = $this->doctor->patients()->pluck('id');
        $doctorUnreadMessage = Message::unread()->whereHas('sender')
            ->where([
                'receiver_id' => $this->doctor->id,
            ])->count();

        //unread docuement
        $doctorUnreadDocument = PatientDocument::query()->unreadDocuments()
            ->whereHas('folder', function ($query) {
                $query->whereHas('patient', function ($q) {
                    $q->whereHas('medicalData', function ($p) {
                        $p->where('doctor_id', $this->doctor->id);
                    });
                });
            })->count();

        $mineIncompletedSurvey = Survey::query()->WhereHas('patients', function ($query) {
            $query->whereNull('completed_at')->whereHas('medicalData', function ($q) {
                $q->where('doctor_id', $this->doctor->id);
            });
        })->whereDoesntHave('patientAnswers')->count();


        $minePartialSurveyCount = Survey::query()->whereHas('patients', function ($query) {
            $query->whereNull('completed_at')->whereHas('medicalData', function ($q) {
                $q->where('doctor_id', $this->doctor->id);
            });
        })->whereHas('patientAnswers')->count();

        //alertable survey
        $alertableAnswers = DB::table('survey_warning_ans')->select('answer_id')->pluck('answer_id');
        $mineAlertableSurvey = PatientAnswer::whereIn('answer_id', $alertableAnswers)
            ->distinct(['patient_answers.survey_id', 'patient_answers.patient_id'])
            ->join('patient_survey', function ($join) {
                $join->on('patient_answers.survey_id', '=', 'patient_survey.survey_id');
                $join->on('patient_answers.patient_id', '=', 'patient_survey.patient_id');
            })
            ->join('medical_data', function ($join) {
                $join->on('medical_data.patient_id', '=', 'patient_answers.patient_id');
            })
            ->where('medical_data.doctor_id', $this->doctor->id)
            ->whereNull('patient_survey.reviewed_at')
            ->count();

        return $this->markdown('mail.daily-reminder-mail')
            ->subject(config('app.name') . ' Daily Reminder')
            ->with([
                'messages' => [
                    'totalUnreadMessage' => $this->totalUnreadMessage,
                    'doctorUnreadMessage' => $doctorUnreadMessage
                ],
                'documents' => [
                    'totalUnreadDocument' => $this->totalUnreadDocument,
                    'doctorUnreadDocument' => $doctorUnreadDocument,
                ],
                'survey' => [
                    'totallyIncompleted' => [
                        'total' => $this->totalIncompleSurvey,
                        'doctors' => $mineIncompletedSurvey,
                    ],
                    'partially' => [
                        'total' => $this->partialSurveyCount,
                        'doctors' => $minePartialSurveyCount
                    ],
                    'alertable' => [
                        'total' => $this->alertableSurveyTotal,
                        'doctors' => $mineAlertableSurvey
                    ]
                ]
            ]);
    }
}
