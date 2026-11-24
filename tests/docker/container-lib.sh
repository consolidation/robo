#!/usr/bin/env bash
# Helpers sourced by the scripts that run *inside* the test containers.

set -euo pipefail

log() {
    printf '\033[1;34m[php %s] %s\033[0m\n' "$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')" "$*"
}

fail() {
    printf '\033[1;31mFAIL: %s\033[0m\n' "$*" >&2
    exit 1
}

# Copy the read-only Robo checkout to /robo, without its vendor directory and
# build artifacts, so that it can be used as a Composer path repository.
export_robo() {
    mkdir -p /robo
    tar -C /robo-src \
        --exclude=./vendor --exclude=./.git --exclude=./site \
        --exclude=./robo.phar --exclude=./build --exclude=./tests \
        -cf - . | tar -C /robo -xf -
}

# Make the Composer project in the current directory install Robo from /robo.
use_local_robo() {
    composer config repositories.robo \
        "{\"type\": \"path\", \"url\": \"/robo\", \"options\": {\"symlink\": false, \"versions\": {\"consolidation/robo\": \"$ROBO_TEST_VERSION\"}}}"
}

# Verify that the installed consolidation/robo is the local checkout.
assert_local_robo() {
    local installed
    installed="$(composer show consolidation/robo --format=json | php -r 'echo json_decode(stream_get_contents(STDIN), true)["versions"][0];')"
    [[ "$installed" == "$ROBO_TEST_VERSION" ]] \
        || fail "expected consolidation/robo $ROBO_TEST_VERSION from the local checkout, got $installed"
    cmp -s /robo/src/Robo.php vendor/consolidation/robo/src/Robo.php \
        || fail "vendor/consolidation/robo does not match the local checkout"
    log "Using local Robo checkout ($installed)"
}
