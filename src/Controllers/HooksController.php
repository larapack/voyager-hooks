<?php

namespace Larapack\VoyagerHooks\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Larapack\Hooks\Hooks;

class HooksController extends Controller
{
    protected $hooks;

    public function __construct(Hooks $hooks)
    {
        $this->hooks = $hooks;
    }

    public function index()
    {
        return view('voyager-hooks::browse', [
            'hooks' => $this->hooks->hooks(),
        ]);
    }

    public function install(Request $request)
    {
        $this->hooks->install($request->get('name'));

        return redirect(route('voyager.hooks'));
    }

    public function uninstall($name)
    {
        $this->hooks->uninstall($name);

        return redirect(route('voyager.hooks'));
    }

    public function enable($name)
    {
        $this->hooks->enable($name);

        return redirect(route('voyager.hooks'));
    }

    public function disable($name)
    {
        $this->hooks->disable($name);

        return redirect(route('voyager.hooks'));
    }
}
