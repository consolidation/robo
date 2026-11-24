#!/usr/bin/env bash
# Drush suite for tests/docker/run.sh: Drupal with Drush 13 and the local Robo
# checkout. Entries are DRUPAL:PHP.

# shellcheck disable=SC2034 # read by run.sh
drush_matrix=(
    10:8.3 10:8.4 10:8.5 10:8.6
    11:8.3 11:8.4 11:8.5 11:8.6
)

drush_setup() {
    :
}

# drush_run_entry DRUPAL:PHP IMAGE
drush_run_entry() {
    # shellcheck disable=SC2046
    docker run $(docker_run_args) -e "DRUPAL_VERSION=${1%%:*}" "$2" bash /tests/drush/test.sh
}
