<template>
  <div>
    <b-page-header title="Questionari da protocollo" />

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
                    (survey.pathologies || [])
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
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        d="M 11.496094 0 C 11.863281 0 12.199219 0.214844 12.359375 0.554688 L 15.097656 6.347656 L 21.21875 7.28125 C 21.578125 7.332031 21.875 7.59375 21.988281 7.945312 C 22.101562 8.300781 22.007812 8.691406 21.753906 8.949219 L 17.3125 13.46875 L 18.363281 19.847656 C 18.425781 20.214844 18.277344 20.59375 17.976562 20.8125 C 17.679688 21.027344 17.253906 21.058594 16.964844 20.882812 L 11.496094 17.878906 L 5.992188 20.882812 C 5.707031 21.058594 5.316406 21.027344 5.015625 20.8125 C 4.71875 20.59375 4.570312 20.214844 4.597656 19.847656 L 5.679688 13.46875 L 1.242188 8.949219 C 0.984375 8.691406 0.894531 8.300781 1.007812 7.945312 C 1.121094 7.59375 1.417969 7.332031 1.777344 7.28125 L 7.894531 6.347656 L 10.632812 0.554688 C 10.796875 0.214844 11.128906 0 11.496094 0 Z M 11.496094 3.238281 L 9.398438 7.679688 C 9.261719 7.96875 8.988281 8.175781 8.675781 8.222656 L 3.953125 8.9375 L 7.382812 12.429688 C 7.601562 12.652344 7.703125 12.976562 7.652344 13.292969 L 6.84375 18.199219 L 11.042969 15.894531 C 11.328125 15.738281 11.667969 15.738281 11.945312 15.894531 L 16.148438 18.199219 L 15.339844 13.292969 C 15.289062 12.976562 15.394531 12.652344 15.613281 12.429688 L 19.042969 8.9375 L 14.320312 8.222656 C 14.003906 8.175781 13.730469 7.96875 13.597656 7.679688 Z M 11.496094 3.238281"
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

        <span class="font-bold">{{
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

<style scoped>
span {
  @apply text-gray-500;
}
</style>
