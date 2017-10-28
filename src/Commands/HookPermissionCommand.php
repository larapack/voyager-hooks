<?php

namespace Larapack\VoyagerHooks\Commands;

use Illuminate\Console\Command;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class HookPermissionCommand extends Command
{
    protected $signature = 'voyager:hook-permission';

    protected $description = 'Install the browse hooks permission';

    public function __construct()
    {
        parent::__construct();
    }

    public function fire()
    {
        return $this->handle();
    }

    public function handle()
    {
        $permission = Permission::firstOrCreate([
            'key'        => 'browse_hooks',
            'table_name' => null,
        ]);

        $role = Role::where('name', 'admin')->firstOrFail();

        $role->permissions()->attach($permission->id);
    }
}
