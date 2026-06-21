self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        e.waitUntil(self.Notification.showNotification(msg.title, {
            body: msg.body,
            icon: msg.icon,
            actions: msg.actions,
            data: msg.data
        }));
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    // Redireciona para o painel se a notificação tiver URL
    const targetUrl = event.notification.data ? event.notification.data.url : '/admin';
    
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(function(clientList) {
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
