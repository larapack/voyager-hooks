<?php

namespace Larapack\VoyagerHooks\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Larapack\Hooks\Hooks;

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
        $name = $this->request->get('name');
        $this->hooks->install($name);

        return $this->redirect("Hook [{$name}] have been installed!");
    }

    public function uninstall($name)
    {
        $this->hooks->uninstall($name);

        return $this->redirect("Hook [{$name}] have been uninstalled!");
    }

    public function update($name)
    {
        $this->hooks->update($name);

        return $this->redirect("Hook [{$name}] have been updated!");
    }

    public function enable($name)
    {
        $this->hooks->enable($name);

        return $this->redirect("Hook [{$name}] have been enabled!");
    }

    public function disable($name)
    {
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
