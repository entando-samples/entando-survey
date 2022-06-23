<template>
    <div v-show="!settingsStore.loading" class="auth-wrapper font-sans">
        <div class="pt-36">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import { useSettingsStore } from "@/stores/settings";
import { defineComponent } from "@vue/composition-api";

export default defineComponent({
    name: "AuthLayout",
    setup() {
        const settingsStore = useSettingsStore();

        return {
            settingsStore
        }
    }
});
</script>
