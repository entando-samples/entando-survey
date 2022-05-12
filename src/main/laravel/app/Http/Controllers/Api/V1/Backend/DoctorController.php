<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    function __invoke(Request $request)
    {
        return success(
            User::doctorRole()
                ->search($request->search)
                ->get()
        );
    }
}
