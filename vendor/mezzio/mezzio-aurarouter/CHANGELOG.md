# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 3.1.0 - 2021-01-24


-----

### Release Notes for [3.1.0](https://github.com/mezzio/mezzio-aurarouter/milestone/1)



### 3.1.0

- Total issues resolved: **1**
- Total pull requests resolved: **2**
- Total contributors: **2**

#### Enhancement

 - [4: PHP 8.0 compatiblity and dependency upgrades.](https://github.com/mezzio/mezzio-aurarouter/pull/4) thanks to @jensdenies and @boesing
 - [3: Updated PHPUnit to version 9.3.0 and bumped the minimum PHP version to 7.3](https://github.com/mezzio/mezzio-aurarouter/pull/3) thanks to @jensdenies

## 3.0.1 - 2019-06-20

### Added

- [zendframework/zend-expressive-aurarouter#39](https://github.com/zendframework/zend-expressive-aurarouter/pull/39) adds support for PHP 7.3.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.0.0rc3 - 2018-03-07

### Added

- Nothing.

### Changed

- [zendframework/zend-expressive-aurarouter#35](https://github.com/zendframework/zend-expressive-aurarouter/pull/35)
  updates the minimum supported version of mezzio-router to 3.0.0rc4.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.0.0rc2 - 2018-03-06

### Added

- Nothing.

### Changed

- [zendframework/zend-expressive-aurarouter#34](https://github.com/zendframework/zend-expressive-aurarouter/pull/34)
  updates the minimum supported version of mezzio-router to 3.0.0rc2.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-expressive-aurarouter#34](https://github.com/zendframework/zend-expressive-aurarouter/pull/34)
  fixes how the `AuraRouter` marshals a `RouteResult` when the router detects
  successful path-based matches and the current HTTP method is not supported,
  ensuring that a correct list of allowed HTTP methods is generated for a route
  result failure.

## 3.0.0rc1 - 2018-03-05

### Added

- Nothing.

### Changed

- [zendframework/zend-expressive-aurarouter#32](https://github.com/zendframework/zend-expressive-aurarouter/pull/32)
  updates the package to pin to mezzio-router 3.0.0rc1 or later.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-expressive-aurarouter#32](https://github.com/zendframework/zend-expressive-aurarouter/pull/32)
  fixes an issue with how a failure result is marshaled when the path patches
  but the request method does not. The package now correctly aggregates allowed
  methods for the route result failure instance.

## 3.0.0alpha2 - 2018-02-06

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixes the `ConfigProvider` to alias `Mezzio\Router\RouterInterface`
  to `Mezzio\Router\AuraRouter`, and to define the latter as an
  invokable.

## 3.0.0 - 2018-03-15

### Added

- [zendframework/zend-expressive-aurarouter#26](https://github.com/zendframework/zend-expressive-aurarouter/pull/26) and
  [zendframework/zend-expressive-aurarouter#31](https://github.com/zendframework/zend-expressive-aurarouter/pull/31) adds
  support for the mezzio-router 3.0 series.

- [zendframework/zend-expressive-aurarouter#30](https://github.com/zendframework/zend-expressive-aurarouter/pull/30)
  adds the class `Mezzio\Router\AuraRouter\ConfigProvider`, and exposes
  it in the `composer.json` as a `config-provider`.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-expressive-aurarouter#26](https://github.com/zendframework/zend-expressive-aurarouter/pull/26)
  removes support for the mezzio-router 2.0 series.

- [zendframework/zend-expressive-aurarouter#26](https://github.com/zendframework/zend-expressive-aurarouter/pull/26)
  removes support for PHP 5.6 and PHP 7.0.

### Fixed

- Nothing.

## 2.2.0 - 2018-03-08

### Added

- Nothing.

### Changed

- [zendframework/zend-expressive-aurarouter#36](https://github.com/zendframework/zend-expressive-aurarouter/pull/36)
  updates the minimum supported version of mezzio-router to 2.4.0.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.1.1 - 2017-12-06

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-expressive-aurarouter#27](https://github.com/zendframework/zend-expressive-aurarouter/pull/27)
  removes support for the 3.0.0-dev versions of mezzio-router, as it
  contains backwards-incompatible API changes.

### Fixed

- Nothing.

## 2.1.0 - 2017-12-05

### Added

- [zendframework/zend-expressive-aurarouter#23](https://github.com/zendframework/zend-expressive-aurarouter/pull/23)
  adds support for PHP 7.2.

- [zendframework/zend-expressive-aurarouter#25](https://github.com/zendframework/zend-expressive-aurarouter/pull/25)
  adds support for the 3.0.0-dev series of mezzio-router, which has an
  API that is compatible with the 2.X series.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-expressive-aurarouter#23](https://github.com/zendframework/zend-expressive-aurarouter/pull/23)
  removes support for HHVM.

### Fixed

- Nothing.

## 2.0.1 - TBD

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.0.0 - 2017-01-11

### Added

- [zendframework/zend-expressive-aurarouter#20](https://github.com/zendframework/zend-expressive-aurarouter/pull/20)
  adds support for mezzio-router 2.0. This includes a breaking change
  to those _extending_ `Mezzio\Router\AuraRouter`, as the
  `generateUri()` method now expects a third, optional argument,
  `array $options = []`.

  For consumers, this represents new functionality; you may now pass router
  options, such as a translator and/or translation text domain, via the new
  argument when generating a URI. Currently, Aura.Router does not support any
  options.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.1.3 - 2016-12-15

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-expressive-aurarouter#19](https://github.com/zendframework/zend-expressive-aurarouter/pull/19)
  ensures that when `HTTP_METHOD_ANY` is specified for the route, any HTTP
  method results in a successful routing result.

## 1.1.2 - 2016-12-15

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-expressive-aurarouter#18](https://github.com/zendframework/zend-expressive-aurarouter/pull/18)
  fixes what happens when a route specifies no valid HTTP methods. Aura.Router
  always treats these as routing matches, but they should not be. This patch
  updates the implementation to determine if the matched route specifies any
  valid HTTP methods; if not, it treats it as a potential failed result
  (potential only, as HEAD and OPTIONS requests will be implicitly successful).

## 1.1.1 - 2016-12-14

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-expressive-aurarouter#17](https://github.com/zendframework/zend-expressive-aurarouter/pull/17)
  fixes how the router reports a failure when a root path matches a portion of
  the request path. With the upgrade to Aura.Router v3, these were incorrectly
  being reported as 405 errors instead of 404; they are not reported correctly.

- [zendframework/zend-expressive-aurarouter#17](https://github.com/zendframework/zend-expressive-aurarouter/pull/17)
  fixes how the router returns allowed methods when a 405 occurs; previously, it
  would return the allowed methods from the first route matching the path; it
  now returns the aggregated set of methods supported from all routes with the
  same path.

## 1.1.0 - 2016-12-14

### Added

- [zendframework/zend-expressive-aurarouter#7](https://github.com/zendframework/zend-expressive-aurarouter/pull/7) adds
  support for specifying wildcard segments via the `wildcard` option passed to a
  route:

  ```php
  $app->get('/foo', $middleware, 'foo')
      ->setOptions(['wildcard' => 'captured']); // captures to "captured" param
  ```

### Changed

- [zendframework/zend-expressive-aurarouter#11](https://github.com/zendframework/zend-expressive-aurarouter/pull/11)
  updates the component to use the Aura.Router version 3 series instead of the
  version 2 series. The exposed API remains the same.

- [zendframework/zend-expressive-aurarouter#15](https://github.com/zendframework/zend-expressive-aurarouter/pull/15)
  updates the router to populate the returned `RouteResult` with the associated
  `Mezzio\Router\Route` instance on a successful route match.

- [zendframework/zend-expressive-aurarouter#15](https://github.com/zendframework/zend-expressive-aurarouter/pull/15)
  updates the router to always honor `HEAD` and `OPTIONS` requests when a path
  matches. Dispatchers will need to check the `Route` composed in the
  `RouteResult` to determine if matches against these methods were explicit or
  implicit (using `Route::implicitHead()` and `Route::implicitOptions()`).

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.0 - 2015-12-07

First stable release.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.3.0 - 2015-12-02

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Updated to use [mezzio/mezzio-router](https://github.com/mezzio/mezzio-router)
  instead of mezzio/mezzio.

## 0.2.0 - 2015-10-20

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Updated to Mezzio RC builds.

## 0.1.0 - 2015-10-10

Initial release.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
