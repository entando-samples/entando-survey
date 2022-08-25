<?php

return [
    'entando_keycloak_client_id' => env('KEYCLOAK_CLIENT_ID', 'survey'),
    'server_servlet_context_path' => str_replace('/', '', env('SERVER_SERVLET_CONTEXT_PATH', ''))
];
