#!/usr/bin/env bash
# Shared helpers for the Docker-based integration tests.
# Sourced by tests/docker/run-*.sh; not meant to be executed directly.

set -euo pipefail

DOCKER_TEST_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROBO_ROOT="$(cd "$DOCKER_TEST_DIR/../.." && pwd)"
# Composer cache shared by all containers: a Docker volume, or a host
# directory given in ROBO_DOCKER_COMPOSER_CACHE (used by CI to persist it).
COMPOSER_CACHE_MOUNT="${ROBO_DOCKER_COMPOSER_CACHE:-robo-docker-test-composer-cache}"

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

# Label attached to every container and network a run creates, so that they
# can all be removed on exit, including after Ctrl-C or a failure.
RUN_LABEL="robo-docker-test=$$"

# Common "docker run" arguments: the Robo checkout (read-only), the test
# scripts, and the shared Composer cache. COMPOSER_AUTH is passed through when
# set, e.g. a GitHub token so that Composer isn't rate-limited by GitHub.
docker_run_args() {
    echo --rm --label "$RUN_LABEL" \
        -v "$ROBO_ROOT:/robo-src:ro" \
        -v "$DOCKER_TEST_DIR:/tests:ro" \
        -v "$COMPOSER_CACHE_MOUNT:/composer-cache" \
        -e "ROBO_TEST_VERSION=$ROBO_TEST_VERSION" \
        -e COMPOSER_AUTH
}

cleanup_docker_resources() {
    local ids
    ids="$(docker ps -aq --filter "label=$RUN_LABEL")"
    # shellcheck disable=SC2086
    [[ -z "$ids" ]] || docker rm -f $ids >/dev/null 2>&1 || true
    ids="$(docker network ls -q --filter "label=$RUN_LABEL")"
    # shellcheck disable=SC2086
    [[ -z "$ids" ]] || docker network rm $ids >/dev/null 2>&1 || true
}

# Stop the running test jobs and exit; the EXIT trap then removes the containers.
on_interrupt() {
    trap - INT TERM
    echo
    echo "Interrupted." >&2
    jobs -p | xargs kill 2>/dev/null
    exit 130
}

# Number of environments to test at once: ROBO_DOCKER_JOBS, or half the CPUs
# available to Docker.
docker_jobs() {
    if [[ -n "${ROBO_DOCKER_JOBS:-}" ]]; then
        echo "$ROBO_DOCKER_JOBS"
        return
    fi
    local cpus
    cpus="$(docker info --format '{{.NCPU}}' 2>/dev/null || echo 2)"
    echo $(( cpus / 2 > 0 ? cpus / 2 : 1 ))
}

# run_matrix ENTRY...
#
# Runs the function run_entry (defined by the calling script) once for each
# matrix entry, as "run_entry ENTRY IMAGE". Entries end in ":<php version>",
# e.g. "drush:11:8.4". Up to $(docker_jobs) entries run in parallel,
# each with its own log file; with ROBO_DOCKER_JOBS=1 the output is streamed.
run_matrix() {
    local entries=("$@")
    local jobs total=${#entries[@]}
    jobs="$(docker_jobs)"

    trap cleanup_docker_resources EXIT
    trap on_interrupt INT TERM

    # Build each PHP image once, before starting any test.
    local entry php built=" "
    for entry in "${entries[@]}"; do
        php="${entry##*:}"
        if [[ "$built" != *" $php "* ]]; then
            build_php_image "$php" >/dev/null
            built="$built$php "
        fi
    done

    local failed=() start=$SECONDS
    if [[ "$jobs" -le 1 || "$total" -le 1 ]]; then
        for entry in "${entries[@]}"; do
            log "$entry"
            run_entry "$entry" "robo-docker-test:php${entry##*:}" || failed+=("$entry")
        done
    else
        local logdir i running done_count=0 status elapsed
        logdir="$(mktemp -d "${TMPDIR:-/tmp}/robo-docker-tests.XXXXXX")"
        log "Testing ${total} environments, ${jobs} at a time (logs: $logdir)"
        local started=() finished=()
        for ((i = 0; i < total; i++)); do finished[i]=0; done

        i=0
        while [[ $done_count -lt $total ]]; do
            # Start new jobs while there are free slots.
            running=$(( i - done_count ))
            while [[ $i -lt $total && $running -lt $jobs ]]; do
                entry="${entries[i]}"
                started[i]=$SECONDS
                (
                    status=0
                    run_entry "$entry" "robo-docker-test:php${entry##*:}" || status=$?
                    echo "$status" > "$logdir/$i.status"
                ) > "$logdir/$i.log" 2>&1 < /dev/null &
                i=$(( i + 1 ))
                running=$(( running + 1 ))
            done

            sleep 1

            # Report jobs that finished.
            local j
            for ((j = 0; j < i; j++)); do
                [[ ${finished[j]} -eq 0 && -f "$logdir/$j.status" ]] || continue
                finished[j]=1
                done_count=$(( done_count + 1 ))
                status="$(cat "$logdir/$j.status")"
                elapsed=$(( SECONDS - started[j] ))
                if [[ "$status" == "0" ]]; then
                    printf '\033[1;32mPASS\033[0m %s (%ss) [%s/%s]\n' "${entries[j]}" "$elapsed" "$done_count" "$total"
                else
                    printf '\033[1;31mFAIL\033[0m %s (%ss) [%s/%s], log: %s\n' "${entries[j]}" "$elapsed" "$done_count" "$total" "$logdir/$j.log"
                    tail -n 40 "$logdir/$j.log" | sed 's/^/    /'
                    failed+=("${entries[j]}")
                fi
            done
        done
        wait
        [[ ${#failed[@]} -gt 0 ]] || rm -rf "$logdir"
    fi

    if [[ ${#failed[@]} -gt 0 ]]; then
        printf '\033[1;31mDocker tests failed for:\033[0m\n' >&2
        printf '  - %s\n' "${failed[@]}" >&2
        return 1
    fi
    log "All Docker tests passed (${total} environments, $(( SECONDS - start ))s)"
}
