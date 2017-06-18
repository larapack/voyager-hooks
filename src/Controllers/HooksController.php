<?php

namespace Larapack\VoyagerHooks\Controllers;

use Carbon\Carbon;
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
        $lastUpdated = $this->hooks->getLastRemoteCheck();

        if (is_null($lastUpdated)) {
            $lastUpdated = Carbon::now();
            $this->hooks->setLastRemoteCheck($lastUpdated);
            $this->hooks->remakeJson();
        }

        return view('voyager-hooks::browse', [
            'hooks'              => $this->hooks->hooks(),
            'daysSinceLastCheck' => $lastUpdated->diffInDays(Carbon::now()),
        ]);
    }

    public function install(Request $request)
    {
        $name = $request->get('name');
        $this->hooks->install($name);

        return redirect(route('voyager.hooks'))->with([
            'message'    => 'Hook '.$name.' was Installed',
            'alert-type' => 'success',
        ]);
    }

    public function uninstall($name)
    {
        $this->hooks->uninstall($name);

        return redirect(route('voyager.hooks'))->with([
            'message'    => 'Hook '.$name.' was Uninstalled',
            'alert-type' => 'success',
        ]);
    }

    public function update($name)
    {
        $this->hooks->update($name);

        return redirect(route('voyager.hooks'))->with([
            'message'    => 'Hook '.$name.' was Updated',
            'alert-type' => 'success',
        ]);
    }

    public function enable($name)
    {
        $this->hooks->enable($name);

        return redirect(route('voyager.hooks'))->with([
            'message'    => 'Hook '.$name.' is Enabled',
            'alert-type' => 'success',
        ]);
    }

    public function disable($name)
    {
        $this->hooks->disable($name);

        return redirect(route('voyager.hooks'))->with([
            'message'    => 'Hook '.$name.' was Disabled',
            'alert-type' => 'success',
        ]);
    }

    public function cacheRefresh()
    {
        $this->hooks->refreshCache();

        return redirect(route('voyager.hooks'))->with([
            'message'    => 'Hooks Cache Updated',
            'alert-type' => 'success',
        ]);
    }
}
