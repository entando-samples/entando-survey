<?php 

return [
  'realm_public_key' => '',

  'load_user_from_database' => env('KEYCLOAK_LOAD_USER_FROM_DATABASE', false),
  
  'user_provider_custom_retrieve_method' => null,

  'user_provider_credential' => env('KEYCLOAK_USER_PROVIDER_CREDENTIAL', 'username'),

  'token_principal_attribute' => env('KEYCLOAK_TOKEN_PRINCIPAL_ATTRIBUTE', 'preferred_username'),

  'append_decoded_token' => env('KEYCLOAK_APPEND_DECODED_TOKEN', true),

  'allowed_resources' => env('KEYCLOAK_ALLOWED_RESOURCES', null)
];
