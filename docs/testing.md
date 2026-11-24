# Running the Automated Tests

This page covers running the tests of Robo itself. For the tasks that run test tools from a RoboFile (`taskPHPUnit()`, `taskBehat()`, `taskCodecept()`, …), see [Testing Tasks](tasks/Testing.md).

The quickest check is to run the test suite directly:

```sh
composer install
composer test
```

`composer test` runs these Composer scripts in order and stops at the first failure:

| Script | What it does |
|---|---|
| `composer lint` | `php -l` on every file in `src` and `tests/src` |
| `composer unit` | PHPUnit |
| `composer cs` | php-cs-fixer, in check mode (`composer cs-fix` applies the fixes) |
| `composer test-docker` | Docker integration tests with Drush, JoRobo and Joomla (see below) |

## Docker integration tests

These tests check that Robo still works for downstream projects built on it. Each test installs the downstream project in a fresh Docker container, using **this Robo checkout** (including uncommitted changes) as a Composer path repository instead of the released `consolidation/robo` package. Each environment also checks that the installed `consolidation/robo` package is that checkout.

| Suite | Environments | What it checks |
|---|---|---|
| `drush` | Drush 13 with Drupal 10 and 11, on PHP 8.3, 8.4, 8.5 and 8.6 (SQLite) | Installs Drupal with `drush site:install`, then runs `drush status`, `drush cache:rebuild` and `drush php:eval` |
| `joomla` | JoRobo (latest release tag) and Joomla 6 (latest release), on PHP 8.3 (MariaDB 11) | Runs [JoRobo](https://github.com/joomla-projects/jorobo)'s own PHPUnit suite, set up the same way as JoRobo's CI with [weblinks](https://github.com/joomla-extensions/weblinks) as the extension under test. Then builds the weblinks package with `robo build`, installs Joomla with its CLI installer, and installs the package with `joomla extension:install` |

Drush 13 is the last Drush version that uses Robo. Drush 8 never used it, and the Drush 14 and 15 branches (not yet released) have dropped it.

`composer test-docker` runs both suites, and `composer test-drush` and `composer test-joomla` run one each. All three call `tests/docker/run.sh`. Each suite's environments and setup are defined in `tests/docker/<suite>/suite.sh`. Without Docker, the tests print a warning and are skipped.

### Running a subset

Name a suite to run its default environments, follow it with `VERSION:PHP` pairs to pick environments, or give `SUITE:VERSION:PHP` directly. A Joomla version can be a major version (its latest release is used) or an exact release:

```sh
composer test-drush -- 11:8.4               # Drupal 11 on PHP 8.4
tests/docker/run.sh drush 10:8.3 11:8.6     # two Drush environments
tests/docker/run.sh joomla:6.2.1:8.3 drush  # one Joomla release and all Drush environments
```

### Parallel runs

Environments run in parallel, half as many at a time as Docker has CPUs (4 with 8 CPUs). All selected suites share one pool of jobs, and the Joomla environments start first because they take the longest. Each one writes to its own log file, and a `PASS` or `FAIL` line is printed as each finishes. When one fails, the end of its log is printed and the log directory is kept. With `ROBO_DOCKER_JOBS=1`, the environments run one at a time and their output is shown as it happens.

The first run is the slowest, because it builds the images and fills the Composer cache. After that, a Drush environment takes about 7 seconds and a Joomla environment about 15, and `composer test-docker` finishes in about 25 seconds.

### Environment variables

| Variable | Effect |
|---|---|
| `ROBO_DOCKER_TESTS=skip` | Skip the Docker tests, e.g. `ROBO_DOCKER_TESTS=skip composer test` |
| `ROBO_DOCKER_TESTS=required` | Fail instead of skipping when Docker isn't available |
| `ROBO_DOCKER_JOBS=N` | Number of environments to test in parallel |
| `ROBO_DOCKER_COMPOSER_CACHE=DIR` | Host directory for Composer's cache, instead of the `robo-docker-test-composer-cache` volume. On macOS with Docker Desktop, use a directory under `/Users`: paths under `/tmp` stay inside Docker's VM |
| `JOROBO_REF` | JoRobo tag or branch to test (default: latest release tag) |
| `WEBLINKS_REF` | weblinks branch used as the extension under test (default: `5.x-dev`) |

### On GitHub Actions

`.github/workflows/docker-integration.yml` runs these tests on every pull request to `5.x`. Pushes don't trigger it. Each environment runs as a separate job, so they all run at the same time and show up as separate checks on the PR. The environments come from `tests/docker/run.sh --list`, so `tests/docker/<suite>/suite.sh` is the only place they're defined.

To run it by hand, for example on a release branch before `robo release`, open **Actions → Docker integration tests → Run workflow**. You can pick the branch, and optionally which suites or environments to test, in the same form as for `run.sh` (e.g. `drush 11:8.4 joomla`).

The jobs pass the workflow's `GITHUB_TOKEN` to Composer through `COMPOSER_AUTH`, so GitHub doesn't rate-limit its downloads, and keep Composer's cache between runs with `actions/cache`.

### Cleaning up

Images are tagged `robo-docker-test:php<version>`, and Composer downloads are cached in the `robo-docker-test-composer-cache` volume. Remove them when they're no longer needed:

```sh
docker rmi $(docker images -q robo-docker-test)
docker volume rm robo-docker-test-composer-cache
```

## Reproducing a CI job

To reproduce a specific CI job (a particular PHP version or Symfony pin from the matrix in `.github/workflows/ci.yml`), use [act](https://github.com/nektos/act), which runs GitHub Actions workflows locally. There are two ways to run it:

1. **Natively**: the job's steps run directly on your machine.
2. **In Docker**: the job runs inside an Ubuntu container, close to GitHub's hosted runners.

Either way, act only runs Linux jobs. The `windows-latest` matrix entries are skipped, so they can only be checked on GitHub.

### Setup (both methods)

Install act:

```sh
brew install act
```

act needs a GitHub token to download the actions the workflow uses (`actions/checkout`, `shivammathur/setup-php`, …). The examples below take it from the GitHub CLI:

```sh
export GITHUB_TOKEN="$(gh auth token)"
```

If `GH_HOST` points at a GitHub Enterprise host, use `env -u GH_HOST gh auth token` instead.

#### Work around `runs-on: ${{ matrix.os }}`

The `tests` job selects its runner with `runs-on: ${{ matrix.os }}`. Some act versions (seen in 0.2.98) don't map an expression like this to a runner image, and fail with `image=` and `invalid reference format`. To work around it, run a temporary copy of the workflow with the label written out:

```sh
mkdir -p /tmp/act-wf
sed 's/runs-on: \${{ matrix.os }}/runs-on: ubuntu-latest/' .github/workflows/ci.yml > /tmp/act-wf/ci.yml
```

Re-run this after editing `ci.yml`. Because every matrix entry in the copy says `ubuntu-latest`, always pass `--matrix os:ubuntu-latest` so the Windows entries don't run too.

Pick a matrix entry with `--matrix key:value`. For example, `--matrix dependencies:symfony7 --matrix php-version:8.4` selects the Symfony 7.4 job on PHP 8.4. Run `act -l` to list jobs, and add `-n` to any command for a dry run.

### Option 1: Native (self-hosted mode)

Mapping the runner label to `-self-hosted` makes act run each step directly on your machine. Docker isn't needed.

```sh
act push -W /tmp/act-wf/ci.yml -j tests \
  --matrix os:ubuntu-latest --matrix dependencies:symfony7 --matrix php-version:8.4 \
  -P ubuntu-latest=-self-hosted \
  -s GITHUB_TOKEN
```

- **Pros:** No image download and no emulation, so runs start fast.
- **Cons:** It isn't isolated. `shivammathur/setup-php` installs or switches the requested PHP version on your machine (via Homebrew on macOS), and `composer update` rewrites `composer.json` and `composer.lock` in your working tree. Afterwards, restore them with `git checkout composer.json composer.lock && composer install`. The job also runs on macOS rather than Ubuntu, so OS-specific behavior can differ from CI.

### Option 2: Docker

act runs the job in a container built from an Ubuntu runner image. Your working tree is copied in, so your files aren't modified, and the environment is close to GitHub's.

```sh
act push -W /tmp/act-wf/ci.yml -j tests \
  --matrix os:ubuntu-latest --matrix dependencies:symfony7 --matrix php-version:8.4 \
  -P ubuntu-latest=catthehacker/ubuntu:act-latest \
  --container-architecture linux/amd64 \
  -s GITHUB_TOKEN
```

- `-P ubuntu-latest=catthehacker/ubuntu:act-latest` uses the medium runner image, which has the tools `setup-php` needs.
- `--container-architecture linux/amd64` runs the same x86 image as GitHub. It's needed on Apple Silicon and runs under emulation there, so it's slower.
- The first run downloads a multi-GB image.

#### Using Colima instead of Docker Desktop

act ignores Docker contexts, so point it at Colima's socket explicitly. Also turn off mounting the Docker socket into the job container, since the workflow doesn't need it:

```sh
DOCKER_HOST="unix://$HOME/.colima/default/docker.sock" \
act push -W /tmp/act-wf/ci.yml -j tests \
  --matrix os:ubuntu-latest --matrix dependencies:symfony7 --matrix php-version:8.4 \
  -P ubuntu-latest=catthehacker/ubuntu:act-latest \
  --container-architecture linux/amd64 \
  --container-daemon-socket - \
  -s GITHUB_TOKEN
```

For faster amd64 emulation, enable Rosetta in Colima (`colima start --vz-rosetta`, or `rosetta: true` in `~/.colima/default/colima.yaml`).

#### Saving the flags

Flags that never change can go in `~/.actrc`, one per line:

```
-P ubuntu-latest=catthehacker/ubuntu:act-latest
--container-architecture linux/amd64
--container-daemon-socket -
```
