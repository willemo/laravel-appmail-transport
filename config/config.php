<?php

return [

    /*
     |-------------------------------------------------------------------------
     | AppMail API Key
     |-------------------------------------------------------------------------
     |
     | You can generate the API key on the Credentials page for your server
     */

    'api_key' => env('APPMAIL_KEY'),

    /*
     |-------------------------------------------------------------------------
     | AppMail API Host
     |-------------------------------------------------------------------------
     |
     | The AppMail host for sending API requests, defaults to "api.appmail.io"
     */

    'api_host' => env('APPMAIL_HOST', 'api.appmail.io'),

    /*
     |-------------------------------------------------------------------------
     | AppMail API version
     |-------------------------------------------------------------------------
     |
     | The AppMail API version, defaults to "v1"
     */

    'api_version' => env('APPMAIL_VERSION', 'v1'),

];
