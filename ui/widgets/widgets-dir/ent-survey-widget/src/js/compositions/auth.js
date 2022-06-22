import client from "@/http";
import Validator from "@/utils/validator";
import Vue from "vue";
import { ref } from "@vue/composition-api";
import { useAuthStore } from "@/stores/auth";

export default function useAuth() {
    const isLoading = ref(false);
    const authStore = useAuthStore();
    const validator = Validator({});

    async function getUser() {
        const response = await client.get("csrf-cookie", {
            baseURL: window.laravel.appUrl,
        });
        console.log(response);

        try {
            authStore.user = entando.keycloak.idTokenParsed;
        } catch (error) {
            // pass
        }
    }


    return {
        errors: validator.errors,
        isLoading,
        getUser,
    };
}
