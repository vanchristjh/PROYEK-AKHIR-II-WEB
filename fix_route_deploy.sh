#!/bin/bash

# Fix Laravel Route Conflicts in Docker

echo "===== Laravel Route Conflict Fixer ====="
echo "This script helps diagnose and fix route naming conflicts"

# Run inside the container
docker-compose exec app bash -c "
    echo 'Checking route conflicts...'
    
    # Check for routes using the same name
    php artisan route:list | grep -E 'guru\.assignments\.update' || echo 'No matching routes found'
    
    # Suggest manual check
    echo 'You may need to check the following files for route conflicts:'
    echo ' - routes/web.php'
    echo ' - routes/api.php'
    echo ' - app/Providers/RouteServiceProvider.php'
    
    echo 'Look for duplicate route names, especially guru.assignments.update'
    echo 'Try bypassing the route cache with:'
    echo '  php artisan optimize:clear'
"

echo "===== Route Analysis Complete ====="
echo "Try deploying again after fixing route conflicts"
