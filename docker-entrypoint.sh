#!/bin/sh
set -e

# Wait for DB if needed (optional but good)
# sleep 5

# Run migrations
php yii migrate --interactive=0

# Start Apache
exec apache2-foreground
