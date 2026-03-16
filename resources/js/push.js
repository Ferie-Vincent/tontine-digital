/**
 * Push notifications management for DIGI-TONTINE PWA
 */

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function getBaseUrl() {
    const meta = document.querySelector('meta[name="base-url"]');
    if (meta) {
        return meta.content.replace(/\/$/, '');
    }
    return '';
}

async function getRegistration() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return null;
    }
    return navigator.serviceWorker.ready;
}

window.isPushSupported = function() {
    return 'serviceWorker' in navigator && 'PushManager' in window;
};

window.isPushSubscribed = async function() {
    const registration = await getRegistration();
    if (!registration) return false;
    const subscription = await registration.pushManager.getSubscription();
    return !!subscription;
};

window.subscribeToPush = async function() {
    try {
        const registration = await getRegistration();
        if (!registration) {
            throw new Error('Service Worker non disponible');
        }

        const vapidKey = document.querySelector('meta[name="vapid-public-key"]')?.content;
        if (!vapidKey) {
            throw new Error('Cle VAPID non trouvee');
        }

        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidKey),
        });

        const baseUrl = getBaseUrl();
        const response = await fetch(baseUrl + '/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(subscription.toJSON()),
        });

        if (!response.ok) throw new Error('Erreur serveur');

        return true;
    } catch (error) {
        console.warn('Push subscription failed:', error.message);
        return false;
    }
};

window.unsubscribeFromPush = async function() {
    try {
        const registration = await getRegistration();
        if (!registration) return true;

        const subscription = await registration.pushManager.getSubscription();
        if (!subscription) return true;

        const baseUrl = getBaseUrl();
        await fetch(baseUrl + '/push/unsubscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ endpoint: subscription.endpoint }),
        });

        await subscription.unsubscribe();
        return true;
    } catch (error) {
        console.warn('Push unsubscribe failed:', error.message);
        return false;
    }
};
