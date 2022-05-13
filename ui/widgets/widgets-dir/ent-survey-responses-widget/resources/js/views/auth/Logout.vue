<template>
    <span>Logging out...</span>
</template>

<script>
import client from "@/http";
import router from "@/router";
import { useAuthStore } from "@/stores/auth";
import { defineComponent, onMounted } from "@vue/composition-api";

export default defineComponent({
    name: "Logout",
    setup(_, { root }) {
        const authStore = useAuthStore();

        onMounted(() => {
            client.post("logout")
                .finally(() => {
                    authStore.logout();
                    router.push({
                        name: "login",
                        query: { redirect: root.$route.query.redirect },
                    })
                });
        })
    }
})
</script>
