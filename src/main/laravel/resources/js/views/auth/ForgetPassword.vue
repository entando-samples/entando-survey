<template>
  <div class="px-2 mx-auto max-w-sm shadow-md">
    <div
      class="
        text-center
        font-bold
        text-3xl
        py-4
        bg-secondary
        text-white
        rounded-t-md
      "
    >
      R-Kare
    </div>
    <main class="px-6 py-7 bg-white rounded-b-md">
      <form @submit.prevent="submit">
        <p style="color: #303233">
          Please enter your email to search for your account.
        </p>
        <div class="mt-3">
          <input
            class="input w-full focus:outline-none"
            type="email"
            v-model="form.email"
            placeholder="Email"
          />
          <small class="block text-yellow-600" v-if="form.loading"
            >Loading......</small
          >
          <small class="block text-red-600" v-if="errors.email">{{
            errors.email
          }}</small>
          <small class="block text-blue-600" v-if="successMessage">{{
            successMessage
          }}</small>
        </div>

        <div class="mt-6">
          <button
            type="submit"
            class="
              w-full
              py-3
              bg-primary
              text-white
              font-bold font-sm
              rounded-md
            "
            :disabled="isLoading"
          >
            Submit
          </button>
        </div>
      </form>
    </main>
  </div>
</template>

<script>
import useAuth from "@/compositions/auth";
import { defineComponent, reactive, ref } from "@vue/composition-api";
import Vue from "vue";

export default defineComponent({
  name: "PasswordResetPage",
  setup(_, { root }) {
    const { errors, isLoading, forgetPassword } = useAuth();

    const successMessage = ref("");
    const form = reactive({
      email: "",
      loading: false,
    });

    function submit() {
      form.loading = true;
      successMessage.value = "";
      forgetPassword(form)
        .then((res) => {
          successMessage.value = res.message;
          form.loading = false;
        })
        .catch((err) => {
          form.loading = false;
          Vue.toasted.error(err?.response?.data?.message || err.message);
        });
    }

    return {
      form,
      isLoading,
      errors,
      submit,
      successMessage,
    };
  },
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
