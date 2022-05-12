<template>
  <div>
    <div class="float-right col-auto hover:text-blue-900 bg-transparent">
      <b-button @click="sendQuestionario()">Send Questionari</b-button>
    </div>
    <table class="table-auto w-full text-sm">
      <thead class="uppercase text-white bg-ternary">
        <tr class="font-semibold text-left">
          <td class="p-2">nome questionario</td>
          <td class="p-2">INVIARE TRAMITE</td>
          <td class="p-2">data invio</td>
          <td class="p-2">stato</td>
          <td class="p-2">data compilaz</td>
          <td class="p-2">N. solleciti</td>
          <td class="p-2">data sollecito</td>
          <td class="p-2">medico sollecitante</td>
          <td>Alert</td>
          <td></td>
        </tr>
      </thead>
      <tbody>
        <tr v-if="loading">
          <td colspan="7">
            <div class="text-gray-600 text-center py-3">
              Loading surveys....
            </div>
          </td>
        </tr>
        <tr v-else-if="!(surveys || []).length">
          <td colspan="7">
            <div class="text-gray-600 text-center py-3">
              No surveys available
            </div>
          </td>
        </tr>
        <template v-else>
          <tr
            v-for="(survey, index) in surveys"
            :key="survey.id"
            class="cursor-pointer"
            :class="
              survey.is_alertable
                ? 'bg-red-100'
                : index % 2 === 0
                ? 'bg-gray-100'
                : ''
            "
            :data-row="survey.id"
            @click="gotoDetail($event, survey)"
          >
            <td class="px-2 py-3">
              <div class="text-primary font-semibold">{{ survey.title }}</div>
            </td>
            <td class="px-2 py-3">
              <div>{{ survey.pivot.send_via }}</div>
            </td>

            <td class="px-2 py-3">
              <div>{{ survey.pivot.created_at | datetime }}</div>
            </td>

            <td class="px-2 py-3 text-white">
              <span
                class="px-2 text-xs bg-green-500 rounded-md"
                v-if="survey.pivot.completed_at"
                style="padding-top: 2px; padding-bottom: 2px"
                >Compilato</span
              >
              <span
                class="px-2 text-xs bg-red-500 rounded-md whitespace-nowrap"
                style="padding-top: 2px; padding-bottom: 2px"
                v-else
                >Non compilato</span
              >
            </td>

            <td class="px-2 py-3">
              <div v-if="survey.pivot.completed_at">
                {{ survey.pivot.completed_at | datetime }}
              </div>
              <div v-else>---</div>
            </td>

            <td class="px-2 py-3">
              <div>{{ survey.reminders_count || 0 }}</div>
            </td>

            <td class="px-2 py-3">
              <div v-if="survey.reminders.length">
                {{ survey.reminders[0].created_at | datetime }}
              </div>
              <div v-else>---</div>
              <p class="text-gray-600 text-sm space-x-1">
                <span>Precedente:</span>
                <span v-if="survey.reminders.length > 1">{{
                  survey.reminders[1].created_at | datetime
                }}</span>
                <span v-else>---</span>
              </p>
            </td>

            <td class="px-2 py-3">
              <div>
                {{
                  survey.reminders.length
                    ? survey.reminders[0].user.name
                    : "---"
                }}
              </div>
            </td>

            <td class="px-2 py-3">
              <svg
                v-if="survey.is_alertable"
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

            <td class="px-2 py-3">
              <div class="flex items-center space-x-2">
                <b-button
                  v-if="!survey.pivot.completed_at"
                  @click="showRemindModal($event, survey)"
                  >Sollecita</b-button
                >
              </div>
            </td>
          </tr>
        </template>
      </tbody>
    </table>

    <b-modal
      title="conferma sollecito"
      v-if="modals.remind.show"
      @close="modals.remind.show = false"
    >
      <h3>
        Stai per inviare un sollecito di lettura al paziente
        <span class="font-bold">{{ modals.remind.patient.name }}</span> per il
        questionario
        <span class="font-bold">{{ modals.remind.survey.title }}</span> .
        <p>Conferma di voler procedere.</p>
        <!-- {{ messagesCount.loading ? "(loading...)" : messagesCount.value }} -->
      </h3>

      <template #footer>
        <div class="flex gap-x-4 flex-row-reverse">
          <b-button @click="remind($event, modals.remind.survey.id)"
            >Confirm</b-button
          >
          <b-button variant="primary-alt" @click="modals.remind.show = false"
            >Indietro</b-button
          >
        </div>
      </template>
    </b-modal>

    <!-- send survey pop out window -->
    <b-modal
      size="3xl"
      title="inviare Questionari"
      v-if="sendSurveyModal.show"
      @close="sendSurveyModal.show = false"
    >
      <p class="font-bold text-gray-600">Nome Questionario</p>
      <p class="mt-1 text-lg">
        <b-select
          placeholder="Questionario"
          v-model="selectSurveyId"
          :options="allSurveys.data"
          :reduce="(item) => item.id"
          close_on_select="true"
          label="title"
          :multiple="false"
        ></b-select>
        <err-msg :message="errors.survey_id"></err-msg>
        <err-msg :message="errors.patient_id"></err-msg>
      </p>

      <template #footer>
        <div class="flex gap-x-4 flex-row-reverse">
          <b-button @click="sendSurvey(selectSurveyId)">Inviare</b-button>
          <b-button variant="primary-alt" @click="sendSurveyModal.show = false"
            >Indietro</b-button
          >
        </div>
      </template>
    </b-modal>
    <!-- send survey pop out window ends -->
  </div>
