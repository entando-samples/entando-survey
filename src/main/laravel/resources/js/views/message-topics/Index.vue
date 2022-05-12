<template>
  <div>
    <b-page-header title="Message Topic" />

    <div class="flex items-start space-x-7">
      <div class="w-2/5 space-y-2">
        <p class="text-sm">Ricerca per nome</p>
        <div class="border border-primary py-1 px-2 bg-white flex items-center">
          <input class="w-full bg-transparent" v-model="search" type="text" />
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
    </div>

    <div class="flex flex-row-reverse">
      <router-link to="/message-topics/create">
        <b-button class="py-2">Aggiungi</b-button>
      </router-link>
    </div>
    <b-block class="mt-5">
      <table class="table-auto w-full text-sm">
        <thead class="uppercase text-white bg-ternary">
          <tr class="font-semibold text-left">
            <td class="p-2">Title</td>
            <td></td>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="5">
              <div class="text-gray-600 text-center py-3">
                Loading topics....
              </div>
            </td>
          </tr>
          <tr v-else-if="!(topics || []).length">
            <td colspan="5">
              <div class="text-gray-600 text-center py-3">
                No topics available
              </div>
            </td>
          </tr>
          <template v-else>
            <tr
              v-for="(topic, index) in topics"
              :key="topic.id"
              :class="index % 2 === 0 ? 'bg-gray-100' : ''"
              :data-row="topic.id"
            >
              <td class="px-2 py-3">
                <div class="text-primary font-semibold">{{ topic.title }}</div>
              </td>

              <td class="px-2 py-3 flex justify-center">
                <div class="flex items-center space-x-2">
                  <div
                    class="
                      flex
                      space-x-3
                      text-white
                      bg-primary
                      rounded
                      p-1
                      border-2 border-primary
                    "
                  >
                    <router-link
                      :to="{
                        name: 'messageTopics.edit',
                        params: { id: topic.id },
                      }"
                      class="text-center"
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
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                        />
                      </svg>
                    </router-link>
                  </div>

                  <button
                    @click="showDeleteModal(topic.id)"
                    class="
                      p-1
                      text-primary
                      hover:text-white
                      border-2 border-primary
                      rounded
                      hover:bg-primary
                    "
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
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </b-block>

    <b-modal
      title="Confirm Delete"
      v-if="modals.delete.show"
      @close="modals.delete.show = false"
    >
      <h3>
        Are you sure? (
        {{ messagesCount.loading ? "(loading...)" : messagesCount.value }}
        messages are related to this topic.)
      </h3>

      <template #footer>
        <div class="flex gap-x-4 flex-row-reverse">
          <b-button
            :disabled="messagesCount.loading"
            @click="deleteItem($event, modals.delete.id)"
            >Confirm</b-button
          >
          <b-button variant="primary-alt" @click="modals.delete.show = false"
            >Indietro</b-button
          >
        </div>
      </template>
    </b-modal>
  </div>
</template>

<script>
import Vue from "vue";
import {
  defineComponent,
  onMounted,
  ref,
  watch,
  reactive,
} from "@vue/composition-api";
import debounce from "lodash/debounce";
import useMessageTopics from "@/compositions/messageTopics";

export default defineComponent({
  name: "MessageTopicIndexPage",
  setup() {
    const modals = reactive({
      delete: {
        show: false,
        id: null,
      },
    });

    const {
      topics,
      loading,
      messagesCount,
      deleteTopic,
      getTopics,
      getRelatedMessagesCount,
    } = useMessageTopics();

    const search = ref("");

    onMounted(() => {
      getTopics();
    });

    function showDeleteModal(id) {
      getRelatedMessagesCount(id);
      modals.delete.id = id;
      modals.delete.show = true;
    }

    async function deleteItem(event, id) {
      const btn = event.target;
      const row = document.querySelector(`[data-row="${id}"]`);

      btn.disabled = true;

      row.style.opacity = ".6";
      row.style.pointerEvents = "none";

      await deleteTopic(id);
      Vue.toasted.success("Success");
      getTopics();

      btn.disabled = false;

      modals.delete.show = false;
      modals.delete.id = null;
    }

    const onSearchChange = debounce(() => {
      getTopics({
        search: search.value,
      });
    }, 500);

    watch(search, onSearchChange);

    return {
      modals,
      messagesCount,
      topics,
      loading,
      search,
      deleteItem,
      showDeleteModal,
    };
  },
});
</script>
