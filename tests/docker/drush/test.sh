#!/usr/bin/env bash
# Runs inside the test container: installs Drupal $DRUPAL_VERSION with Drush 13
# (using the local Robo checkout) and runs a few Drush commands against it.

# shellcheck source=SCRIPTDIR/../container-lib.sh
source /tests/container-lib.sh

: "${DRUPAL_VERSION:?}"
: "${DRUSH_VERSION:=13}"

export_robo

log "Creating Drupal $DRUPAL_VERSION project"
composer create-project --quiet --no-install "drupal/recommended-project:^$DRUPAL_VERSION" /work/site
cd /work/site || exit 1
use_local_robo

log "Installing Drush $DRUSH_VERSION with the local Robo checkout"
composer require --quiet --update-with-all-dependencies \
    "drush/drush:^$DRUSH_VERSION" "consolidation/robo:$ROBO_TEST_VERSION"
assert_local_robo

drush() {
    vendor/bin/drush --no-interaction "$@"
}

log "drush site:install"
drush site:install minimal --yes \
    --db-url=sqlite://localhost/sites/default/files/.ht.sqlite \
    --site-name="Robo test" --account-pass=admin

log "drush status"
bootstrap="$(drush status --field=bootstrap)"
[[ "$bootstrap" == "Successful" ]] || fail "Drupal bootstrap: '$bootstrap'"
drush status --fields=drupal-version,drush-version,php-version

log "drush cache:rebuild"
drush cache:rebuild

log "drush php:eval"
site_name="$(drush php:eval 'echo \Drupal::config("system.site")->get("name");')"
[[ "$site_name" == "Robo test" ]] || fail "site name: '$site_name'"

log "OK: Drupal $(drush status --field=drupal-version), Drush $(drush status --field=drush-version)"
