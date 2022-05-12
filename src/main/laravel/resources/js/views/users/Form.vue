<template>
  <div>
    <b-page-header title="Crea un nuovo user"></b-page-header>

    <b-block class="mt-3">
      <form @submit.prevent="save">
        <div>
          <label for="name" class="font-semibold">Name</label>
          <div class="mt-1">
            <input
              id="name"
              type="text"
              v-model="form.name"
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
        <div class="mt-3">
          <label for="role" class="font-semibold">Role</label>
          <div class="mt-1">
            <b-select
              class="mt-1"
              placeholder="select a role"
              :options="roles"
              label="label"
              :reduce="(item) => item.value"
              v-model="form.role"
            ></b-select>
            <err-msg :message="errors.role"></err-msg>
          </div>
        </div>
        <template v-if="!isCreating">
          <div class="mt-3">
            <label for="password" class="font-semibold">Password</label>
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
              <small class="block text-gray-500"
                >(Leave empty if you dont want to change)</small
              >
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
            </div>
          </div>

          <div class="mt-3">
            <input
              id="email_report"
              type="checkbox"
              v-model="form.email_report"
              class="border border-primary px-2 py-1 focus-within:outline-none"
            />
            <label for="password_confirmation" class="font-semibold"
              >Send daily email report</label
            >
          </div>
        </template>
        <b-button type="submit" class="mt-5" :disabled="loading.userForm"
          >Save</b-button
        >
      </form>
    </b-block>
  </div>
</template>


<script>
import useUsers from "@/compositions/users";
import router from "@/router";
import { computed, defineComponent, reactive } from "@vue/composition-api";
import Vue from "vue";

export default defineComponent({
  name: "UserFormPage",
  setup(_, { root }) {
    const {
      errors,
      loading,
      roles: rawRoles,
      user: form,
      saveUser,
      getRoles,
      getUser,
      updateUser,
    } = useUsers();

    const isCreating = computed(() => root.$route.name === "users.create");

    const roles = computed(() =>
      Object.entries(rawRoles.value).map(([value, label]) => ({ label, value }))
    );

    onCreated();

    function onCreated() {
      getRoles();

      if (!isCreating.value) {
        getUser(root.$route.params.id).catch((err) => {
          if (err.response?.status === 404) {
            router.back();
          }

          Vue.toasted.error(err?.response?.data?.message || err.message);
        });
      }
    }

    async function save() {
      try {
        if (isCreating.value) {
          await saveUser(form);
          Vue.toasted.success("saved successfully");
        } else {
          await updateUser(root.$route.params.id, form);
          Vue.toasted.success("updated successfully");
        }

        router.push({
          name: "users.index",
        });
      } catch (err) {
        Vue.toasted.error(err?.response?.data?.message || err.message);
      }
    }

    return {
      roles,
      errors,
      loading,
      form,
      isCreating,
      save,
    };
  },
});
</script>
