#!/usr/bin/env bash
# Runs JoRobo's PHPUnit suite against the local Robo checkout in a Docker
# container, then builds joomla-extensions/weblinks with JoRobo and installs it
# into a fresh Joomla site.
#
# Usage: tests/docker/run-joomla.sh [JOOMLA:PHP ...]
#   e.g. tests/docker/run-joomla.sh 6:8.4 6.2.1:8.3
# JOOMLA is a major version (latest release is used) or an exact release.
#
# Environment: ROBO_DOCKER_TESTS=skip      skip these tests
#              ROBO_DOCKER_TESTS=required  fail instead of skipping without Docker
#              JOROBO_REF=0.14.2           JoRobo tag/branch (default: latest tag)
#              WEBLINKS_REF=5.x-dev        weblinks branch used as the test extension

# shellcheck source=SCRIPTDIR/lib.sh
source "$(dirname "$0")/lib.sh"

skip_if_disabled
require_docker

MATRIX=("$@")
if [[ ${#MATRIX[@]} -eq 0 ]]; then
    MATRIX=(6:8.3)
fi

DB_IMAGE="mariadb:11"
DB_PASS="robo-test"
NETWORK="robo-joomla-test-$$"
DB_CONTAINER="$NETWORK-db"

cleanup() {
    docker rm -f "$DB_CONTAINER" >/dev/null 2>&1 || true
    docker network rm "$NETWORK" >/dev/null 2>&1 || true
}
trap cleanup EXIT

docker pull --quiet "$DB_IMAGE" >/dev/null

failed=()
for entry in "${MATRIX[@]}"; do
    joomla="${entry%%:*}"
    php="${entry##*:}"
    log "Joomla $joomla / JoRobo / PHP $php"
    image="$(build_php_image "$php")"

    cleanup
    docker network create "$NETWORK" >/dev/null
    docker run -d --name "$DB_CONTAINER" --network "$NETWORK" \
        -e "MARIADB_ROOT_PASSWORD=$DB_PASS" -e MARIADB_DATABASE=joomla \
        "$DB_IMAGE" >/dev/null
    log "Waiting for $DB_IMAGE"
    for _ in $(seq 60); do
        docker exec "$DB_CONTAINER" healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1 && break
        sleep 1
    done

    # shellcheck disable=SC2046
    if ! docker run $(docker_run_args) --network "$NETWORK" \
        -e "JOOMLA_VERSION=$joomla" -e "DB_HOST=$DB_CONTAINER" -e "DB_PASS=$DB_PASS" \
        -e "JOROBO_REF=${JOROBO_REF:-latest}" -e "WEBLINKS_REF=${WEBLINKS_REF:-5.x-dev}" \
        "$image" bash /tests/joomla/test.sh; then
        failed+=("Joomla $joomla / PHP $php")
    fi
done

if [[ ${#failed[@]} -gt 0 ]]; then
    printf '\033[1;31mJoomla tests failed for:\033[0m\n' >&2
    printf '  - %s\n' "${failed[@]}" >&2
    exit 1
fi
log "All Joomla tests passed (${#MATRIX[@]} environments)"
