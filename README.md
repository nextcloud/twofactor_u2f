# U2F second factor provider for Nextcloud

![Downloads](https://img.shields.io/github/downloads/nextcloud/twofactor_u2f/total.svg)

|branch|target Nextcloud version|build status and code metrics|
|---|---|---|
|master| Nextcloud 14.0.x | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nextcloud/twofactor_u2f/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nextcloud/twofactor_u2f/?branch=master) [![Build Status](https://api.travis-ci.org/nextcloud/twofactor_u2f.svg?branch=master)](https://travis-ci.org/nextcloud/twofactor_u2f) [![JavaScript Coverage Status](https://coveralls.io/repos/github/nextcloud/twofactor_u2f/badge.svg?branch=master)](https://coveralls.io/github/nextcloud/twofactor_u2f?branch=master)
| stable1.5.4 | Nextcloud 13.0.2 | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nextcloud/twofactor_u2f/badges/quality-score.png?b=stable1.5.4)](https://scrutinizer-ci.com/g/nextcloud/twofactor_u2f/?branch=stable1.5.4) [![Build Status](https://api.travis-ci.org/nextcloud/twofactor_u2f.svg?branch=stable1.5)](https://travis-ci.org/nextcloud/twofactor_u2f) [![JavaScript Coverage Status](https://coveralls.io/repos/github/nextcloud/twofactor_u2f/badge.svg?branch=stable1.5.4)](https://coveralls.io/github/nextcloud/twofactor_u2f?branch=stable1.5.4) |

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
