# Digitalquill Laravel Utils

## Installation

Add this to your `composer.json` file:

    "require": {
        "dq/laravel-utils": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://gitlab.digitalquill.co.uk/useful/laravel-utils.git"
        }
    ]

Then register any service provider you need in `config/app.php`.

# Usage

    Dq\LaravelUtils\SqlLogServiceProvider

> This will add sql queries to laravel log
> Queries will be separated by request uri
