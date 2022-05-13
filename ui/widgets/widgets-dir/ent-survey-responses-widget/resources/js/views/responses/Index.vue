<template>
  <div>
    <b-page-header title="Risposte questionari" />

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
            <td></td>
          </tr>
        </thead>
        <tbody>
          <template v-if="!loading">
            <tr v-if="!surveys.data.length">
              <td colspan="3">
                <div class="text-gray-600 text-center py-3">No surveys available</div>
              </td>
            </tr>
            <tr v-for="(survey, index) in surveys.data" :key="survey.id" :class="index % 2 === 0 ? 'bg-gray-100' : ''">
              <td class="px-2 py-3">
                <div class="text-primary font-semibold">{{ survey.title }}</div>
              </td>
              <td class="px-2 py-3 flex justify-center">
                <div class="flex items-center space-x-2">
                    <router-link :to="{ name: 'responses.answer', params: { id: survey.id } }">
                        <b-button class="px-5 font-semibold text-white bg-primary rounded p-1 border-2 border-primary">
                            Reply
                        </b-button>
                    </router-link>
                </div>
              </td>
            </tr>
          </template>
          <template v-else-if="loading">
            <tr>
              <td colspan="5">
                <div class="text-gray-600 text-center py-3">Loading surveys....</div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </b-block>
    <div class="mt-7">
      <div class="flex flex-row-reverse">
        <b-paginator @change="onPageChange" :data="surveys.meta" v-if="surveys.meta"></b-paginator>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, onMounted, ref, reactive, watch } from '@vue/composition-api';
import useSurveys from '@/compositions/surveys';
import useDocumentsFilter from '@/compositions/documentFilters';
import debounce from 'lodash/debounce';
import Vue from 'vue';

export default defineComponent({
  name: 'ResponsesIndexPage',
  setup(props, { root }) {
    const modals = reactive({ scheduler: false });
    const search = ref('');
    const filters = reactive({
      pathologies: root.$route.query?.pathology ? [Number(root.$route.query?.pathology)] : null,
    });
    const deleteSurveyModal = reactive({
      remind: {
        show: false,
        survey: null,
      },
    });

    const { getSurveys, surveys, loading, deleteSurvey } = useSurveys();
    const { getFilters, filters: filterData } = useDocumentsFilter();

    console.log('surveys ', surveys);
    async function confirmDeleteModal(e, survey) {
      deleteSurveyModal.remind.survey = survey;
      deleteSurveyModal.remind.show = true;
    }

    onMounted(() => {
      getFilters();
      getSurveys({ pathologies: filters.pathologies });
    });

    const onSearchChange = debounce(() => {
      getSurveys({
        search: search.value,
        pathologies: filters.pathologies,
      });
    }, 800);

    const onFiltersChange = debounce(() => {
      getSurveys({
        search: search.value,
        pathologies: filters.pathologies,
      });
    });

    watch(search, onSearchChange);
    watch(filters, onFiltersChange);

    async function deleteItem(event, id) {
      await deleteSurvey(id);
      Vue.toasted.success('Success');
      getSurveys();
      deleteSurveyModal.remind.survey = null;
      deleteSurveyModal.remind.show = false;
    }

    function onPageChange({ page }) {
      getDocuments({
        page,
        search: search.value,
        pathologies: filters.pathologies,
      });
    }

    function redirectToPatient(surveyId) {
      alert(surveyId);
    }
    return {
      modals,
      filters,
      search,
      filterData,
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
