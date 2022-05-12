<?php

namespace App\Http\Controllers\Api\V1\Backend\Patients;

use App\Http\Controllers\Controller;
use App\Models\PatientDocument;
use App\Models\PatientFolder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index($id)
    {

        return success(PatientFolder::where('patient_id', $id)->with('documents')->get());
    }

    public function markAsRead(PatientDocument $document)
    {
        if (!$document->is_read) {
            $document->update([
                'read_at' => now()
            ]);
        }

        return success($document, "Document marked as read");
    }

    public function download($id)
    {
        $data = PatientDocument::findOrFail($id);

        // !TODO: clean and extract this mess to suitable place
        return response()->streamDownload(function () use ($data) {

            $options = new \ZipStream\Option\Archive();

            $options->setContentType('application/octet-stream');

            $zip = new \ZipStream\Zipstream("documents.zip", $options);

            foreach ($data->attachments as $url) {
                $path = 'uploads/' . basename($url);

                try {
                    $file = Storage::readStream($path);

                    $zip->addFileFromStream("attachments/" . basename($url), $file);
                } catch (\Exception $e) {
                    Log::error("unable to read the file at storage path: $path and output to zip stream. Exception is " . $e->getMessage());
                }
            }

            if ($data->voice_message) {
                $path = 'uploads/' . basename($data->voice_message);

                try {
                    $file = Storage::readStream($path);

                    $zip->addFileFromStream("voice-message/" . basename($data->voice_message), $file);
                } catch (\Exception $e) {
                    Log::error("unable to read the file at storage path: $path and output to zip stream. Exception is " . $e->getMessage());
                }
            }


            $zip->finish();
        }, 'documents.zip');
    }
}
