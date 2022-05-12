<template>
  <div>
    <b-page-header title="Messaggi" />

    <div class="flex items-start space-x-7">
      <div class="w-2/5 space-y-2">
        <p class="text-sm">Ricerca per nome</p>
        <div class="border border-primary py-1 px-2 bg-white flex items-center">
          <input
            class="w-full bg-transparent"
            v-model="filterParameters.search"
            type="text"
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
      <div class="w-1/5 space-y-2">
        <p class="text-sm">Filtra per oggetto</p>
        <b-select
          v-model="filterParameters.topics"
          placeholder="Scegli l’oggetto"
          :searchable="true"
          :options="topics"
          :reduce="(item) => item.id"
          label="label"
          :multiple="true"
        ></b-select>
      </div>
      <div class="flex-grow flex flex-row-reverse">
        <b-button class="mt-6">
          <router-link :to="{ name: 'messages.create' }"
            >Send Message</router-link
          >
        </b-button>
      </div>
    </div>

    <div class="mt-5">
      <div class="grid grid-cols-9 gap-x-10">
        <div class="col-span-2">
          <div class="bg-white text-sm rounded-md">
            <router-link class="flex justify-between py-3 px-2" to="inbound">
              <p class="flex items-center">
                <svg
                  class="w-6 h-6 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                  />
                </svg>
                <span>In Arrivo</span>
              </p>

              <span>({{ counts.inbound }})</span>
            </router-link>
            <router-link class="flex justify-between py-3 px-2" to="read">
              <p class="flex items-center">
                <svg
                  class="w-6 h-6 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  />
                </svg>

                <span>Letti</span>
              </p>

              <span>({{ counts.read }})</span>
            </router-link>
            <router-link
              class="flex justify-between py-3 px-2"
              to="require-call"
            >
              <p class="flex items-center">
                <svg
                  class="w-6 h-6 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                  />
                </svg>
                <span>Contatto Telefonico</span>
              </p>

              <span>({{ counts.require_call }})</span>
            </router-link>
            <router-link class="flex justify-between py-3 px-2" to="archived">
              <p class="flex items-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6 mr-2"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 13h6m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
                <span>Archivio Risposte</span>
              </p>

              <span>({{ counts.archived }})</span>
            </router-link>
            <router-link class="flex justify-between py-3 px-2" to="outbound">
              <p class="flex items-center">
                <svg
                  class="w-6 h-6 mr-2"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                  />
                </svg>
                <span>Outbound</span>
              </p>

              <span>({{ counts.outbound }})</span>
            </router-link>
          </div>
        </div>
        <div class="col-span-7">
          <router-view :filters="filterParameters"></router-view>
          <div class="mt-7">
            <portal-target name="message-pagination"></portal-target>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  defineComponent,
  onMounted,
  reactive,
  watchEffect,
} from "@vue/composition-api";
import useMessages from "@/compositions/messages";

export default defineComponent({
  name: "MessageIndexPage",
  setup(_, { root }) {
    const { topics, counts, getParameters } = useMessages();

    const filterParameters = reactive({
      search: "",
      topics: [],
    });

    onMounted(() => {
      getParameters();
    });

    watchEffect(() => {
      localStorage.setItem("last-active-message-list", root.$route.name);
    });

    return {
      topics,
      counts,
      filterParameters,
    };
  },
});
</script>


<style scoped>
.active--nav {
  @apply bg-blue-200 bg-opacity-70 text-primary;
}
</style>
