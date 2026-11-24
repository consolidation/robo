#!/usr/bin/env bash
# Installs Drupal sites with Drush 13 and the local Robo checkout in Docker
# containers and runs basic Drush commands in each of them.
#
# Usage: tests/docker/run-drush.sh [DRUPAL:PHP ...]
#   e.g. tests/docker/run-drush.sh 11:8.4
# Without arguments, the full matrix below is tested.
#
# Environment: ROBO_DOCKER_TESTS=skip      skip these tests
#              ROBO_DOCKER_TESTS=required  fail instead of skipping without Docker

# shellcheck source=SCRIPTDIR/lib.sh
source "$(dirname "$0")/lib.sh"

skip_if_disabled
require_docker

MATRIX=("$@")
if [[ ${#MATRIX[@]} -eq 0 ]]; then
    MATRIX=(
        10:8.3 10:8.4 10:8.5 10:8.6
        11:8.3 11:8.4 11:8.5 11:8.6
    )
fi

failed=()
for entry in "${MATRIX[@]}"; do
    drupal="${entry%%:*}"
    php="${entry##*:}"
    log "Drupal $drupal / Drush 13 / PHP $php"
    image="$(build_php_image "$php")"
    # shellcheck disable=SC2046
    if ! docker run $(docker_run_args) -e "DRUPAL_VERSION=$drupal" "$image" bash /tests/drush/test.sh; then
        failed+=("Drupal $drupal / PHP $php")
    fi
done

if [[ ${#failed[@]} -gt 0 ]]; then
    printf '\033[1;31mDrush tests failed for:\033[0m\n' >&2
    printf '  - %s\n' "${failed[@]}" >&2
    exit 1
fi
log "All Drush tests passed (${#MATRIX[@]} environments)"
