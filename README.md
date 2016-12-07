# DirectAdmin API client

This is a simple DirectAdmin API Client in php7.

## Basic usage

To set up the connection use one of the base functions:

```php
use NHosting\DirectAdmin\DirectAdmin;

$da = new DirectAdmin('https://127.0.0.1:2222', 'admin', 'password');
```
It returns the main class with the functions get(...) and post(...).

## Legal

Read the LICENSE file for more information about the license terms.

