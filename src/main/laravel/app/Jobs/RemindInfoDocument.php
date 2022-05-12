<?php

namespace App\Jobs;

use App\Firebase\PushNotification;
use App\Models\Document;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemindInfoDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $document;
    protected $notificationTitle;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Document $document, $notificationTitle = null)
    {
        $this->user = $user;
        $this->document = $document;
        $this->notificationTitle = $notificationTitle ?? 'Document reminder';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        PushNotification::make(
            'remind-document',
            $this->notificationTitle,
            $this->document->title
        )
            ->forNotifiable($this->user)
            ->withData([
                'document_id' => $this->document->id,
            ])
            ->send();
    }
}
