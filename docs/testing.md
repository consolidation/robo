# Running the Automated Tests

The quickest check is to run the test suite directly:

```sh
composer install
composer test        # lint + phpunit + php-cs-fixer
```

To reproduce a specific CI job (a particular PHP version or Symfony pin from the matrix in `.github/workflows/ci.yml`), use [act](https://github.com/nektos/act), which runs GitHub Actions workflows locally. There are two ways to run it:

1. **Natively**: the job's steps run directly on your machine.
2. **In Docker**: the job runs inside an Ubuntu container, close to GitHub's hosted runners.

Either way, act only runs Linux jobs. The `windows-latest` matrix entries are skipped, so they can only be checked on GitHub.

## Setup (both methods)

Install act:

```sh
brew install act
```

act needs a GitHub token to download the actions the workflow uses (`actions/checkout`, `shivammathur/setup-php`, …). The examples below take it from the GitHub CLI:

```sh
export GITHUB_TOKEN="$(gh auth token)"
```

If `GH_HOST` points at a GitHub Enterprise host, use `env -u GH_HOST gh auth token` instead.

### Work around `runs-on: ${{ matrix.os }}`

The `tests` job selects its runner with `runs-on: ${{ matrix.os }}`. Some act versions (seen in 0.2.98) don't map an expression like this to a runner image, and fail with `image=` and `invalid reference format`. To work around it, run a temporary copy of the workflow with the label written out:

```sh
mkdir -p /tmp/act-wf
sed 's/runs-on: \${{ matrix.os }}/runs-on: ubuntu-latest/' .github/workflows/ci.yml > /tmp/act-wf/ci.yml
```

Re-run this after editing `ci.yml`. Because every matrix entry in the copy says `ubuntu-latest`, always pass `--matrix os:ubuntu-latest` so the Windows entries don't run too.

Pick a matrix entry with `--matrix key:value`. For example, `--matrix dependencies:symfony7 --matrix php-version:8.4` selects the Symfony 7.4 job on PHP 8.4. Run `act -l` to list jobs, and add `-n` to any command for a dry run.

## Option 1: Native (self-hosted mode)

Mapping the runner label to `-self-hosted` makes act run each step directly on your machine. Docker isn't needed.

```sh
act push -W /tmp/act-wf/ci.yml -j tests \
  --matrix os:ubuntu-latest --matrix dependencies:symfony7 --matrix php-version:8.4 \
  -P ubuntu-latest=-self-hosted \
  -s GITHUB_TOKEN
```

- **Pros:** No image download and no emulation, so runs start fast.
- **Cons:** It isn't isolated. `shivammathur/setup-php` installs or switches the requested PHP version on your machine (via Homebrew on macOS), and `composer update` rewrites `composer.json` and `composer.lock` in your working tree. Afterwards, restore them with `git checkout composer.json composer.lock && composer install`. The job also runs on macOS rather than Ubuntu, so OS-specific behavior can differ from CI.

## Option 2: Docker

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

### Using Colima instead of Docker Desktop

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

### Saving the flags

Flags that never change can go in `~/.actrc`, one per line:

```
-P ubuntu-latest=catthehacker/ubuntu:act-latest
--container-architecture linux/amd64
--container-daemon-socket -
```
