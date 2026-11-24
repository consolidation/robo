# Docker integration tests

These tests check that Robo works for downstream projects that are built on it.
Each test installs the downstream project in a fresh Docker container, using
**this Robo checkout** (including uncommitted changes) as a Composer path
repository instead of the released `consolidation/robo` package.

| Script                   | Composer script        | Environments                                                    |
|--------------------------|------------------------|-----------------------------------------------------------------|
| `run-drush.sh`           | `composer test-drush`  | Drush 13 with Drupal 10 and 11 on PHP 8.3, 8.4, 8.5, 8.6 (SQLite) |
| `run-joomla.sh`          | `composer test-joomla` | JoRobo's test suite, then weblinks on Joomla 6 (latest release), PHP 8.3 (MariaDB 11) |

Both scripts run as part of `composer test`.

* **Drush**: installs Drupal with `drush site:install`, then runs `drush status`,
  `drush cache:rebuild` and `drush php:eval`.
* **Joomla**: clones [JoRobo](https://github.com/joomla-projects/jorobo) (latest
  release tag) and runs its own PHPUnit suite, set up the same way as JoRobo's
  CI, with [weblinks](https://github.com/joomla-extensions/weblinks) as the
  extension under test. It then builds the weblinks package with `robo build`,
  installs Joomla with its CLI installer, and installs the package with
  `joomla extension:install`. Override the versions with `JOROBO_REF` and
  `WEBLINKS_REF` (defaults: latest tag, `5.x-dev`).

Each environment also checks that the installed `consolidation/robo` package is
the local checkout.

## Running a subset

Pass `DRUPAL:PHP` or `JOOMLA:PHP` pairs to override the default matrix:

```sh
composer test-drush -- 11:8.4
tests/docker/run-drush.sh 10:8.3 11:8.6
tests/docker/run-joomla.sh 6:8.4 6.2.1:8.3   # major version or exact release
```

## Environment variables

* `ROBO_DOCKER_TESTS=skip`: skip these tests (e.g. `ROBO_DOCKER_TESTS=skip composer test`).
* `ROBO_DOCKER_TESTS=required`: fail instead of skipping when Docker is not available.

Without Docker, the tests print a warning and are skipped.

Images are tagged `robo-docker-test:php<version>`. Composer downloads are
cached in the `robo-docker-test-composer-cache` volume. Remove both with
`docker rmi` and `docker volume rm` when they're no longer needed.
