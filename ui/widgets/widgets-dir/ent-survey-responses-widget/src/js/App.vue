<template>
  <router-view></router-view>
</template>


<script>
import { defineComponent } from "@vue/composition-api";
import useAuth from "./compositions/auth";
import router from "./router";
import { useAuthStore } from "./stores/auth";
import { useSettingsStore } from "./stores/settings";

export default defineComponent({
  name: "App",
  setup(_, { root }) {
    const { getUser } = useAuth();
    const settingsStore = useSettingsStore();
    const authStore = useAuthStore();

    authStore.$subscribe((_, state) => {
      if (state.user) {
        router.push(root.$route.query.redirect || "surveys");
      }
    });

    settingsStore.loading = true;
    getUser().finally(() => {
      settingsStore.loading = false;
    });

    return {
      settingsStore,
    };
  },
});
</script>
