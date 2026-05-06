import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
    }),
    
    getters: {
        isOwner: (state) => state.user?.role === 'owner',
        isAdmin: (state) => state.user?.role === 'admin',
        isCashier: (state) => state.user?.role === 'cashier',
        isKitchen: (state) => state.user?.role === 'kitchen',
        isWarehouse: (state) => state.user?.role === 'warehouse',
        hasRole: (state) => (roles) => {
            if (Array.isArray(roles)) {
                return roles.includes(state.user?.role);
            }
            return state.user?.role === roles;
        },
    },
    
    actions: {
        setUser(user) {
            this.user = user;
        },
        clearUser() {
            this.user = null;
        },
    },
});
