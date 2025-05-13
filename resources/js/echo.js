import Echo from 'laravel-echo';

import Notify from "simple-notify";
import Pusher from 'pusher-js';
window.Pusher = Pusher;
window.Notify = Notify;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

const playNotificationSound = () => {
    const audio = new Audio('/audio/notif.wav');
    audio.play().catch(error => {
        console.log("Playback failed. Waiting for user interaction.");
        document.addEventListener("click", () => audio.play(), { once: true });
    });
};

const notify = (e) => {
    new window.Notify ({
        status: 'info',
        title: 'Notifikasi Baru',
        text:`<div style="margin-left: 12px; font-size: 0.875rem; font-weight: normal;">
                        <div style="font-size: 0.875rem; font-weight: 600; color: #111827;">${e.sender_name}</div>
                        <div style="font-size: 0.875rem; font-weight: normal;">${e.sender_status} | ${e.message}</div>
                        <span style="font-size: 0.75rem; font-weight: 500; color: #2563eb;">Beberapa detik yang lalu.</span>
                    </div>`,
        effect: 'fade',
        speed: 300,
        showIcon: true,
        showCloseButton: true,
        autoclose: true,
        autotimeout: 5000,
        notificationsGap: null,
        notificationsPadding: null,
        type: 'outline',
        position: 'right top',
        customWrapper: '',
    });
}

if(document.querySelector('meta[name="user-id"]')){
    window.Echo.private(`App.Models.User.${document.querySelector('meta[name="user-id"]').content}`)
        .notification((notification) => {
            playNotificationSound();
            notify(notification);
        });
}
window.notify = notify;
window.playNotificationSound = playNotificationSound;
