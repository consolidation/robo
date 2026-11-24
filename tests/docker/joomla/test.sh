#!/usr/bin/env bash
# Runs inside the test container:
#   1. runs JoRobo's own PHPUnit suite against the local Robo checkout, set up
#      the same way as JoRobo's CI (with joomla-extensions/weblinks as the
#      extension under test);
#   2. builds the weblinks package with JoRobo and installs it into a fresh
#      Joomla $JOOMLA_VERSION site.

# shellcheck source=SCRIPTDIR/../container-lib.sh
source /tests/container-lib.sh

: "${JOOMLA_VERSION:?}"
: "${DB_HOST:?}"
: "${DB_PASS:?}"
: "${JOROBO_REF:=latest}"
: "${WEBLINKS_REF:=5.x-dev}"

export_robo

# --- JoRobo test suite -------------------------------------------------------

if [[ "$JOROBO_REF" == "latest" ]]; then
    JOROBO_REF="$(git ls-remote --tags --refs https://github.com/joomla-projects/jorobo.git \
        | sed 's|.*refs/tags/||' | grep -E '^v?[0-9]+\.[0-9]+\.[0-9]+$' | sort -V | tail -1)"
fi

log "Cloning JoRobo $JOROBO_REF and weblinks $WEBLINKS_REF"
git clone --quiet --depth 1 --branch "$JOROBO_REF" https://github.com/joomla-projects/jorobo.git /work/jorobo
cd /work/jorobo || exit 1
git clone --quiet --depth 1 --branch "$WEBLINKS_REF" https://github.com/joomla-extensions/weblinks.git test-weblinks
# Same setup as JoRobo's CI (.github/workflows/ci.yml).
rm jorobo.dist.ini
cp test-weblinks/jorobo.dist.ini jorobo.ini

log "Installing JoRobo's dependencies with the local Robo checkout"
use_local_robo
composer update --quiet --with-dependencies consolidation/robo
assert_local_robo

log "Running JoRobo's PHPUnit suite"
# Show details, so that new warnings/deprecations caused by Robo are visible.
vendor/bin/phpunit --display-warnings --display-deprecations tests

# --- Build weblinks and install it into Joomla -------------------------------

if [[ "$JOOMLA_VERSION" =~ ^[0-9]+$ ]]; then
    log "Looking up the latest Joomla $JOOMLA_VERSION release"
    # shellcheck disable=SC2016 # PHP code, not shell
    JOOMLA_VERSION="$(curl -fsSL https://downloads.joomla.org/api/v1/latest/cms \
        | php -r '$major = $argv[1];
            foreach (json_decode(stream_get_contents(STDIN), true)["branches"] as $b) {
                if ($b["branch"] === "Joomla! $major") { echo $b["version"]; }
            }' "$JOOMLA_VERSION")"
    [[ -n "$JOOMLA_VERSION" ]] || fail "could not determine the latest Joomla release"
fi

log "Downloading Joomla $JOOMLA_VERSION"
mkdir -p /work/joomla
curl -fsSL "https://github.com/joomla/joomla-cms/releases/download/$JOOMLA_VERSION/Joomla_$JOOMLA_VERSION-Stable-Full_Package.tar.gz" \
    | tar -xz -C /work/joomla

log "Installing Joomla"
php /work/joomla/installation/joomla.php install --no-interaction \
    --site-name="Robo test" \
    --admin-user="Robo Admin" --admin-username=admin \
    --admin-password=robo-test-password --admin-email=admin@example.com \
    --db-type=mysqli --db-host="$DB_HOST" --db-user=root --db-pass="$DB_PASS" \
    --db-name=joomla --db-prefix=robo_ --db-encryption=0

joomla() {
    php /work/joomla/cli/joomla.php --no-interaction "$@"
}

log "robo build (weblinks)"
rm -rf test-weblinks/dist
vendor/bin/robo build --base=/work/jorobo/test-weblinks
package="$(find test-weblinks/dist -maxdepth 1 -name 'pkg-weblinks-*.zip' -o -maxdepth 1 -name 'pkg_weblinks*.zip' | head -1)"
[[ -n "$package" ]] || { ls -la test-weblinks/dist; fail "robo build did not create a weblinks package"; }
log "Built $package"

log "Installing weblinks into Joomla"
joomla extension:install --path="/work/jorobo/$package"
joomla extension:list --type=component | grep -q com_weblinks \
    || fail "com_weblinks is not listed as an installed component"

log "OK: Joomla $JOOMLA_VERSION, JoRobo $JOROBO_REF, weblinks $WEBLINKS_REF"
