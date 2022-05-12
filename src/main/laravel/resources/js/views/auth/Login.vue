<template>
    <div class="px-2 mx-auto max-w-sm shadow-md">
        <div class="text-center font-bold text-3xl py-4 bg-secondary text-white rounded-t-md">R-Kare</div>
        <main class="px-6 py-7 bg-white rounded-b-md">
            <form @submit.prevent="submit">
                <p style="color: #303233;">Benvenuto, effettua il login.</p>
                <div class="mt-3">
                    <input
                        class="input w-full focus:outline-none"
                        type="email"
                        v-model="form.email"
                        placeholder="Email"
                        required
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
                <div class="mt-7 flex items-center">
                    <input id="remember-me" type="checkbox" class="h-4 w-4" :value="true" />
                    <label
                        for="remember-me"
                        class="ml-3 text-sm"
                        style="color: #5C6F82"
                    >Recordati di me</label>
                </div>
                <div class="mt-6">
                    <button
                        type="submit"
                        class="w-full py-3 bg-primary text-white font-bold font-sm rounded-md"
                        :disabled="isLoading"
                    >Accedi</button>
                </div>
            </form>
            <div class="text-center mt-5">
                <a href="/forget/password" class="text-primary font-semibold">Password dimenticata?</a>
            </div>
        </main>
    </div>
</template>

<script>
import useAuth from "@/compositions/auth";
import { defineComponent, reactive } from "@vue/composition-api";

export default defineComponent({
    name: 'LoginPage',
    setup() {
        const { login, errors, isLoading } = useAuth();

        const form = reactive({
            email: '',
            password: '',
        });

        function submit() {
            login(form);
        }

        return {
            form,
            errors,
            isLoading,
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
