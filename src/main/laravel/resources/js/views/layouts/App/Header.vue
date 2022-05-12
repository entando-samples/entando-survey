<template>
  <header
    class="md:pl-sidebar h-header flex items-center shadow-md w-full z-10"
  >
    <div class="w-full container mx-auto px-4">
      <div class="w-full flex items-center justify-between">
        <portal-target name="header"></portal-target>

        <div class="flex items-center space-x-5 text-primary">
          <div class="flex items-center">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-7 w-7 fill-current"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                clip-rule="evenodd"
              />
            </svg>
            <!--drop down -->
            <div>
              <v-dropdown placement="right">
                <!-- Button content -->
                <template v-slot:button>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 fill-current"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </template>

                <!-- Opened dropdown content -->
                <template v-slot:content>
                  <a
                    class="
                      flex
                      w-full
                      justify-between
                      items-center
                      rounded
                      px-2
                      py-1
                      my-1
                      hover:bg-indigo-600 hover:text-white
                    "
                    href="/change/profile"
                    >Change Password</a
                  >
                  <hr />

                  <span
                    @click="logout"
                    class="
                      flex
                      w-full
                      justify-between
                      items-center
                      rounded
                      px-2
                      py-1
                      my-1
                      hover:bg-red-600 hover:text-white
                      cursor-pointer
                    "
                    >Logout</span
                  >
                </template>
              </v-dropdown>
            </div>
            <!--drop down ends -->
          </div>
          <div
            class="
              border-2 border-primary
              rounded
              px-2
              flex
              items-center
              space-x-2
            "
            style="padding: 3px 0.5rem"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
            <span class="text-sm font-bold">2</span>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { defineComponent } from "@vue/composition-api";
import DropDown from "../DropDown.vue";
import { useAuthStore } from "@/stores/auth";
export default defineComponent({
  components: {
    "v-dropdown": DropDown,
  },
  name: "Header",
  setup() {
    const authStore = useAuthStore();

    function logout() {
      authStore.logout().then((res) => {
        Vue.toasted.success(res.message);
        router.push({ name: "login" });
      });
    }
    return {
      logout,
    };
  },
});
</script>
