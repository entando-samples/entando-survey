<template>
  <div>
    <b-page-header title="Gestione Pazienti" />

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
              v-model="filters.doctors"
              placeholder="Medico"
              :options="filterData.doctors"
              :multiple="true"
              :reduce="(item) => item.id"
              label="label"
            ></b-select>
          </div>
          <div class="w-1/3">
            <b-select
              v-model="filters.pathologies"
              placeholder="Pathologia"
              :options="filterData.pathologies"
              :multiple="true"
              label="title"
              :reduce="(item) => item.id"
            ></b-select>
          </div>
        </div>
      </div>
    </div>
    <b-block class="mt-5">
      <table class="table-auto w-full text-sm">
        <thead class="uppercase text-white bg-ternary">
          <tr class="font-semibold text-left">
            <td class="p-2">nome e cognome</td>
            <td class="p-2">patologia</td>
            <td class="p-2">Data inizio terapia</td>
            <td class="p-2">documenti inviati</td>
            <td class="p-2">questionari inviati</td>
            <td class="p-2">alert</td>
            <td></td>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="10">
              <div class="text-gray-600 text-center py-3">
                Loading patients...
              </div>
            </td>
          </tr>
          <tr v-else-if="!patients.data.length">
            <td colspan="10">
              <div class="text-gray-600 text-center py-3">
                No patients available
              </div>
            </td>
          </tr>
          <tr
            v-else
            v-for="(patient, index) in patients.data"
            :key="patient.id"
            :class="{
              'bg-red-100': patient.is_alertable,
              'bg-white': Number(index % 2) !== 0,
              'bg-gray-100': Number(index % 2) === 0,
            }"
          >
            <td class="px-2 py-3">
              <div class="text-primary font-semibold">{{ patient.name }}</div>
              <p class="mt-1 text-xs text-gray-500">F - 60 anni</p>
            </td>
            <td class="px-2 py-3">
              <div>---</div>
              <p class="mt-1 text-xs text-gray-500">---</p>
            </td>
            <td class="px-2 py-3">
              <div>---</div>
              <p class="mt-1 text-xs text-gray-500">---</p>
            </td>
            <td class="px-2 py-3">
              <div>
                {{ patient.read_documents_count }}/{{ patient.documents_count }}
              </div>
            </td>
            <td class="px-2 py-3">
              <div>
                {{ patient.read_surveys_count }}/{{ patient.surveys_count }}
              </div>
            </td>
            <td class="px-2 py-3">
              <svg
                v-if="patient.is_alertable"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 fill-current text-yellow-500"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </td>
            <td>
              <router-link
                to="/patients/2"
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
            :data="patients.meta"
            @change="onPageChange"
            v-if="patients.meta"
          ></b-paginator>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import usePatients from "@/compositions/patients";
import { defineComponent, reactive, watch } from "@vue/composition-api";
import debounce from "lodash/debounce";
import Vue from "vue";

export default defineComponent({
  name: "PatientIndexPage",
  setup() {
    const {
      loading,
      patients,
      filters: filterData,
      getPatients,
      getFilters,
    } = usePatients();

    const filters = reactive({
      pathologies: [],
      doctors: [],
      search: "",
    });
    let urlParam = new URLSearchParams(document.location.search);
    const survey = urlParam.get("survey");
    const documentId = urlParam.get("document");
    const infodocument = urlParam.get("infodocument");
    const filtersurvey = urlParam.get('filtersurvey');
    fetchPatients();
    getFilters();

    watch(filters, debounce(onFiltersChange, 500));

    function fetchPatients({
      page = 1,
      pathologies = [],
      doctors = [],
      search = "",
      filterBySurvey = survey,
      filterByDocument = documentId,
      infoDocument = infodocument,
      filterSurvey = filtersurvey,
    } = {}) {
      getPatients({
        page,
        forListing: "true",
        pathologies,
        doctors,
        search,
        filterBySurvey,
        filterByDocument,
        infoDocument,
        filterSurvey
      }).catch((err) => {
        Vue.toasted.error(err?.response?.data?.message || err.message);
      });
    }

    function onPageChange({ page }) {
      fetchPatients({ page, ...filters });
    }

    function onFiltersChange() {
      fetchPatients({ page: 1, ...filters });
    }

    return {
      filters,
      filterData,
      loading,
      patients,
      onPageChange,
    };
  },
});
</script>
