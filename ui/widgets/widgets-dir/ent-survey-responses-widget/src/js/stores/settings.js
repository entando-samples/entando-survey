import { defineStore } from "pinia";

export const useSettingsStore = defineStore("settings", {
    state: () => {
        return {
            loading: false,
        };
    },
    getters: {},
});
