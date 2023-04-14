<?php

return [
    'server' => env('LDAP_SERVER'),
    'ou' => env('LDAP_OU'),
    'username' => env("LDAP_USERNAME"),
    'password' => env("LDAP_PASSWORD"),
    'use_ldap_auth' => env('LDAP_AUTHENTICATION', true),
];
