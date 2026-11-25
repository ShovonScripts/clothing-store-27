#!/usr/bin/env bash
# deploy.sh - Zero-downtime deployment script for clothing-store
# Usage: ./deploy.sh [environment]
#   environment: production (default) or staging
# Run from project root on the server.

set -euo pipefail

ENVIRONMENT="${1:-production}"
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="/var/www/clothing-store"
RELEASE_DIR="${APP_DIR}/releases"
CURRENT_DIR="${APP_DIR}/current"
SHARED_DIR="${APP_DIR}/shared"
TIMESTAMP=$(date +"%Y%m%d%H%M%S")
RELEASE_PATH="${RELEASE_DIR}/${TIMESTAMP}"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $*"
}

warn() {
    echo -e "${YELLOW}[$(date '+%Y-%m-%d %H:%M:%S')] WARNING:${NC} $*"
}

error() {
    echo -e "${RED}[$(date '+%Y-%m-%d %H:%M:%S')] ERROR:${NC} $*" >&2
}

# Pre-flight checks
check_requirements() {
    log "Checking requirements..."
    command -v php >/dev/null 2>&1 || { error "php not found"; exit 1; }
    command -v composer >/dev/null 2>&1 || { error "composer not found"; exit 1; }
    command -v node >/dev/null 2>&1 || { error "node not found"; exit 1; }
    command -v npm >/dev/null 2>&1 || { error "npm not found"; exit 1; }
    command -v git >/dev/null 2>&1 || { error "git not found"; exit 1; }
    
    # Check PHP version
    php_version=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
    if (( $(echo "$php_version < 8.3" | bc -l) )); then
        error "PHP 8.3+ required, found $php_version"
        exit 1
    fi
    
    log "Requirements OK"
}

# Create directory structure
setup_directories() {
    log "Setting up directories..."
    mkdir -p "${RELEASE_DIR}"
    mkdir -p "${SHARED_DIR}/storage"
    mkdir -p "${SHARED_DIR}/bootstrap/cache"
    mkdir -p "${SHARED_DIR}/.env"
}

# Clone or update repository
update_code() {
    log "Updating code..."
    if [ -d "${RELEASE_PATH}/.git" ]; then
        cd "${RELEASE_PATH}"
        git fetch origin
        git reset --hard origin/main
    else
        git clone --depth 1 --branch main https://github.com/your-org/clothing-store.git "${RELEASE_PATH}"
        cd "${RELEASE_PATH}"
    fi
}

# Install PHP dependencies
install_php_deps() {
    log "Installing PHP dependencies..."
    cd "${RELEASE_PATH}"
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
}

# Build frontend assets
build_assets() {
    log "Building frontend assets..."
    cd "${RELEASE_PATH}"
    npm ci --prefer-offline --no-audit --no-fund
    npm run build
}

# Configure environment
configure_env() {
    log "Configuring environment..."
    if [ ! -f "${SHARED_DIR}/.env" ]; then
        cp "${RELEASE_PATH}/.env.example" "${SHARED_DIR}/.env"
        warn "Created .env from example - EDIT ${SHARED_DIR}/.env with production values!"
    fi
    
    # Link .env
    ln -nfs "${SHARED_DIR}/.env" "${RELEASE_PATH}/.env"
    
    # Generate app key if not set
    if ! grep -q "^APP_KEY=" "${SHARED_DIR}/.env" || grep -q "^APP_KEY=$" "${SHARED_DIR}/.env"; then
        cd "${RELEASE_PATH}"
        php artisan key:generate --force
    fi
}

# Link shared directories
link_shared() {
    log "Linking shared directories..."
    # Remove release directories that will be shared
    rm -rf "${RELEASE_PATH}/storage"
    rm -rf "${RELEASE_PATH}/bootstrap/cache"
    rm -rf "${RELEASE_PATH}/public/storage"
    
    # Create symlinks
    ln -nfs "${SHARED_DIR}/storage" "${RELEASE_PATH}/storage"
    ln -nfs "${SHARED_DIR}/bootstrap/cache" "${RELEASE_PATH}/bootstrap/cache"
    ln -nfs "${SHARED_DIR}/storage/app/public" "${RELEASE_PATH}/public/storage"
}

# Run database migrations
run_migrations() {
    log "Running database migrations..."
    cd "${RELEASE_PATH}"
    php artisan migrate --force --no-interaction
}

# Optimize Laravel
optimize() {
    log "Optimizing Laravel..."
    cd "${RELEASE_PATH}"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    php artisan filament:cache-components
}

# Switch to new release
switch_release() {
    log "Switching to new release..."
    # Atomic switch
    ln -nfs "${RELEASE_PATH}" "${CURRENT_DIR}"
    
    # Reload PHP-FPM (adjust service name as needed)
    if systemctl is-active --quiet php8.3-fpm; then
        log "Reloading PHP-FPM..."
        sudo systemctl reload php8.3-fpm
    elif systemctl is-active --quiet php-fpm; then
        sudo systemctl reload php-fpm
    else
        warn "Could not detect PHP-FPM service, skipping reload"
    fi
    
    # Restart queue workers
    log "Restarting queue workers..."
    cd "${CURRENT_DIR}"
    php artisan queue:restart
}

# Health check
health_check() {
    log "Running health check..."
    sleep 3
    local url="http://localhost"
    if [ "${ENVIRONMENT}" = "staging" ]; then
        url="https://staging.yourdomain.com"
    fi
    
    for i in {1..10}; do
        if curl -sf "${url}/up" >/dev/null; then
            log "Health check passed!"
            return 0
        fi
        sleep 2
    done
    
    error "Health check failed after 10 attempts"
    return 1
}

# Cleanup old releases (keep last 5)
cleanup() {
    log "Cleaning up old releases..."
    cd "${RELEASE_DIR}"
    ls -1t | tail -n +6 | xargs -r rm -rf
}

# Rollback function
rollback() {
    log "Rolling back to previous release..."
    cd "${RELEASE_DIR}"
    local previous=$(ls -1t | head -n 2 | tail -n 1)
    if [ -n "${previous}" ]; then
        ln -nfs "${RELEASE_DIR}/${previous}" "${CURRENT_DIR}"
        cd "${CURRENT_DIR}"
        php artisan queue:restart
        if systemctl is-active --quiet php8.3-fpm; then
            sudo systemctl reload php8.3-fpm
        fi
        log "Rollback complete to ${previous}"
    else
        error "No previous release to rollback to"
        exit 1
    fi
}

# Main deployment flow
main() {
    log "Starting deployment to ${ENVIRONMENT}..."
    log "Project: ${PROJECT_DIR}"
    log "Release: ${RELEASE_PATH}"
    
    check_requirements
    setup_directories
    update_code
    install_php_deps
    build_assets
    configure_env
    link_shared
    run_migrations
    optimize
    switch_release
    
    if health_check; then
        cleanup
        log "Deployment successful!"
    else
        error "Deployment failed health check"
        rollback
        exit 1
    fi
}

# Handle arguments
case "${1:-}" in
    rollback)
        rollback
        ;;
    *)
        main
        ;;
esac