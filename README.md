# U2F second factor provider for Nextcloud

![Downloads](https://img.shields.io/github/downloads/nextcloud/twofactor_u2f/total.svg)
![Build Status](https://api.travis-ci.org/nextcloud/twofactor_u2f.svg?branch=master)
![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nextcloud/twofactor_u2f/badges/quality-score.png?b=master)
![JavaScript Coverage Status](https://coveralls.io/repos/github/nextcloud/twofactor_u2f/badge.svg?branch=master)

![](screenshots/challenge.png)

# Requirements
In order to use this app for authentication, you have to use a browser that supports the U2F standard:
* Google Chrome
* Chromium
* Firefox 
  * V56 or before: In combination with [this extension](https://addons.mozilla.org/en-US/firefox/addon/u2f-support-add-on/)
  * V57 or newer: After activation of `security.webauth.u2f` in `about:config`
* Opera

## Login with external apps
Once you enable U2F with Two Factor U2F, your aplications (for example your Android app or your GNOME app) will need to login using device passwords. To manage it, [know more here](https://docs.nextcloud.com/server/11/user_manual/session_management.html#managing-devices)

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

Every device supporting U2F should work fine. The following devices are known to work:

* [Nitrokey FIDO U2F](https://shop.nitrokey.com/shop/product/nitrokey-fido-u2f-20)
