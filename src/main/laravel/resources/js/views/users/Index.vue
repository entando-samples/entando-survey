<template>
  <div>
    <b-page-header title="User management"></b-page-header>

    <div class="flex items-start space-x-7">
      <div class="w-2/5 space-y-2">
        <p class="text-sm">Ricerca per nome</p>
        <div class="border border-primary py-1 px-2 bg-white flex items-center">
          <input
            class="w-full bg-transparent"
            type="search"
            v-model="filters.search"
          />
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-primary mx-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
        </div>
      </div>
      <div class="w-3/5 space-y-2">
        <p class="text-sm">Filtra per</p>
        <div class="flex items-start space-x-3">
          <div class="w-1/3">
            <b-select
              v-model="filters.roles"
              placeholder="Role"
              :options="roles"
              label="label"
              :reduce="(item) => item.value"
              :multiple="true"
            ></b-select>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5 flex flex-row-reverse">
      <router-link to="/users/create">
        <b-button class="py-2">Crea un nuovo user</b-button>
      </router-link>
    </div>

    <b-block class="mt-5">
      <table class="table-auto w-full text-sm">
        <thead class="uppercase text-white bg-ternary">
          <tr class="font-semibold text-left">
            <td class="p-2">name</td>
            <td class="p-2">email</td>
            <td class="p-2">Last updated</td>
            <td class="p-2">role</td>
            <td></td>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading.users">
            <td colspan="10">
              <div class="text-gray-600 text-center py-3">Loading users...</div>
            </td>
          </tr>
          <tr v-else-if="!users.data.length">
            <td colspan="10">
              <div class="text-gray-600 text-center py-3">
                No users available
              </div>
            </td>
          </tr>
          <tr
            v-else
            v-for="(user, index) in users.data"
            :key="user.id"
            :class="{
              'bg-white': Number(index % 2) !== 0,
              'bg-gray-100': Number(index % 2) === 0,
            }"
          >
            <td class="px-2 py-3">
              <div class="text-primary font-semibold">{{ user.name }}</div>
            </td>
            <td class="px-2 py-3">
              <div>{{ user.email }}</div>
            </td>
            <td class="px-2 py-3">
              <div>{{  user.updated_at.human }}</div>
            </td>
            <td class="px-2 py-3">
              <div>{{  user.role || "---" }}</div>
            </td>
            <td>
              <router-link
                :to="{ name: 'users.edit', params: { id: user.id } }"
                class="inline-block bg-primary rounded-sm p-1 text-center"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-white"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                  />
                </svg>
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </b-block>
    <div class="mt-7">
      <div class="flex items-center justify-between gap-x-10">
        <div>
          <b-button>Exporta CSV</b-button>
        </div>
        <div>
          <b-paginator
            :data="users.meta"
            @change="onPageChange"
            v-if="users.meta"
          ></b-paginator>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  computed,
  defineComponent,
  reactive,
  watch,
} from "@vue/composition-api";
import useUsers from "@/compositions/users";
import Vue from "vue";
import debounce from "lodash/debounce";

export default defineComponent({
  name: "UserIndexPage",
  setup() {
    const { roles: rawRoles, loading, users, getUsers, getRoles } = useUsers();

    const roles = computed(() =>
      Object.entries(rawRoles.value).map(([value, label]) => ({ label, value }))
    );

    const filters = reactive({
      search: null,
      roles: [],
    });

    console.log(users);

    fetchUsers();
    getRoles();

    watch(filters, debounce(onFiltersChange, 500));

    function fetchUsers() {
      getUsers(arguments[0]).catch((err) => {
        Vue.toasted.error(err.response?.data?.message || err.message);
      });
    }

    function onPageChange({ page }) {
      fetchUsers({ page, ...filters });
    }

    function onFiltersChange() {
      fetchUsers({ page: 1, ...filters });
    }

    return {
      rawRoles,
      roles,
      loading,
      filters,
      users,
      onPageChange,
    };
  },
});
</script>
