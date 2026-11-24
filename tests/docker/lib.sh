#!/usr/bin/env bash
# Shared helpers for the Docker-based integration tests.
# Sourced by tests/docker/run-*.sh; not meant to be executed directly.

set -euo pipefail

DOCKER_TEST_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROBO_ROOT="$(cd "$DOCKER_TEST_DIR/../.." && pwd)"
COMPOSER_CACHE_VOLUME="robo-docker-test-composer-cache"

# Version under which the local Robo checkout is offered to Composer. It has to
# satisfy the "^5.1" constraints of Drush and JoRobo, so the "-dev" suffix of
# Robo::VERSION is dropped.
ROBO_TEST_VERSION="$(sed -n "s/.*const VERSION = '\([0-9.]*\).*/\1/p" "$ROBO_ROOT/src/Robo.php")"

log() {
    printf '\033[1;34m==> %s\033[0m\n' "$*"
}

# Exit successfully (skip) when Docker is unavailable, unless ROBO_DOCKER_TESTS=required.
require_docker() {
    if command -v docker >/dev/null 2>&1 && docker info >/dev/null 2>&1; then
        return 0
    fi
    if [[ "${ROBO_DOCKER_TESTS:-}" == "required" ]]; then
        echo "Docker is not available, but ROBO_DOCKER_TESTS=required." >&2
        exit 1
    fi
    printf '\033[1;33mWARNING: Docker is not available; skipping %s.\033[0m\n' "$(basename "$0")" >&2
    exit 0
}

# Allow skipping explicitly, e.g. ROBO_DOCKER_TESTS=skip composer test
skip_if_disabled() {
    if [[ "${ROBO_DOCKER_TESTS:-}" == "skip" ]]; then
        echo "ROBO_DOCKER_TESTS=skip; skipping $(basename "$0")."
        exit 0
    fi
}

# Build (or reuse) the test image for a PHP version and print its tag.
build_php_image() {
    local php_version="$1"
    local tag="robo-docker-test:php${php_version}"
    log "Building image $tag" >&2
    docker build --quiet --pull=false \
        --build-arg "PHP_VERSION=$php_version" \
        -t "$tag" "$DOCKER_TEST_DIR" >/dev/null
    echo "$tag"
}

# Common "docker run" arguments: the Robo checkout (read-only), the test
# scripts, and a shared Composer cache volume.
docker_run_args() {
    echo --rm \
        -v "$ROBO_ROOT:/robo-src:ro" \
        -v "$DOCKER_TEST_DIR:/tests:ro" \
        -v "$COMPOSER_CACHE_VOLUME:/composer-cache" \
        -e "ROBO_TEST_VERSION=$ROBO_TEST_VERSION"
}
