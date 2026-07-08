@auth
    <div class="ltn__drop-menu freshbox-notification-menu">
        <ul>
            <li>
                <a href="#"><i class="far fa-bell"></i></a>
                @if (($clientUnreadNotificationsCount ?? 0) > 0)
                    <sup>{{ $clientUnreadNotificationsCount }}</sup>
                @endif
                <ul>
                    @forelse (($clientNotifications ?? collect()) as $notification)
                        <li>
                            <a href="{{ route('client.notifications.read', $notification) }}" class="freshbox-notification-item {{ $notification->is_read ? 'is-read' : 'is-unread' }}">
                                @if ($notification->image)
                                    <img src="{{ \Illuminate\Support\Str::startsWith($notification->image, ['http://', 'https://']) ? $notification->image : asset('storage/' . $notification->image) }}" alt="{{ $notification->title ?? 'Thông báo' }}">
                                @endif
                                <span>
                                    <strong>{{ $notification->title ?? 'Thông báo mới' }}</strong>
                                    <small>{{ \Illuminate\Support\Str::limit($notification->message, 70) }}</small>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li><span class="freshbox-notification-empty">Chưa có thông báo mới</span></li>
                    @endforelse
                </ul>
            </li>
        </ul>
    </div>
@endauth
