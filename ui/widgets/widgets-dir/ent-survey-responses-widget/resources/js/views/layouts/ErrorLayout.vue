<template>
    <div v-show="!settingsStore.loading">
        <router-view></router-view>
    </div>
</template>


<script>
import { useSettingsStore } from "@/stores/settings";
import { defineComponent } from "@vue/composition-api";

export default defineComponent({
    name: "ErrorLayout",
    setup() {
        const settingsStore = useSettingsStore();

        return {
            settingsStore
        }
    }
});
</script>
