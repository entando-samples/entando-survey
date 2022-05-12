<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Models\Pathology;

class DocumentFilterListingController extends Controller
{
    public function __invoke()
    {
        return success([
            'pathologies' => Pathology::get(['id', 'title', 'slug']),
        ]);
    }
}
