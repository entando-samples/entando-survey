<template>
  <div>
    <div class="text-gray-500 text-center text-sm" v-if="loadingMedicalData">
      Loading...
    </div>
    <template v-else>
      <div class="grid grid-cols-12 gap-5 tracking-wide">
        <div class="col-span-4 space-y-2">
          <p class="font-bold text-sm text-gray-500">Patologia</p>
          <p class="text-black" v-if="medicalData.pathology">
            {{ medicalData.pathology.title }}
          </p>
          <p class="text-gray-500" v-else>---</p>
        </div>
        <div class="col-span-4 space-y-2">
          <p class="font-bold text-sm text-gray-500">Data inzio terapia</p>
          <p class="text-black" v-if="medicalData.initial_date">
            {{ medicalData.initial_date | date }}
          </p>
          <p class="text-gray-500" v-else>---</p>
        </div>
        <div class="col-span-4 space-y-2">
          <p class="font-bold text-sm text-gray-500">Medico referente</p>
          <p class="text-black" v-if="medicalData.doctor">
            {{ medicalData.doctor.name }} ({{ medicalData.doctor.email }})
          </p>
          <p class="text-gray-500" v-else>---</p>
        </div>
      </div>

      <div class="mt-10 flex flex-row-reverse">
        <b-button
          variant="primary-alt"
          @click="modals.main = true"
          :disabled="medicalData.initial_date"
          >Notifica I'inizio del protocollo</b-button
        >
      </div>

      <b-modal
        title="Data Medici"
        size="3xl"
        v-if="modals.main"
        @close="modals.main = false"
      >
        <div>
          <label class="font-semibold">Pathology</label>
          <b-select
            class="mt-1"
            v-model="form.pathology"
            placeholder="Patologia"
            label="title"
            :options="options.pathologies"
            :reduce="(item) => item.id"
            :multiple="false"
          ></b-select>
        </div>

        <div class="mt-3">
          <label class="font-semibold">Medico</label>
          <b-select
            class="mt-1"
            v-model="form.doctor"
            placeholder="search and select a doctor"
            label="label"
            :options="computedDoctors"
            :reduce="(item) => item.id"
            @search="onDoctorSearch"
            :filterable="false"
            server-message="type to search doctors..."
          >
            <template #option="{ option, isSelected }">
              <div class="-mx-3 flex items-center space-x-2">
                <input
                  type="checkbox"
                  :checked="isSelected"
                  style="pointer-events: none"
                />
                <p class="font-semibold">{{ option.name }}</p>
              </div>
              <p class="ml-2">{{ option.email }}</p>
            </template>
          </b-select>
        </div>

        <template #footer>
          <div class="flex flex-row-reverse">
            <div>
              <b-button
                class="mr-auto"
                :disabled="savingMedicalData"
                @click="save"
                >Submit</b-button
              >
            </div>
          </div>
        </template>
      </b-modal>
    </template>
  </div>
</template>

<script>
import useDocumentsFilter from "@/compositions/documentFilters";
import usePatients from "@/compositions/patients";
import { computed, defineComponent, reactive } from "@vue/composition-api";
import debounce from "lodash/debounce";
import Vue from "vue";

export default defineComponent({
  name: "MedicalData",
  props: ["userId"],
  setup(props, { root }) {
    const { filters: options, getFilters } = useDocumentsFilter();
    const {
      loadingMedicalData,
      savingMedicalData,
      medicalData,
      saveMedicalData,
      getMedicalData,
    } = usePatients();
    
    const computedDoctors = computed(() => {
      return (doctors.data || []).map((doctor) => {
        return {
          label: `${doctor.name} (${doctor.email})`,
          ...doctor,
        };
      });
    });

    let modals = reactive({
      main: false,
    });

    const form = reactive({
      pathology: null,
      doctor: null,
    });

    getFilters();

    fetchMedicalData();

    function fetchMedicalData() {
      getMedicalData(props.userId).catch((err) => {
        Vue.toasted.error(err.response?.data?.message || err.message);
      });
    }

    const fetchDoctors = debounce((search, loading) => {
      getDoctors({ search }).then(loading(false));
    }, 500);

    const onDoctorSearch = (search, loading) => {
      if (search) {
        loading(true);
        fetchDoctors(search.trim(), loading);
      }
    };

    function save() {
      saveMedicalData(props.userId, form)
        .then(() => {
          modals.main = false;
          fetchMedicalData();
          Vue.toasted.success("Successfully saved");
        })
        .catch((err) => {
          Vue.toasted.error(err.response?.data?.message || err.message);
        });
    }

    return {
      modals,
      loadingMedicalData,
      options,
      form,
      computedDoctors,
      medicalData,
      savingMedicalData,
      onDoctorSearch,
      save,
    };
  },
});
</script>
