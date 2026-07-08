<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class DefaultClientData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = null;
        $clientNotifications = collect();
        $clientUnreadNotificationsCount = 0;

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
                $user = Auth::user();
                if ($user) {
                    $notificationQuery = Notification::where('user_id', $user->id)
                        ->whereIn('type', ['customer_coupon', 'product_promotion'])
                        ->where(function ($query) {
                            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                        });

                    $clientUnreadNotificationsCount = (clone $notificationQuery)
                        ->where('is_read', 0)
                        ->count();

                    $clientNotifications = $notificationQuery
                        ->latest('created_at')
                        ->take(8)
                        ->get();
                }
            } catch (\Throwable $e) {
                $user = null;
                $clientNotifications = collect();
                $clientUnreadNotificationsCount = 0;
            }
        }

        View::share([
            'userClient' => $user,
            'clientNotifications' => $clientNotifications,
            'clientUnreadNotificationsCount' => $clientUnreadNotificationsCount,
        ]);

        return $next($request);
    }
}
