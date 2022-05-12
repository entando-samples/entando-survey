import { defineStore } from "pinia";

export const useSettingsStore = defineStore("settings", {
    state: () => {
        return {
            loading: false,
            message_count: 0
        };
    },
    getters: {},
});
