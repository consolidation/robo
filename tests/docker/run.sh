#!/usr/bin/env bash
# Runs the Docker integration tests: downstream projects (Drush, JoRobo/Joomla)
# tested against the local Robo checkout. All selected environments share one
# pool of parallel jobs. See docs/testing.md.
#
# Usage: tests/docker/run.sh [SUITE [VERSION:PHP ...] | SUITE:VERSION:PHP ...]
#   tests/docker/run.sh                     all suites, default matrices
#   tests/docker/run.sh drush               the Drush suite's default matrix
#   tests/docker/run.sh drush 11:8.4 10:8.3 selected Drush environments
#   tests/docker/run.sh joomla:6:8.4 drush  mix of both
#   tests/docker/run.sh --list [...]        print the selected entries as a JSON
#                                           array, without running them
#
# Suites: joomla, drush (each defined in tests/docker/<suite>/suite.sh).
#
# Environment: ROBO_DOCKER_TESTS=skip      skip these tests
#              ROBO_DOCKER_TESTS=required  fail instead of skipping without Docker
#              ROBO_DOCKER_JOBS=N          environments to test in parallel
#                                          (default: half of Docker's CPUs)
#              JOROBO_REF=0.14.2           JoRobo tag/branch (default: latest tag)
#              WEBLINKS_REF=5.x-dev        weblinks branch used by the Joomla suite

# shellcheck source=SCRIPTDIR/lib.sh
source "$(dirname "$0")/lib.sh"

# The slowest suite comes first, so that it doesn't end up running alone at the end.
SUITES=(joomla drush)
for suite in "${SUITES[@]}"; do
    # shellcheck source=/dev/null
    source "$DOCKER_TEST_DIR/$suite/suite.sh"
done

is_suite() {
    [[ " ${SUITES[*]} " == *" $1 "* ]]
}

usage_error() {
    echo "$1" >&2
    sed -n '6,15p' "$0" | sed 's/^# \{0,1\}//' >&2
    exit 2
}

# Expand the arguments into SUITE:VERSION:PHP entries.
entries=()
add_defaults() {
    local matrix entry
    matrix="${1}_matrix[@]"
    for entry in "${!matrix}"; do
        entries+=("$1:$entry")
    done
}
current=""
pending_defaults=""
list_only=""
for arg in "$@"; do
    if [[ "$arg" == "--list" ]]; then
        list_only=1
    elif is_suite "$arg"; then
        [[ -z "$pending_defaults" ]] || add_defaults "$pending_defaults"
        current="$arg"
        pending_defaults="$arg"
    elif [[ "$arg" == *:*:* ]] && is_suite "${arg%%:*}"; then
        entries+=("$arg")
    elif [[ "$arg" == *:* && -n "$current" ]]; then
        entries+=("$current:$arg")
        pending_defaults=""
    else
        usage_error "Unknown suite or environment: $arg"
    fi
done
[[ -z "$pending_defaults" ]] || add_defaults "$pending_defaults"
if [[ ${#entries[@]} -eq 0 && -z "$pending_defaults" ]]; then
    for suite in "${SUITES[@]}"; do
        add_defaults "$suite"
    done
fi

if [[ -n "$list_only" ]]; then
    printf '%s\n' "${entries[@]}" | php -r 'echo json_encode(file("php://stdin", FILE_IGNORE_NEW_LINES)), "\n";'
    exit 0
fi

skip_if_disabled
require_docker

# Set up each suite that has entries (e.g. pull service images).
for suite in "${SUITES[@]}"; do
    [[ " ${entries[*]} " == *" $suite:"* ]] && "${suite}_setup"
done

# run_entry SUITE:VERSION:PHP IMAGE
run_entry() {
    "${1%%:*}_run_entry" "${1#*:}" "$2"
}

run_matrix "${entries[@]}"
