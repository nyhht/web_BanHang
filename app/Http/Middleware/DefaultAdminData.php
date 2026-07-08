<?php

namespace App\Http\Middleware;

use App\Models\Contact;
use App\Models\Notification;
use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DefaultAdminData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $messages = [];
        $notifications = [];
        $user = null;

        $dbAvailable = true;

        if (config('database.default') === 'mysql') {
            $dbHost = config('database.connections.mysql.host', '127.0.0.1');
            $dbPort = config('database.connections.mysql.port', 3306);
            $dbSocket = @fsockopen($dbHost, (int) $dbPort, $errno, $errstr, 1);
            $dbAvailable = (bool) $dbSocket;

            if ($dbSocket) {
                fclose($dbSocket);
            }
        }

        if ($dbAvailable) {
            try {
                $user = Auth::guard('admin')->user();

                if ($user) {
                    $messages = Contact::where('is_replied', 0)->latest()->get();
                    $notifications = Notification::where('is_read', 0)
                        ->whereIn('type', ['order', 'contact', 'wishlist'])
                        ->latest('created_at')
                        ->get();
                }
            } catch (\Throwable $e) {
                $messages = [];
                $notifications = [];
                $user = null;
            }
        }

        View::share([
            'messages' => $messages,
            'notifications' => $notifications,
            'userAdmin' => $user
        ]);
        return $next($request);
    }
}
