<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\DocumentPatientPivot;
use App\Models\Message;
use App\Models\PatientSurveyPivot;

class ConfigController extends Controller
{
    public function myConfigs()
    {
        return success([
            "notification_id" => auth()->user()->getNotifiableId(),
            'unread_messages' => $this->unreadMessagesCount(),
            'unread_patient_documents' => DocumentPatientPivot::where([
                'patient_id' => auth()->id(),
                'is_read' => false
            ])->count(),
            'incomplete_survyes' => PatientSurveyPivot::where([
                'patient_id' => auth()->id(),
                'completed_at' => null
            ])->count(),
        ]);
    }

    protected function unreadMessagesCount()
    {
        return  Message::query()
            ->withoutGlobalScope('not-archived')
            ->without('topic')
            ->where(
                fn ($q) => $q->where('receiver_id', auth()->id())
                    ->where('read_at', null)
            )
            ->orWhere(
                fn ($q) => $q->where('sender_id', auth()->id())
                    ->whereHas('reply', fn ($q) => $q->whereNull('read_at'))
            )
            ->count();
    }
}
