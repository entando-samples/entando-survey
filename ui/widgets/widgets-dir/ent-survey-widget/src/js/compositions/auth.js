import client from "@/http";
import Validator from "@/utils/validator";
import Vue from "vue";
import { ref } from "@vue/composition-api";
import { useAuthStore } from "@/stores/auth";

export default function useAuth() {
    const isLoading = ref(false);
    const authStore = useAuthStore();
    const validator = Validator({});
    function login({ email, password }) {

        validator.reset();

        isLoading.value = true;

        return client
            .post("login", {
                email,
                password,
            })
            .then((res) => {
                authStore.user = res.data.data.user;
            })
            .catch((err) => {
                if (validator.isValidationError(err)) {
                    validator.adaptErr(err);
                }

                if (err?.response?.status === 403) {
                    Vue.set(
                        validator.errors,
                        "email",
                        err.response.data.message
                    );
                }

                throw err;
            })
            .finally(() => {
                isLoading.value = false;
            });
    }

    async function getUser() {
        await client.get("csrf-cookie", {
            baseURL: window.laravel.appUrl,
        });

        try {
            const response = await client.get("/backend/me");
            authStore.user = response.data.data.user;
        } catch (error) {
            // pass
        }
    }

    function resetPassword(data) {
        isLoading.value = true;
        validator.reset();

        return client.post('reset', data)
            .then(res => {
                return res.data;
            })
            .catch((err) => {
                if (validator.isValidationError(err)) {
                    validator.adaptErr(err);
                }

                throw err;
            })
            .finally(() => isLoading.value = false)
    }

    function forgetPassword(data) {
        validator.reset();
        return client.post('/forget/password', data)
            .then((res) => {
                return res.data;

            })
            .catch((err) => {
                if (validator.isValidationError(err)) {
                    validator.adaptErr(err);
                }
                throw err;
            })
            .finally(() => isLoading.value = false);
    }

    return {
        errors: validator.errors,
        isLoading,
        login,
        getUser,
        resetPassword,
        forgetPassword,
    };
}
