# Changelog
All notable changes to this project will be documented in this file.

## 6.3.1 – 2022-06-13
### Changed
- App is now officially deprecated. Two-Factor WebAuthn can be used as replacement
- Updated dependencies

## 6.3.0 – unreleased
### Added
- Nextcloud 23 support
### Changed
- New and updated translations
- Updated dependencies

## 6.2.0 – 2021-06-24
### Added
- Nextcloud 22 support
### Changed
- New and updated translations
- Updated dependencies
- Documented list of supported devices

## 6.1.0 – 2021-01-25
### Added
- Nextcloud 21 support
- PHP8 support
### Changed
- New and updated translations
- Updated dependencies

## 6.0.0 – 2020-08-26
### Added
- Nextcloud 20 support
### Changed
- New and updated translations
- Updated dependencies
### Removed
- Nextcloud 17 support

## 5.1.0 – 2020-03-19
### Added
- Nextcloud 19 support
### Changed
- New and updated translations
- Updated dependencies

## 5.0.2 – 2020-01-07
### Added
- Warning if protocol and/or appId protocols do not match
### Changed
- New and updated translations
- Updated dependencies

## 5.0.1 – 2019-12-12
### Changed
- New and updated translations
- Updated dependencies
### Fixed
- JavaScript vulnerabilities in `handlebars`, `mem` and `serialize-javascript` dependency

## 5.0.0 – 2019-12-02
### Added
- Nextcloud 18 support
- php7.4 support
### Changed
- New and updated translations
- Updated dependencies
### Fixed
- Npm dependency vulnerability
- Unnecessarily big challenge bundle
### Removed
- php7.1 support

## 4.0.0 – 2019-08-26
### Added
- Ability to set up during login
### Changed
- New and updated translations
### Removed
- Nextcloud 16 support

## 3.0.1 – 2019-08-06
### Added
- `lodash` npm package vulnerability
### Changed
- Updated translations
- Updated dependencies

## 3.0.0 – 2019-05-03
### Added
- Nextcloud 17 support
### Changed
- Updated translations
- Updated dependencies
### Fixed
- `tar` npm package vulnerability
### Removed
- Nextcloud 15 support

## 2.1.3 – 2019-04-04
### Fixed
- Listing keys without a name
- Format HTTP warning as warning text color
- Translatable provider description (for the settings page header)
### Changed
- Updated translations
- Updated dependencies

## 2.1.2 – 2019-03-07
### Fixed
- App name typo
### Changed
- Updated translations
- Updated dependencies

## 2.1.1 – 2019-02-12
### Added
- More client-side debug logging
### Fixed
- Updated vulnerable `lodash` dependency

## 2.1.0 – 2018-12-12
### Added
- Ability to disable provider via `occ twofactor:disable <uid> u2f`
- Support for Nextcloud 16
- Support for php 7.3
- New and updated translations
### Fixed
- Password confirmation on IE and other outdated browsers

## 2.0.2 – 2018-11-20
### Fixed
- Missing challenge script
### Changed
- New and updated translations

## 2.0.1 – 2018-11-19
### Fixed
- Updated vulnerable npm dependencies
### Changed
- New and updated translations

## 2.0.0 – 2018-11-09
### Added
- Nextcloud 15 support
- New personal settings page (consolidated with other 2FA providers)
### Removed
- Nextcloud 14 support

## 1.6.1 – 2018-08-02
### Fixed
- Server provider registration updates when using multiple u2f devices

## 1.6.0 – 2018-08-02
### Added
- Nextcloud 14 support (requires beta 2+)
- Performance improvements
- New and updated translations
### Changed
- Dropped Nextcloud 13 support

## 1.5.5 – 2018-05-14
### Fixed
- Installation on Nextcloud 13.x

## 1.5.3 – 2018-04-30
### Fixed
- Allow Nextcloud 13.0.2

## 1.5.2 – 2018-03-28
### Changed
- Requires Nextcloud 13.0.1
### Fixed
- U2F on Firefox Quantum and mobile browsers

## 1.5.1 – 2018-01-09
### Added
- New and updated translations

## 1.5.0 – 2017-12-15
### Added
- Show warning if HTTP is used
- Php7.2 support
- Nextcloud 13 support
- New translations
- Firefox' internal U2F
### Fixed
- Settings are now listed in security section

## 1.4.0 – 2017-10-30
### Added
- Better U2F error messages

## 1.3.3 – 2017-08-21
### Added
- Translations
### Fixed
- Activity type for 2FA activies

## 1.3.2 – 2017-05-15
### Fixed
- Login fails when using multiple U2F devices

## 1.3.1 – 2017-05-04
### Fixed
- Login fails when using multiple U2F devices

## 1.3.0 – 2017-05-02
### Added
- Support for multiple U2F devices
- Translations
### Fixed
- Personal settings icon on NC12

## 1.2.0 – 2017-04-03
### Added
- Settings icon (NC12 only)
### Changed
- New and updated translations

## 1.1.0 – 2017-02-06
### Added
- Password confirmation when enabling/disabling the provider
- Translations

## 1.0.0 – 2017-01-23
### Added
- Nextcloud 12 support
- php7.1 support
- Updated U2F library to support NFC tokens
- Publish activities when U2F is enabled or disabled (Nextcloud 12 only)

### Fixed
- Replaced deprecated IDb interface by IDBConnection
