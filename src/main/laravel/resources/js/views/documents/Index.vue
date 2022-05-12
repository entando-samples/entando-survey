<template>
  <div>
    <b-page-header title="Gestione Documenti" />

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
      <div class="w-3/5 space-y-2">
        <p class="text-sm">Filtra per</p>
        <div class="flex items-start space-x-3">
          <div class="w-1/3">
            <b-select
              v-model="filters.pathologies"
              placeholder="Patologia"
              label="title"
              :options="filterData.pathologies"
              :reduce="(item) => item.id"
              :multiple="true"
            ></b-select>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-5 flex flex-row-reverse">
      <router-link to="/documents/create">
        <b-button class="py-2">Crea un nuovo documento</b-button>
      </router-link>
    </div>
    <b-block class="mt-5">
      <table class="table-auto w-full text-sm">
        <thead class="uppercase text-white bg-ternary">
          <tr class="font-semibold text-left">
            <td class="p-2">nomee documento</td>
            <td class="p-2">creato il</td>
            <td class="p-2">creato da</td>
            <td class="p-2">patologia associata</td>
            <td></td>
          </tr>
        </thead>
        <tbody>
          <template v-if="!loading">
            <tr v-if="!documents.data.length">
              <td colspan="5">
                <div class="text-gray-600 text-center py-3">
                  No documents available
                </div>
              </td>
            </tr>
            <tr
              v-for="(document, index) in documents.data"
              :key="document.id"
              :class="index % 2 === 0 ? 'bg-gray-100' : ''"
            >
              <td class="px-2 py-3">
                <div class="text-primary font-semibold">
                  {{ document.title }}
                </div>
              </td>
              <td class="px-2 py-3">
                <div class="font-medium">{{ document.created_at | date }}</div>
              </td>
              <td class="px-2 py-3">
                <div class="font-medium">
                  {{ document.creator ? document.creator.name : "" }}
                </div>
              </td>
              <td class="px-2 py-3">
                <div class="font-medium" style="max-width: 150px">
                  {{
                    (document.pathologies || [])
                      .map((item) => item.title)
                      .join(", ")
                  }}
                </div>
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
                        name: 'documents.edit',
                        params: { id: document.id },
                      }"
                      class="text-center"
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
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                      </svg>
                    </router-link>
                    <router-link
                      :to="{
                        name: 'documents.edit',
                        params: { id: document.id },
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
                    @click="confirmDeleteModal($event, document)"
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
                  <router-link
                    :to="{
                      path: '/patients',
                      query: { document: document.id },
                    }"
                  >
                    <span
                      class="cursor-pointer underline text-green-700"
                      :class="
                        document.patients_count > 0
                          ? 'hover:text-green-900 hover:font-extrabold'
                          : 'cursor-not-allowed'
                      "
                      >({{ document.patients_count }})</span
                    >
                  </router-link>
                </div>
              </td>
            </tr>
          </template>
          <template v-else-if="loading">
            <tr>
              <td colspan="5">
                <div class="text-gray-600 text-center py-3">
                  Loading documents...
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </b-block>
    <div class="mt-7">
      <div class="flex flex-row-reverse">
        <b-paginator
          @change="onPageChange"
          :data="documents.meta"
          v-if="documents.meta"
        ></b-paginator>
      </div>
    </div>

    <!--confirmation box to delete document -->
    <b-modal
      title="conferma eliminare"
      v-if="deleteDocumentModal.remind.show"
      @close="deleteDocumentModal.remind.show = false"
    >
      <h3>
        Questo documento è

        <span class="font-bold">{{
          deleteDocumentModal.remind.document.title
        }}</span>
        già stato inviato. sei sicuro?
      </h3>

      <template #footer>
        <div class="flex gap-x-4 flex-row-reverse">
          <b-button
            @click="deleteDoc($event, deleteDocumentModal.remind.document.id)"
            >Confirm</b-button
          >
          <b-button
            variant="primary-alt"
            @click="deleteDocumentModal.remind.show = false"
            >Indietro</b-button
          >
        </div>
      </template>
    </b-modal>
    <!--confirmation box to delete document ends here-->
  </div>
</template>

<script>
import useDocumentsFilter from "@/compositions/documentFilters";
import useDocuments from "@/compositions/documents";
import debounce from "lodash/debounce";
import Vue from "vue";
import {
  defineComponent,
  onMounted,
  reactive,
  ref,
  watch,
} from "@vue/composition-api";

export default defineComponent({
  name: "PatientIndexPage",
  setup(props, { root }) {
    const search = ref("");
    const filters = reactive({
      pathologies: root.$route.query?.pathology
        ? [Number(root.$route.query?.pathology)]
        : null,
    });
    const deleteDocumentModal = reactive({
      remind: {
        show: false,
        document: null,
      },
    });

    const { getDocuments, documents, loading, deleteDocument } = useDocuments();
    const { getFilters, filters: filterData } = useDocumentsFilter();

    async function confirmDeleteModal(e, document) {
      deleteDocumentModal.remind.document = document;
      deleteDocumentModal.remind.show = true;
    }

    onMounted(() => {
      getDocuments({ pathologies: filters.pathologies });
      getFilters();
    });

    async function deleteDoc(event, id) {
      await deleteDocument(id);
      Vue.toasted.success("Success");
      getDocuments();
      deleteDocumentModal.remind.document = null;
      deleteDocumentModal.remind.show = false;
    }

    const onSearchChange = debounce(() => {
      getDocuments({
        search: search.value,
        pathologies: filters.pathologies,
      });
    }, 800);

    const onFiltersChange = () => {
      getDocuments({
        search: search.value,
        pathologies: filters.pathologies,
      });
    };

    watch(search, onSearchChange);
    watch(filters, onFiltersChange);

    function onPageChange({ page }) {
      getDocuments({
        page,
        search: search.value,
        pathologies: filters.pathologies,
      });
    }

    return {
      filters,
      search,
      onPageChange,
      filterData,
      documents,
      loading,
      deleteDoc,
      deleteDocumentModal,
      confirmDeleteModal,
    };
  },
});
</script>
