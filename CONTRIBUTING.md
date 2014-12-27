# Contributing to Robo

## Testing changes locally

* Enable `phar.readonly` in your `php.ini` config file (this will allow you to build `.phar` files)
* Clone the repository
* Make your changes
* Build the robo executable: `php robo phar:build`
* Rename it: `mv robo.phar robo`
* Test your changes: `php robo <command>`
