Google Authentication
=====================

[![Build Status](https://travis-ci.org/kanboard/plugin-google-auth.svg?branch=master)](https://travis-ci.org/kanboard/plugin-google-auth)

Link a Google account to a Kanboard user profile.

Author
------

- Frédéric Guillot
- License MIT

Requirements
------------

- Access to the [Google Developer Console](https://console.developers.google.com)
- OAuth Google API credentials

Installation
------------

- Decompress the archive in the `plugins` folder

or

- Create a folder **plugins/GoogleAuth**
- Copy all files under this directory

Documentation
-------------

### How does this work?

- The Google authentication use the OAuth 2.0 protocol
- Any user account in Kanboard can be linked to a Google Account
- When a Kanboard user account is linked to Google, you can login with one click

### Procedure to link a Google Account

1. Go to your user profile
2. Click on **External accounts**
3. Click on the link **Link my Google Account**
4. You are redirected to the **Google Consent screen**
5. Authorize Kanboard by clicking on the button **Accept**
6. Your account is now linked

Now, on the login page you can be authenticated in one click with the link **Login with my Google Account**.

Your name and email are automatically updated from your Google Account.

### Installation instructions

#### Setting up OAuth 2.0 in Google Developer Console

- Follow the [official Google documentation](https://developers.google.com/accounts/docs/OAuth2Login#appsetup) to create a new application
- In Kanboard, you can get the **redirect url** in **Settings > Integrations > Google Authentication**

#### Setting up Kanboard

There are two different methods to configure Kanboard:

1. The easiest way is to copy and paste the Google Client credentials in the form **Settings > Integrations > Google Authentication**.
2. Or add the credentials in your custom config file

If you use the second method, use these parameters in your `config.php`:

```php
<?php

// Google client id (Get this value from the Google developer console)
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID');

// Google client secret key (Get this value from the Google developer console)
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');
```

### Notes

Kanboard use these information from your Google profile:

- Full name
- Email address
- Google unique id

The Google unique id is used to link together the local user account and the Google account.
