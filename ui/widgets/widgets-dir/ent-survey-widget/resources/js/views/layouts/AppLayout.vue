<template>
    <div class="app-wrapper text-base-black" v-show="!settingsStore.loading">
        <div class="flex flex-col w-full" style="margin: 0 auto;">
            <aside class="fixed hidden md:block md:w-sidebar h-full bg-ternary z-20">
                <div class="h-header px-4 flex items-center bg-secondary text-white">
                    <span class="font-bold text-4xl">R-kare</span>
                </div>
                <Navigation />
            </aside>
            <Header></Header>
            <div class="md:pl-sidebar" style="background: rgb(249 248 248);">
                <main
                    class="container mx-auto px-4 pt-3 pb-10 text-gray-700"
                    style="min-height: calc(100vh - 3.4rem)"
                >
                    <router-view></router-view>
                </main>
                <footer class="py-2 mx-auto bg-white z-10">
                    <div class="container mx-auto px-4 text-gray-500">R-Kare</div>
                </footer>
            </div>
        </div>
        <portal-target name="modals" multiple></portal-target>
    </div>
</template>

<script>
import { useSettingsStore } from "@/stores/settings";
import { defineComponent, onMounted, onUnmounted } from "@vue/composition-api";
import Navigation from "./App/Navigation.vue";
import Header from "./App/Header.vue";

export default defineComponent({
    name: "AppLayout",
    components: {
        Navigation,
        Header
    },
    setup() {
        const settingsStore = useSettingsStore();

        onMounted(() => {})

        onUnmounted(() => {})

        return {
            settingsStore
        }
    }

});
</script>
