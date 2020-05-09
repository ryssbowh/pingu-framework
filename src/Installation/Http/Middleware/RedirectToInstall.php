<?php 

namespace PinguFramework\Installation\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class RedirectToInstall
{
	/**
     * Redirects to /install if the storage/installed file doesnt exist
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return redirect()->route('install');
    }
}