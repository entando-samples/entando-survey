import { defineStore } from "pinia";
// import client from "@/http";
export const useAuthStore = defineStore("user", {
    state: () => {
        return { user: null };
    },
    getters: {
        isAuthenticated(state) {
            return !!state.user;
        },
    },
    actions: {
        //TODO: change this to keyclock logout
        async logout() {
            console.log("logging out....");
            window.location.reload();
        },
    },
});
