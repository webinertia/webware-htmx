# webware/webware-htmx

Provides HTMX support via laminas-view to webware applications: AJAX request detection middleware, HTMX response headers and triggers, and a body-aware laminas-view renderer.

[![PHP Version](https://img.shields.io/packagist/php-v/webware/webware-htmx)](https://packagist.org/packages/webware/webware-htmx)
[![Latest Version](https://img.shields.io/packagist/v/webware/webware-htmx)](https://packagist.org/packages/webware/webware-htmx)
[![License](https://img.shields.io/github/license/webinertia/webware-htmx)](LICENSE)
[![Continuous Integration](https://github.com/webinertia/webware-htmx/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/webinertia/webware-htmx/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/webinertia/webware-htmx/graph/badge.svg)](https://codecov.io/gh/webinertia/webware-htmx)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fwebinertia%2Fwebware-htmx%2F0.1.x)](https://dashboard.stryker-mutator.io/reports/github.com/webinertia/webware-htmx/0.1.x)

## Installation

```bash
composer require webware/webware-htmx
```

Register the `ConfigProvider` in your Mezzio application config aggregator:

```php
Webware\Htmx\ConfigProvider::class,
```

## What it provides

- `DetectAjaxRequestMiddleware` — detects HTMX requests and filters the incoming server request.
- `DisableBodyMiddleware` — disables the body layer for HTMX requests.
- `Webware\Htmx\Response\Header` — HTMX response header helpers (`HX-Trigger`, `HX-Redirect`, …).
- `Webware\Htmx\View\LaminasRenderer` — a laminas-view renderer with body/layout layering, registered as the default `TemplateRendererInterface`.
- A default `body::default` template (app-overridable).

## Template override

The default body template ships at `body::default`. Applications override it by registering their own `templates.map['body::default']` path before the package `ConfigProvider` in the config aggregator.
