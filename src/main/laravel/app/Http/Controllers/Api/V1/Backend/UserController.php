<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Jobs\SendPasswordResetEmail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roles = $request->roles ?? [];

        $roles = empty($roles) ? [User::ADMIN, User::DOCTOR] : $roles;

        $users = User::query()
            ->whereIn('role', $roles)
            ->search($request->search)
            ->paginate();

        return success(UserResource::collection($users));
    }

    public function store(UserRequest $request)
    {
        $attribtues = $request->validated();

        $attribtues['password'] = $attribtues['password'] ?? bcrypt(Str::random(10));

        $user = User::create($attribtues);

        $body = 'You are receiving this email because an account is created with your mail for rkare, please change your password to something strong';
        $subject = 'Account created notification';
        SendPasswordResetEmail::dispatch($user, $subject, $body);

        return success($user);
    }

    public function show($id)
    {
        $user = User::boUser()->findOrFail($id);

        return success($user);
    }

    public function update($id, UserRequest $request)
    {
        $user = User::boUser()->findOrFail($id);

        $attribtues = $request->validated();

        if ($attribtues['password'] ?? '') {
            $attribtues['password'] = bcrypt($attribtues['password']);
        } else {
            unset($attribtues['password']);
        }

        $user->update($attribtues);

        return success($user);
    }

    public function me()
    {
        return success(['user' => auth()->user()]);
    }
}
