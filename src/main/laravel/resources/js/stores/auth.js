import { defineStore } from "pinia";
import client from "@/http";
export const useAuthStore = defineStore("user", {
    state: () => {
        return { user: null };
    },
    getters: {
        isAuthenticated(state) {
            return state.user ? true : false;
        },
    },
    actions: {
        async logout() {
            console.log("logging out....");
            await client.post("logout");
            window.location.reload();
            
        },
    },
});
