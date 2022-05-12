<?php

use Illuminate\Support\Arr;

$permissions = [
    ['user.create', 'user.update', 'user.delete', 'user.read'],
    ['patient.read'],
    ['pathology:create', 'pathology:read', 'pathology:update', 'pathology:delete'],
    ['document.create', 'document.read', 'document.update', 'document.delete'],
    ['question.create', 'question.read', 'question.update', 'question.delete'],
    ['survey.create', 'survey.read', 'survey.update', 'survey.delete', 'survey.schedule'],
    ['messageTopic.create', 'messageTopic.read', 'messageTopic.update', 'messageTopic.delete'],
    [
        'message.create', 'message.reply', 'message.delete',
        'message.view:outbound', 'message.view:archive',
        'message.view:telephone', 'message.view:beds', 'message.view:arriving'
    ],
    ['faq.create', 'faq.update', 'faq.delete', 'faq.read', 'faq.sort'],
    ['credits.update'],
];

$roles = ['doctor', 'admin'];

//admin
$admin = Arr::collapse($permissions);
$doctor = Arr::collapse($permissions);
return [
    'roles' => $roles,
    'doctor' => $doctor,
    'admin' => $admin,
    'patient' => []
];
