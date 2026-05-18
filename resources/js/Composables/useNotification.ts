import { ref } from 'vue';

export interface Notification {
    id: string;
    type: 'success' | 'error' | 'info' | 'warning';
    message: string;
    duration?: number;
}

const notifications = ref<Notification[]>([]);
let notificationId = 0;

export function useNotification() {
    const addNotification = (message: string, type: 'success' | 'error' | 'info' | 'warning' = 'info', duration = 4000) => {
        const id = `notification-${notificationId++}`;
        const notification: Notification = {
            id,
            type,
            message,
            duration
        };

        notifications.value.push(notification);

        if (duration > 0) {
            setTimeout(() => {
                removeNotification(id);
            }, duration);
        }

        return id;
    };

    const removeNotification = (id: string) => {
        const index = notifications.value.findIndex(n => n.id === id);
        if (index > -1) {
            notifications.value.splice(index, 1);
        }
    };

    const success = (message: string, duration?: number) => addNotification(message, 'success', duration);
    const error = (message: string, duration?: number) => addNotification(message, 'error', duration);
    const info = (message: string, duration?: number) => addNotification(message, 'info', duration);
    const warning = (message: string, duration?: number) => addNotification(message, 'warning', duration);

    return {
        notifications,
        addNotification,
        removeNotification,
        success,
        error,
        info,
        warning
    };
}
