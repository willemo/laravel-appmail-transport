# laravel-appmail-transport
[AppMail.io](appmail.io) mail transport for Laravel 5.x. This package adds a mail driver that supports the AppMail.io API v1.

## Installation
To install this package you'll have to require it in composer:

```
composer require willemo/laravel-appmail-transport:1.*
```

After this you'll have to **replace** the default MailServiceProvider from Laravel in `config/app.php`:

```php
Illuminate\Mail\MailServiceProvider::class, // remove this one
Willemo\LaravelAppMailTransport\ExtendedMailServiceProvider::class, // add this one
```

Now the only things you'll have to do is add your AppMail.io server key to the `config/services.php` file and add the `APPMAIL_KEY` variable to your `.env` file or environment variables:

### config/services.php:

```php
'appmail' => [
    'key' => env('APPMAIL_KEY'),
],
```

### .env:

```
APPMAIL_KEY=my_super_secret_appmail_server_key
```

Now all your emails will be sent through the AppMail.io API.

## Todo

What's still on the todo list for this package is the following:

- Process exceptions from the API;
- Create some way to get the message ID and tokens from the response from the API (possibly through events).
