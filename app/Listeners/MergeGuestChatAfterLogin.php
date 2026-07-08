<?php

namespace App\Listeners;

use App\Models\ChatMessage;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MergeGuestChatAfterLogin
{
    /**
     * Laravel 11+ (and 12) uses auto-discovery events & listeners
     * Laravel will automatically scan and register this listener using auto-discovery.
     */

    /**
     * Merge guest chat messages to user after login.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event): void
    {
        $guestToken = request()->cookie('chat_token');

        if ($guestToken) {
            ChatMessage::where('guest_token', $guestToken)
                ->update([
                    'user_id' => $event->user->id,
                    'guest_token' => null
                ]);

            cookie()->queue(cookie()->forget('chat_token'));
        }
    }
}
