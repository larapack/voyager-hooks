<?php

namespace Larapack\VoyagerHooks\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Larapack\Hooks\Hooks;
use TCG\Voyager\Facades\Voyager;

class HooksController extends Controller
{
    protected $hooks;
    protected $request;

    public function __construct(Hooks $hooks, Request $request)
    {
        $this->hooks = $hooks;
        $this->request = $request;
    }

    public function index()
    {
        // Check permission
        Voyager::canOrFail('browse_hooks');

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

    public function install()
    {
        // Check permission
        Voyager::canOrFail('browse_hooks');

        $name = $this->request->get('name');
        $this->hooks->install($name);

        return $this->redirect("Hook [{$name}] have been installed!");
    }

    public function uninstall($name)
    {
        // Check permission
        Voyager::canOrFail('browse_hooks');

        $this->hooks->uninstall($name);

        return $this->redirect("Hook [{$name}] have been uninstalled!");
    }

    public function update($name)
    {
        // Check permission
        Voyager::canOrFail('browse_hooks');

        $this->hooks->update($name);

        return $this->redirect("Hook [{$name}] have been updated!");
    }

    public function enable($name)
    {
        // Check permission
        Voyager::canOrFail('browse_hooks');

        $this->hooks->enable($name);

        return $this->redirect("Hook [{$name}] have been enabled!");
    }

    public function disable($name)
    {
        // Check permission
        Voyager::canOrFail('browse_hooks');

        $this->hooks->disable($name);

        return $this->redirect("Hook [{$name}] have been disabled!");
    }

    protected function redirect($message)
    {
        $referer = $this->request->server('HTTP_REFERER');
        $location = head(explode('?', $referer));
        header('Location: '.$location.'?message='.urlencode($message));
        exit;
    }
}
