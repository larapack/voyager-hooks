<?php

return [
    // Disables the hooks in production to avoid conflicts
    // in the composer.json file
    'disable' => true,

    // Adds the appropriate routing to each hook
    // on installation.
    // If this is set to false, hooks can only be installed/removed
    // via the CLI.
    'add-route' => true,
];
