<template>
    <div class="px-2 mx-auto max-w-sm shadow-md">
        <div class="text-center font-bold text-3xl py-4 bg-secondary text-white rounded-t-md">R-Kare</div>
        <main class="px-6 py-7 bg-white rounded-b-md">
            <form @submit.prevent="submit">
                <p style="color: #303233;">Reset password</p>
                <div class="mt-3">
                    <input
                        class="input w-full focus:outline-none"
                        type="email"
                        :value="form.email"
                        placeholder="Email"
                        readonly
                    />
                    <small class="block text-red-600" v-if="errors.email">{{ errors.email }}</small>
                </div>
                <div class="mt-6">
                    <input
                        class="input w-full focus:outline-none"
                        type="password"
                        v-model="form.password"
                        placeholder="Password"
                        required
                    />
                    <small class="block text-red-600" v-if="errors.password">{{ errors.password }}</small>
                </div>
                <div class="mt-6">
                    <input
                        class="input w-full focus:outline-none"
                        type="password"
                        v-model="form.password_confirmation"
                        placeholder="Password Confirmation"
                        required
                    />
                </div>
                <div class="mt-6">
                    <button
                        type="submit"
                        class="w-full py-3 bg-primary text-white font-bold font-sm rounded-md"
                        :disabled="isLoading"
                    >Submit</button>
                </div>
            </form>
        </main>
    </div>
</template>

<script>
import useAuth from "@/compositions/auth";
import router from "@/router";
import { defineComponent, reactive } from "@vue/composition-api";
import Vue from "vue";

export default defineComponent({
    name: 'PasswordResetPage',
    setup(_, { root }) {
        const { errors, isLoading, resetPassword } = useAuth();

        const form = reactive({
            email: root.$route.query.email,
            token: root.$route.query.token,
            password: null,
            password_confirmation: null
        })

        function submit() {
            resetPassword(form)
                .then(res => {
                    Vue.toasted.success(res.message);
                    router.push({ name: 'login' })
                })
                .catch((err) => {
                    Vue.toasted.error(err?.response?.data?.message || err.message)
                })
        }

        return {
            form,
            isLoading,
            errors,
            submit,
        }
    }
});
</script>

<style lang="scss">
.input {
    height: 55px;
    border: none;
    border-bottom: 1px solid #5c6f82;
    border-radius: 0;
    color: #5c6f82;
    font-weight: 600;
}
</style>
