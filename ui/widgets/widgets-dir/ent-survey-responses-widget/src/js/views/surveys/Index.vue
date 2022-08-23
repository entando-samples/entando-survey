<template>
  <div>
    <b-page-header title="Questionari" />

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
    <b-block class="mt-5">
      <table class="table-auto w-full text-sm">
        <thead class="uppercase text-white bg-ternary">
          <tr class="font-semibold text-left">
            <td class="p-2">title</td>
            <td class="p-2"></td>
            <td></td>
          </tr>
        </thead>
        <tbody>
          <template v-if="!loading">
            <tr v-if="!surveys.data.length">
              <td colspan="3">
                <div class="text-gray-600 text-center py-3">
                  Nessun questionario disponibile
                </div>
              </td>
            </tr>
            <tr
              v-for="(survey, index) in surveys.data"
              :key="survey.id"
              :class="index % 2 === 0 ? 'bg-gray-100' : ''"
            >
              <td class="px-2 py-3">
                <div class="text-primary font-semibold">{{ survey.title }}</div>
              </td>
              <td class="px-2 py-3">
                <div class="font-medium" style="max-width: 150px">
                  {{
                    ([])
                      .map((item) => item.title)
                      .join(", ")
                  }}
                </div>
              </td>
              <td class="px-2 py-3 flex justify-center">
                <div class="flex items-center space-x-2">
                  <router-link
                    :is="survey.can_be_edited ? 'router-link' : 'span'"
                    :to="{ name: 'surveys.edit', params: { id: survey.id } }"
                    class="p-1 border-2"
                    :class="
                      survey.can_be_edited
                        ? 'border-primary rounded bg-primary'
                        : 'border-gray-300 rounded bg-gray-400'
                    "
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5 text-white"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                      stroke="none"
                    >
                      <path
                        d="M 15.222656 6.164062 L 20.960938 7.035156 C 21.433594 7.105469 21.835938 7.449219 21.984375 7.921875 C 22.136719 8.394531 22.015625 8.914062 21.671875 9.265625 L 17.507812 13.457031 L 18.492188 19.46875 C 18.570312 19.960938 18.375 20.460938 17.976562 20.753906 C 17.582031 21.046875 17.058594 21.082031 16.632812 20.847656 L 11.503906 18.039062 L 6.382812 20.847656 C 5.949219 21.082031 5.425781 21.046875 5.03125 20.753906 C 4.636719 20.460938 4.4375 19.960938 4.519531 19.46875 L 5.503906 13.457031 L 1.339844 9.265625 C 0.996094 8.914062 0.875 8.394531 1.027344 7.921875 C 1.175781 7.449219 1.574219 7.105469 2.054688 7.035156 L 7.785156 6.164062 L 10.359375 0.738281 C 10.570312 0.285156 11.015625 0 11.503906 0 C 11.996094 0 12.441406 0.285156 12.652344 0.738281 Z M 15.222656 6.164062"
                      />
                    </svg>
                  </router-link>
                </div>
              </td>
            </tr>
          </template>
          <template v-else-if="loading">
            <tr>
              <td colspan="5">
                <div class="text-gray-600 text-center py-3">
                  Caricamento questionari...
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
          :data="surveys.meta"
          v-if="surveys.meta"
        ></b-paginator>
      </div>
    </div>

    <!--confirmation box to delete Questionari -->
    <b-modal
      title="conferma eliminare"
      v-if="deleteSurveyModal.remind.show"
      @close="deleteSurveyModal.remind.show = false"
    >
      <h3>
        Stai per eliminare questo sondaggio

        <span class="font-bold text-gray-500">{{
          deleteSurveyModal.remind.survey.title
        }}</span
        >.
        <p>Conferma di voler procedere.</p>
      </h3>

      <template #footer>
        <div class="flex gap-x-4 flex-row-reverse">
          <b-button
            @click="deleteItem($event, deleteSurveyModal.remind.survey.id)"
            >Confirm</b-button
          >
          <b-button
            variant="primary-alt"
            @click="deleteSurveyModal.remind.show = false"
            >Indietro</b-button
          >
        </div>
      </template>
    </b-modal>
    <!--confirmation box to delete Questionari ends here-->
  </div>
</template>

<script>
import {
  defineComponent,
  onMounted,
  ref,
  reactive,
  watch,
} from "@vue/composition-api";
import useSurveys from "@/compositions/surveys";
import debounce from "lodash/debounce";
import Vue from "vue";

export default defineComponent({
  name: "SurveyIndexPage",
  setup(props, { root }) {
    const modals = reactive({ scheduler: false });
    const search = ref("");
    const filters = reactive({});
    const deleteSurveyModal = reactive({
      remind: {
        show: false,
        survey: null,
      },
    });

    const { getSurveys, surveys, loading, deleteSurvey } = useSurveys();

    async function confirmDeleteModal(e, survey) {
      deleteSurveyModal.remind.survey = survey;
      deleteSurveyModal.remind.show = true;
    }

    onMounted(() => {
      getSurveys();
    });

    const onSearchChange = debounce(() => {
      getSurveys({
        search: search.value,
      });
    }, 800);

    const onFiltersChange = debounce(() => {
      getSurveys({
        search: search.value,
      });
    });

    watch(search, onSearchChange);

    async function deleteItem(event, id) {
      await deleteSurvey(id);
      Vue.toasted.success("Success");
      getSurveys();
      deleteSurveyModal.remind.survey = null;
      deleteSurveyModal.remind.show = false;
    }

    function onPageChange({ page }) {
      getDocuments({
        page,
        search: search.value,
      });
    }

    function redirectToPatient(surveyId) {
      alert(surveyId);
    }
    return {
      modals,
      filters,
      search,
      surveys,
      loading,
      onPageChange,
      deleteItem,
      deleteSurveyModal,
      confirmDeleteModal,
      redirectToPatient,
    };
  },
});
</script>
