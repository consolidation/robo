#!/usr/bin/env bash
# Joomla suite for tests/docker/run.sh: JoRobo's PHPUnit suite with the local
# Robo checkout, then weblinks built with JoRobo and installed into Joomla.
# Entries are JOOMLA:PHP, where JOOMLA is a major version (latest release) or
# an exact release.

# shellcheck disable=SC2034 # read by run.sh
joomla_matrix=(6:8.3)

JOOMLA_DB_IMAGE="mariadb:11"
JOOMLA_DB_PASS="robo-test"

joomla_setup() {
    docker pull --quiet "$JOOMLA_DB_IMAGE" >/dev/null
}

# joomla_run_entry JOOMLA:PHP IMAGE
# Each entry gets its own network and database, so entries can run in parallel.
joomla_run_entry() {
    local entry="$1" image="$2"
    local network="robo-joomla-test-$$-${entry//[^a-zA-Z0-9]/-}"
    local db="$network-db"

    docker network create --label "$RUN_LABEL" "$network" >/dev/null
    docker run -d --label "$RUN_LABEL" --name "$db" --network "$network" \
        -e "MARIADB_ROOT_PASSWORD=$JOOMLA_DB_PASS" -e MARIADB_DATABASE=joomla \
        "$JOOMLA_DB_IMAGE" >/dev/null
    for _ in $(seq 60); do
        docker exec "$db" healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1 && break
        sleep 1
    done

    local status=0
    # shellcheck disable=SC2046
    docker run $(docker_run_args) --network "$network" \
        -e "JOOMLA_VERSION=${entry%%:*}" -e "DB_HOST=$db" -e "DB_PASS=$JOOMLA_DB_PASS" \
        -e "JOROBO_REF=${JOROBO_REF:-latest}" -e "WEBLINKS_REF=${WEBLINKS_REF:-5.x-dev}" \
        "$image" bash /tests/joomla/test.sh || status=$?

    docker rm -f "$db" >/dev/null 2>&1 || true
    docker network rm "$network" >/dev/null 2>&1 || true
    return $status
}
