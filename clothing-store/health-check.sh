#!/usr/bin/env bash
# Health check script for clothing-store
# Can be used by monitoring systems (UptimeRobot, BetterStack, etc.)

set -euo pipefail

APP_DIR="/var/www/clothing-store/current"
URL="${1:-http://localhost}"

cd "${APP_DIR}" || exit 1

# Check Laravel health endpoint
check_endpoint() {
    local endpoint="$1"
    local description="$2"
    local full_url="${URL}${endpoint}"
    
    response=$(curl -sf -o /dev/null -w "%{http_code}" "${full_url}" --max-time 10 2>/dev/null || echo "000")
    
    if [ "${response}" = "200" ]; then
        echo "✓ ${description} (${endpoint}) - HTTP ${response}"
        return 0
    else
        echo "✗ ${description} (${endpoint}) - HTTP ${response}"
        return 1
    fi
}

echo "=== Health Check for $(basename $(pwd)) ==="
echo "Time: $(date)"
echo "URL: ${URL}"
echo ""

# Core checks
check_endpoint "/up" "Laravel Health" || exit 1
check_endpoint "/" "Homepage" || exit 1
check_endpoint "/shop" "Shop Page" || exit 1

# Check queue
if php artisan queue:work --once --timeout=5 --stop-when-empty 2>/dev/null | grep -q "No jobs"; then
    echo "✓ Queue Worker - No pending jobs (healthy)"
else
    echo "⚠ Queue Worker - Has pending jobs or error"
fi

# Check disk space
disk_usage=$(df -h . | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "${disk_usage}" -lt 85 ]; then
    echo "✓ Disk Usage - ${disk_usage}%"
else
    echo "⚠ Disk Usage - ${disk_usage}% (above 85%)"
fi

# Check database connection
if php artisan db:show 2>/dev/null | grep -q "Connected"; then
    echo "✓ Database Connection"
else
    echo "✗ Database Connection"
fi

# Check Redis (if configured)
if php -r "try { \$r = new Redis(); \$r->connect('127.0.0.1', 6379); echo 'OK'; } catch (Exception \$e) { echo 'FAIL'; }" 2>/dev/null | grep -q "OK"; then
    echo "✓ Redis Connection"
else
    echo "⚠ Redis Connection (not configured or unavailable)"
fi

echo ""
echo "=== Health Check Complete ==="