<?php
require_once 'vendor/autoload.php';
\ = require_once 'bootstrap/app.php';
\->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get authenticated user
\ = \Illuminate\Support\Facades\Auth::user();
if (\) {
    echo \
Authenticated
user:
\$user->name
ID:
\$user->id
\n\;
    echo \Role
ID:
\$user->role_id
\n\;
    \ = \->role;
    if (\) {
        echo \Role:
\$role->name
Slug:
\$role->slug
\n\;
    } else {
        echo \No
role
found
for
the
user!\n\;
    }
} else {
    echo \No
authenticated
user!\n\;
    
    // List all roles
    echo \Available
roles:\n\;
    \ = \App\Models\Role::all();
    foreach (\ as \) {
        echo \-
ID:
\$role->id
Name:
\$role->name
Slug:
\$role->slug
\n\;
    }
    
    // List all users with guru role
    echo \\nUsers
with
guru
role:\n\;
    \ = \App\Models\User::whereHas('role', function(\) {
        \->where('slug', 'guru');
    })->get();
    
    foreach (\ as \) {
        echo \-
ID:
\$user->id
Name:
\$user->name
Username:
\$user->username
\n\;
    }
}

