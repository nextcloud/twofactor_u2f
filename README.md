# U2F second factor provider for Nextcloud

![Downloads](https://img.shields.io/github/downloads/nextcloud/twofactor_u2f/total.svg)
![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nextcloud/twofactor_u2f/badges/quality-score.png?b=master)
![JavaScript Coverage Status](https://coveralls.io/repos/github/nextcloud/twofactor_u2f/badge.svg?branch=master)

![](screenshots/challenge.png)

# Requirements
In order to use this app for authentication, you have to use a browser that supports the U2F standard:

* Brave Browser
* Chromium
* Google Chrome
* Microsoft Edge
* Mozilla Firefox
  * V67 or newer
  * V57 to V66: After activation of `security.webauth.u2f` in `about:config`
  * V56 or before: In combination with [this extension](https://addons.mozilla.org/en-US/firefox/addon/u2f-support-add-on/)
* Opera

## Login with external apps
Once you enable U2F with Two Factor U2F, your applications (for example your GNOME app) will need to login using device passwords. Which can be managed in your security settings.

Official aplications such as the Android or IOS clients and desktop clients can use much safer tokens to login. Apps will automatically redirect you to a browser window to login as usual.

## Development Setup

This app uses [composer](https://getcomposer.org/) and [npm](https://www.npmjs.com/) to manage dependencies. Use

```bash
composer install
npm install
npm run build
```

or if you're using [Krankerl](https://github.com/ChristophWurst/krankerl)

```bash
krankerl up
```

to set up a development version of this app.

## Supported devices

As this App implements the U2F standard,
Every device supporting U2F should work fine. The following devices are known to work:

* [Google Titan Security Key](https://support.google.com/titansecuritykey/answer/9115487?hl=en)
* [GoTrust IdemKey](https://www.gotrustid.com/idem-key)
* [Nitrokey FIDO U2F](https://shop.nitrokey.com/shop/product/nitrokey-fido-u2f-20)
* [SoloKey](https://github.com/solokeys/solo)
  * HW version 2.1V
* [Yubikey 4 & 5 Series](https://www.yubico.com/products/yubikey-5-overview/)

## Notes

* The U2F Standard only works over the HTTPS protocol so make sure the following options are set in your config.php file

```php
'overwriteprotocol' => 'https'
```
