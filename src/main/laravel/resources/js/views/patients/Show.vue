<template>
  <div class="mt-8">
    <b-page-header title="Marco Rossi">
      <router-link
        :to="{ name: 'messages.create', query: { patient: userId } }"
      >
        <b-button>Invia una comunicazione</b-button>
      </router-link>
    </b-page-header>

    <div>
      <b-tabs class="bg-white" :options="{ useUrlFragment: true }">
        <b-tab name="dati anagrafici">
          <div class="p-5">
            <div class="grid grid-cols-12 gap-5 tracking-wide">
              <div class="col-span-4 space-y-8">
                <div class="space-y-2">
                  <p class="font-bold text-sm text-gray-500">Nome e Cognome</p>
                  <p class="text-black">Alessia Lombardi</p>
                </div>
                <div class="space-y-2">
                  <p class="font-bold text-sm text-gray-500">Codice Fiscale</p>
                  <p class="text-black">LMBLSS61P42F205E</p>
                </div>
                <div class="space-y-2">
                  <p class="font-bold text-sm text-gray-500">
                    Comune di nascita e provincia
                  </p>
                  <p class="text-black">Milano (MI)</p>
                </div>
              </div>
              <div class="col-span-4 space-y-8">
                <div class="space-y-2">
                  <p class="font-bold text-sm text-gray-500">Sesso</p>
                  <p class="text-black">Femmina</p>
                </div>
                <div class="space-y-2">
                  <p class="font-bold text-sm text-gray-500">
                    Numero di telefono
                  </p>
                  <p class="text-black">348 123 4567</p>
                </div>
                <div class="space-y-2">
                  <p class="font-bold text-sm text-gray-500">
                    Indirizzo di residenza
                  </p>
                  <p class="text-black">
                    Via Domenico Trentacoste, 9, 20134, Milano (MI), Italia
                  </p>
                </div>
              </div>
              <div class="col-span-4 space-y-8">
                <div class="grid grid-cols-12 gap-5">
                  <div class="col-span-6 space-y-2">
                    <p class="font-bold text-sm text-gray-500">
                      Data di nascita
                    </p>
                    <p class="text-black">01/08/1961</p>
                  </div>
                  <div class="col-span-6 space-y-2">
                    <p class="font-bold text-sm text-gray-500">Età</p>
                    <p class="text-black">60 anni</p>
                  </div>
                </div>
                <div class="space-y-2">
                  <p class="font-bold text-sm text-gray-500">E-mail</p>
                  <p class="text-black">alessialongobardi@email.com</p>
                </div>
                <div class="space-y-2">
                  <p class="font-bold text-sm text-gray-500">
                    Indirizzo di domicilio (solo se diverso da residenza)
                  </p>
                  <p class="text-black">
                    Via Bistolfi, 10, 20096, Cernusco sul Naviglio (MI), Italia
                  </p>
                </div>
              </div>
            </div>
            <div class="mt-10 flex flex-row-reverse">
              <b-button variant="primary-alt"
                >Modifica dati anagrafici</b-button
              >
            </div>
          </div>
        </b-tab>
        <b-tab name="Data Medici">
          <medical-data :user-id="userId"></medical-data>
        </b-tab>
        <b-tab name="Calendario"> comming soon </b-tab>
        <b-tab name="Communicazioni">
          <Messages :user-id="userId"></Messages>
        </b-tab>
      </b-tabs>
    </div>

    <div class="mt-10">
      <b-tabs class="bg-white" :options="{ useUrlFragment: true }">
        <b-tab
          name="Documenti inviati al paziente"
          :suffix="documentAlerts ? dotIcon : ''"
        >
          <info-documents
            :user-id="userId"
            @update-alert="documentAlerts = $event"
          ></info-documents>
        </b-tab>
        <b-tab name="questionari inviati al paziente" :suffix="surveySuffix">
          <surveys
            :user-id="userId"
            @update-alert="surveyAlerts = $event"
            @update-haswarning="surveyWarning = $event"
          ></surveys>
        </b-tab>
        <b-tab name="Documenti del paziente">
          <patient-documents :user-id="userId"></patient-documents>
        </b-tab>
      </b-tabs>
    </div>
  </div>
</template>

<script>
import { computed, defineComponent, ref } from "@vue/composition-api";
import InfoDocuments from "./show/InfoDocuments.vue";
import PatientDocuments from "./show/Documents.vue";
import Surveys from "./show/Surveys.vue";
import MedicalData from "./show/MedicalData.vue";
import Messages from "./show/Messages.vue";

const dotIcon = `<svg class="-mt-5 inline w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>`;

const warningIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 fill-current text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>`;

export default defineComponent({
  name: "PatientShowPage",
  components: {
    PatientDocuments,
    InfoDocuments,
    Surveys,
    MedicalData,
    Messages,
  },
  setup(_, { root }) {
    let userId = root.$route.params.id;

    const documentAlerts = ref(false);
    const surveyAlerts = ref(false);
    const surveyWarning = ref(false);

    return {
      userId,
      documentAlerts,
      surveyAlerts,
      surveyWarning,
      dotIcon,
      warningIcon,
      surveySuffix: computed(() => {
        return (
          (surveyAlerts.value ? dotIcon : "") +
          (surveyWarning.value ? warningIcon : "")
        );
      }),
    };
  },
});
</script>
