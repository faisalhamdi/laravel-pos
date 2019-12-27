<?php

namespace App\Http\Middleware;

use Spatie\Permission\Models\Permission;

use Closure;

class checkrole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // dd($request);
        // $users = User::orderBy('created_at', 'DESC')->paginate(10);
        $permission = Permission::findOrFail(1);
        // return $permission;
        foreach ($permission as $role) {
            if ($role != 'admin') {
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
