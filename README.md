# Sign in With Line

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/ianm/oauth-line.svg)](https://packagist.org/packages/ianm/oauth-line) [![Total Downloads](https://img.shields.io/packagist/dt/ianm/oauth-line.svg)](https://packagist.org/packages/ianm/oauth-line)

A [Flarum](http://flarum.org) extension. Login to your Flarum forum using LINE

This extension adds `LINE` as an OAuth provider to [FoF OAuth](https://extiverse.com/extension/fof/oauth). You must therefore first enable the OAuth extension _before_ enabling this extension.

## Setup

In order to enable Login with LINE, you must first register on the [Line developer console](https://developers.line.biz/console/).

Once registered, create a new `channel` of the type `LINE login`, and select `web app`. Once you've created the channel, make a note of the `Channel ID` and `Channel secret`, then navigate to the `LINE login` tab. Here enter the callback URL for your forum - this will be displayed within the `OAuth` extension settings in your admin panel and look something like `https://forum.example.com/auth/line`.

Enter the `Channel ID` and `Channel secret` in the admin panel for your forum (This can be found at `{YOUR FORUM URL)/admin#/extension/fof-oauth`

## Installation

Install with composer:

```sh
composer require ianm/oauth-line:"*"
```

## Updating

```sh
composer update ianm/oauth-line
php flarum migrate
php flarum cache:clear
```

## Links

- [Packagist](https://packagist.org/packages/ianm/oauth-line)
- [GitHub](https://github.com/imorland/flarum-ext-oauth-line)
- [Discuss](https://discuss.flarum.org/d/PUT_DISCUSS_SLUG_HERE)