</template>

<script>
import useSurveys from "@/compositions/patients/surveys";
import useInfoDocuments from "@/compositions/patients/infoDocuments";
import router from "@/router";
import { defineComponent, reactive, ref } from "@vue/composition-api";
import Vue from "vue";
import useAllSurveys from "@/compositions/surveys";

export default defineComponent({
  name: "Surveys",
  props: ["userId"],
  emits: ["update-alert", "update-haswarning"],
  setup(props, ctx) {
    const {
      loading,
      surveys,
      getSurveys,
      remindSurvey,
      errors,
      sendSurveyToPatient,
    } = useSurveys();

    const { getPatientInfo } = useInfoDocuments();
    const { getSurveys: getALlSurvey, surveys: allSurveys } = useAllSurveys();
    const selectSurveyId = ref(null);
    const modals = reactive({
      remind: {
        show: false,
        survey: null,
        patient: null,
      },
    });
    const sendSurveyModal = reactive({
      show: false,
    });

    fetchSurveys();
    getALlSurvey();

    async function showRemindModal(e, survey) {
      e.stopPropagation();
      modals.remind.patient = await getPatientInfo(survey.pivot.patient_id);
      modals.remind.survey = survey;
      modals.remind.show = true;
    }

    function fetchSurveys() {
      ctx.emit("update-alert", false);

      getSurveys(props.userId)
        .then(() => {
          surveys.value.forEach((item) => {
            if (!item.pivot.completed_at) {
              ctx.emit("update-alert", true);
            }

            if (item.is_alertable) {
              ctx.emit("update-haswarning", true);
            }
          });
        })
        .catch((err) => {
          Vue.toasted.error(err?.response?.data?.message || err.message);
        });
    }

    async function remind(event, surveyId) {
      const btn = event.target;

      btn.setAttribute("disabled", true);

      try {
        await remindSurvey(props.userId, surveyId);
        Vue.toasted.success("Success");
        fetchSurveys();
      } catch (err) {
        Vue.toasted.error(err?.response?.data?.message || err.message);
      } finally {
        btn.removeAttribute("disabled");
        modals.remind.survey = null;
        modals.remind.show = false;
      }
    }

    function gotoDetail(event, { id }) {
      if (event.target.tagName == "BUTTON") {
        event.stopPropagation();
        return;
      }

      router.push({
        name: "patients.surveys.show",
        params: {
          patientId: props.userId,
          surveyId: id,
        },
      });
    }

    function sendQuestionario() {
      sendSurveyModal.show = true;
      selectSurveyId.value = null;
    }
    function sendSurvey(survey_id) {
      sendSurveyToPatient({
        patient_id: props.userId,
        survey_id: survey_id,
      })
        .then((response) => {
          sendSurveyModal.show = false;
          Vue.toasted.success(response.message ?? "Success");
          selectSurveyId.value = null;
          fetchSurveys();
        })
        .catch((err) => {
          Vue.toasted.error(err?.response?.data?.message || err.message);
        });
    }

    return {
      loading,
      errors,
      surveys,
      remind,
      gotoDetail,
      showRemindModal,
      sendSurveyModal,
      modals,
      selectSurveyId,
      sendSurvey,
      sendQuestionario,
      allSurveys,
    };
  },
});
</script>
