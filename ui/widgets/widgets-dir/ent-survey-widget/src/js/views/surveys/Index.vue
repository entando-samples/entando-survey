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
      <div class="w-3/5 space-y-2 mt-5 flex flex-row-reverse">
        <router-link to="/surveys/create">
          <b-button class="py-2">Crea un nuovo questionario</b-button>
        </router-link>
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
                  <!-- <button
                    @click="modals.scheduler = true"
                    class="
                      px-5
                      font-semibold
                      text-white
                      bg-primary
                      rounded
                      p-1
                      border-2 border-primary
                    "
                  >
                    Schedule
                  </button> -->

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
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                      />
                    </svg>
                  </router-link>

                  <button
                    @click="confirmDeleteModal($event, survey)"
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
                  <!-- <router-link
                    :is="survey.patients_count > 0 ? 'router-link' : 'span'"
                    :to="{ path: '/patients', query: { survey: survey.id } }"
                  >
                    <span
                      class="cursor-pointer underline text-green-700"
                      :class="
                        survey.patients_count > 0 ?
                        'hover:text-green-900 hover:font-extrabold'
                        :'cursor-not-allowed'
                      "
                      >({{ survey.patients_count }})</span
                    >
                  </router-link> -->
                </div>
              </td>
            </tr>
          </template>
          <template v-else-if="loading">
            <tr>
              <td colspan="5">
                <div class="text-gray-600 text-center py-3">
                  Loading surveys....
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
    <!-- <b-modal
      size="3xl"
      title="schedula il questionario"
      v-if="modals.scheduler"
      @close="modals.scheduler = false"
    >
      <p class="font-bold text-gray-600">Nome Questionario</p>
      <p class="mt-1 text-lg">Questionario post visita di centratura</p>

      <div class="mt-8">
        <label class="block font-semibold text-gray-600"
          >Quando vuoi schedularlo?</label
        >
        <div class="mt-1 flex justify-between">
          <label class="flex items-center space-x-3">
            <input name="when" type="radio" class="h-5 w-5" />
            <span>Prima</span>
          </label>

          <label class="flex items-center space-x-3">
            <input name="when" type="radio" class="h-5 w-5" />
            <span>Dopo</span>
          </label>

          <select class="p-1 border border-primary">
            <option value="1">First Appointment</option>
            <option value="2">Second Appointment</option>
            <option value="3">Third Appointment</option>
          </select>

          <label class="flex items-center space-x-3">
            <input
              type="number"
              min="0"
              class="h-5 w-14 border border-primary py-3"
            />
            <span>Numero giorni</span>
          </label>
        </div>
      </div>

      <div class="mt-8">
        <label class="block font-semibold text-gray-600"
          >Scegli quando ripetere</label
        >
        <div class="mt-1 flex justify-between">
          <label class="flex items-center space-x-3">
            <span>Ripeti</span>
            <select class="p-1 border border-primary">
              <option value="1">First Appointment</option>
              <option value="2">Second Appointment</option>
              <option value="3">Third Appointment</option>
            </select>
          </label>

          <label class="flex items-center space-x-3">
            <span>ogni</span>
            <input
              type="number"
              min="0"
              class="h-5 w-14 border border-primary py-3"
            />
            <span>giorni, per un massimo di</span>
            <input
              type="number"
              min="0"
              class="h-5 w-14 border border-primary py-3"
            />
            <span>volte</span>
          </label>
        </div>
      </div>

      <template #footer>
        <div class="flex gap-x-4 flex-row-reverse">
          <b-button>Schedula</b-button>
          <b-button variant="primary-alt" @click="modals.scheduler = false"
            >Indietro</b-button
          >
        </div>
      </template>
    </b-modal> -->

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
// import useDocumentsFilter from "@/compositions/documentFilters";
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
    // const { getFilters, filters: filterData } = useDocumentsFilter();

    console.log("surveys ", surveys);
    async function confirmDeleteModal(e, survey) {
      deleteSurveyModal.remind.survey = survey;
      deleteSurveyModal.remind.show = true;
    }

    onMounted(() => {
      // getFilters();
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
      // filterData,
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
