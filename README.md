Google Authentication
=====================

[![Build Status](https://travis-ci.org/kanboard/plugin-google-auth.svg?branch=master)](https://travis-ci.org/kanboard/plugin-google-auth)

- Link a Google account to a Kanboard user profile.
- Show Google Avatar image.

Author
------

- Frédéric Guillot
- License MIT

Requirements
------------

- Kanboard >= 1.0.37
- Access to the [Google Developer Console](https://console.developers.google.com)
- OAuth2 Google API credentials

Installation
------------

You have the choice between 3 methods:

1. Install the plugin from the Kanboard plugin manager in one click
2. Download the zip file and decompress everything under the directory `plugins/GoogleAuth`
3. Clone this repository into the folder `plugins/GoogleAuth`

Note: Plugin folder is case-sensitive.

Documentation
-------------

### Installation instructions

#### Setting up OAuth 2.0 in Google Developer Console

- Follow the [official Google documentation](https://developers.google.com/accounts/docs/OAuth2Login#appsetup) to create a new application
- In Kanboard, you can get the **redirect url** in **Settings > Integrations > Google Authentication**

#### Setting up Kanboard

There are two different methods to configure Kanboard:

![google_auth](https://cloud.githubusercontent.com/assets/323546/15695570/7b50cafc-2776-11e6-93d3-deea7f8f0b3f.png)

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

### Automatic account creation

1. As administrator, enable the account creation in **Settings > Integrations > Google Authentication**
2. If you are using Google Apps for Work, you can allow only your own domain name to avoid unwanted people
3. People just need to click on link "Login with my Google Account" on the login page to create a new account

Notes:

- If you don't apply any domain restriction, **everybody with a Google Account will be able to sign-up**
- The local part of the email address is used to generate Kanboard's username
- Users created by this way will be marked as remote user and the login form will be disabled for them (no local password)

### Procedure to link a Google Account

1. Go to your user profile
2. Click on **External accounts**
3. Click on the link **Link my Google Account**
4. You are redirected to the **Google Consent screen**
5. Authorize Kanboard by clicking on the button **Accept**
6. Your account is now linked

Now, on the login page you can be authenticated in one click with the link **Login with my Google Account**.

Your name and email are automatically updated from your Google Account.

### Notes

Kanboard uses these information from your Google profile:

- Full name
- Email address
- Google unique id

The Google unique id is used to link together the local user account and the Google account.

To disable the Google Avatar go to your **user profile > integrations**, and change the value of the checkbox.
