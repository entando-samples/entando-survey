<template>
  <div>
    <b-page-header title="Crea un nuovo user"></b-page-header>

    <b-block class="mt-3">
      <form @submit.prevent="submit">
        <div>
          <label for="name" class="font-semibold">Name</label>
          <div class="mt-1">
            <input
              id="name"
              type="text"
              v-model="form.name"
              disabled
              class="
                w-full
                border border-primary
                px-2
                py-1
                focus-within:outline-none
              "
            />
            <err-msg :message="errors.name"></err-msg>
          </div>
        </div>
        <div class="mt-3">
          <label for="email" class="font-semibold">Email</label>
          <div class="mt-1">
            <input
              id="email"
              type="text"
              v-model="form.email"
              disabled
              class="
                w-full
                border border-primary
                px-2
                py-1
                focus-within:outline-none
              "
            />
            <err-msg :message="errors.email"></err-msg>
          </div>
        </div>

        <template>
          <div class="mt-3">
            <label for="current_password" class="font-semibold"
              >Current Password</label
            >
            <div class="mt-1">
              <input
                id="current_password"
                type="password"
                v-model="form.current_password"
                autocomplete="false"
                class="
                  w-full
                  border border-primary
                  px-2
                  py-1
                  focus-within:outline-none
                "
              />
              <err-msg :message="errors.current_password"></err-msg>
            </div>
          </div>
          <div class="mt-3">
            <label for="password" class="font-semibold">New Password</label>
            <div class="mt-1">
              <input
                id="password"
                type="password"
                v-model="form.password"
                class="
                  w-full
                  border border-primary
                  px-2
                  py-1
                  focus-within:outline-none
                "
              />

              <err-msg :message="errors.password"></err-msg>
            </div>
          </div>
          <div class="mt-3">
            <label for="password_confirmation" class="font-semibold"
              >Password Confirmation</label
            >
            <div class="mt-1">
              <input
                id="password_confirmation"
                type="password"
                v-model="form.password_confirmation"
                class="
                  w-full
                  border border-primary
                  px-2
                  py-1
                  focus-within:outline-none
                "
              />
              <err-msg :message="errors.password_confirmation"></err-msg>

            </div>
          </div>
        </template>
        <b-button type="submit" class="mt-5">Change Password</b-button>
      </form>
    </b-block>
  </div>
</template>


<script>
import useAuth from "@/compositions/auth";
import useUser from "@/compositions/users";
import router from "@/router";
import { defineComponent, reactive, ref } from "@vue/composition-api";
import Vue from "vue";
import { useAuthStore } from "@/stores/auth";
export default defineComponent({
  name: "ProfilePage",
  setup(_, { root }) {
    const { updateProfile,errors } = useUser();
    const authStore = useAuthStore();
    let userData = ref(authStore.user);
    
    const form = reactive({
      email: userData.value.email,
      name: userData.value.name,
      password: null,
      password_confirmation: null,
      current_password: "",
    });

    console.log(form);

    function submit() {
      updateProfile(form)
        .then((res) => {
          Vue.toasted.success(res.message);
          router.push({ name: "login" });
        })
        .catch((err) => {
          Vue.toasted.error(err?.response?.data?.message || err.message);
        });
    }

    return {
      form,
      errors,
      submit,
    };
  },
});
</script>
