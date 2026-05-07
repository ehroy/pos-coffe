import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useOrderStore = defineStore('orders', () => {
    const activeOrders = ref([]);

    function setOrders(orders) {
        activeOrders.value = orders;
    }

    function addOrUpdateOrder(order) {
        const index = activeOrders.value.findIndex((o) => o.id === order.id);
        if (index !== -1) {
            activeOrders.value[index] = { ...activeOrders.value[index], ...order };
        } else {
            activeOrders.value.unshift(order);
        }
    }

    function removeOrder(orderId) {
        activeOrders.value = activeOrders.value.filter((o) => o.id !== orderId);
    }

    function updateOrderStatus(orderId, status) {
        const order = activeOrders.value.find((o) => o.id === orderId);
        if (order) {
            order.status = status;
        }

        if (['completed', 'cancelled'].includes(status)) {
            removeOrder(orderId);
        }
    }

    return {
        activeOrders,
        setOrders,
        addOrUpdateOrder,
        removeOrder,
        updateOrderStatus,
    };
});
