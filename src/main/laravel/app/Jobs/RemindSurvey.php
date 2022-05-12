<?php

namespace App\Jobs;

use App\Firebase\PushNotification;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemindSurvey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $survey;
    protected $notificationTitle;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Survey $survey, $notificationTitle = null)
    {
        $this->user = $user;
        $this->survey = $survey;
        $this->notificationTitle = $notificationTitle ?? 'Survey reminder';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        PushNotification::make(
            'remind-survey',
            $this->notificationTitle,
            $this->survey->title
        )
            ->forNotifiable($this->user)
            ->withData([
                'survey_id' => $this->survey->id,
            ])
            ->send();
    }
}
