<?php
/**
 * This script helps identify and fix route naming conflicts in Laravel
 * 
 * Usage: php fix_route_conflicts.php
 */

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Analyzing routes for naming conflicts...\n";

// Get all routes
$routes = Route::getRoutes()->getRoutesByName();

// Group routes by name to find duplicates
$duplicateNames = [];
foreach ($routes as $name => $route) {
    $baseName = preg_replace('/(\.create|\.store|\.show|\.edit|\.update|\.destroy)$/', '', $name);
    $duplicateNames[$baseName][] = $name;
}

// Find conflicting routes (those with same base name but different methods)
$conflicts = array_filter($duplicateNames, function ($group) {
    return count($group) > 1;
});

if (empty($conflicts)) {
    echo "No route naming conflicts found.\n";
    exit(0);
}

echo "Found " . count($conflicts) . " route name conflicts:\n";

foreach ($conflicts as $baseName => $group) {
    echo "- Base name: {$baseName}\n";
    echo "  Conflicting routes: " . implode(', ', $group) . "\n";
}

echo "\nChecking for route [guru/assignments/{assignment}] conflict...\n";

// Find routes with specific URI pattern
$assignmentRoutes = [];
foreach ($routes as $name => $route) {
    if (strpos($route->uri(), 'guru/assignments/{assignment}') !== false) {
        $assignmentRoutes[$name] = [
            'uri' => $route->uri(),
            'methods' => $route->methods()
        ];
    }
}

if (count($assignmentRoutes) > 0) {
    echo "Found routes matching 'guru/assignments/{assignment}':\n";
    foreach ($assignmentRoutes as $name => $info) {
        echo "- Name: {$name}, URI: {$info['uri']}, Methods: " . implode(',', $info['methods']) . "\n";
    }
    
    echo "\nSuggestion: Check RouteServiceProvider and route files for duplicate route names.\n";
    echo "Specifically look for 'guru.assignments.update' name being assigned to multiple routes.\n";
}

echo "\nRoute analysis complete.\n";
