<template>
  <div>
    <div class="float-right col-auto hover:text-blue-900 bg-transparent">
      <b-button @click="sendDocument()">Send Document</b-button>
    </div>
    <table class="table-auto w-full text-sm">
      <thead class="uppercase text-white bg-ternary">
        <tr class="font-semibold text-left">
          <td class="p-2">nome documento</td>
          <td class="p-2">inviare tramite</td>
          <td class="p-2">data invio</td>
          <td class="p-2">stato</td>
          <td class="p-2">N. solleciti</td>
          <td class="p-2">data sollecito</td>
          <td class="p-2">medico sollecitante</td>
          <td></td>
        </tr>
      </thead>
      <tbody>
        <tr v-if="loading">
          <td colspan="7">
            <div class="text-gray-600 text-center py-3">
              Loading documents....
            </div>
          </td>
        </tr>
        <tr v-else-if="!(documents || []).length">
          <td colspan="7">
            <div class="text-gray-600 text-center py-3">
              No documents available
            </div>
          </td>
        </tr>
        <template v-else>
          <tr
            v-for="(document, index) in documents"
            :key="document.id"
            :class="index % 2 === 0 ? 'bg-gray-100' : ''"
            :data-row="document.id"
          >
            <td class="px-2 py-3">
              <div class="text-primary font-semibold">{{ document.title }}</div>
            </td>
            <td class="px-2 py-3">
              <div>{{ document.pivot.send_via }}</div>
            </td>

            <td class="px-2 py-3">
              <div>{{ document.pivot.created_at | datetime }}</div>
            </td>

            <td class="px-2 py-3 text-white whitespace-nowrap">
              <span
                class="px-2 text-xs bg-green-500 rounded-md"
                v-if="document.pivot.is_read"
                style="padding-top: 2px; padding-bottom: 2px"
                >Letto</span
              >
              <span
                class="px-2 text-xs bg-red-500 rounded-md"
                style="padding-top: 2px; padding-bottom: 2px"
                v-else
                >Non Letto</span
              >
            </td>

            <td class="px-2 py-3">
              <div>{{ document.reminders_count || 0 }}</div>
            </td>

            <td class="px-2 py-3">
              <div v-if="document.reminders.length">
                {{ document.reminders[0].created_at | datetime }}
              </div>
              <div v-else>---</div>
              <p class="text-gray-600 text-sm space-x-1">
                <span>Precedente:</span>
                <span v-if="document.reminders.length > 1">{{
                  document.reminders[1].created_at | datetime
                }}</span>
                <span v-else>---</span>
              </p>
            </td>

            <td class="px-2 py-3">
              <div>
                {{
                  document.reminders.length
                    ? document.reminders[0].user.name
                    : "---"
                }}
              </div>
            </td>

            <td class="px-2 py-3 flex justify-center">
              <div class="flex items-center space-x-2">
                <b-button
                  v-if="!document.pivot.is_read"
                  @click="showRemindModal($event, document)"
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
        documento
        <span class="font-bold">{{ modals.remind.document.title }}</span
        >.
        <p>Conferma di voler procedere.</p>
        <!-- {{ messagesCount.loading ? "(loading...)" : messagesCount.value }} -->
      </h3>

      <template #footer>
        <div class="flex gap-x-4 flex-row-reverse">
          <b-button @click="remind($event, modals.remind.document.id)"
            >Confirm</b-button
          >
          <b-button variant="primary-alt" @click="modals.remind.show = false"
            >Indietro</b-button
          >
        </div>
      </template>
    </b-modal>

    <!-- send document pop out window -->
    <b-modal
      size="3xl"
      title="inviare documento"
      v-if="sendDocumentModal.show"
      @close="sendDocumentModal.show = false"
    >
      <p class="font-bold text-gray-600">Nome Documenti</p>
      <p class="mt-1 text-lg">
        <b-select
          placeholder="documento"
          v-model="infoDocId"
          close_on_select="true"
          label="title"
          :options="infoDocuments.data"
          :reduce="(item) => item.id"
          :multiple="false"
        ></b-select>
        <err-msg :message="errors.document_id"></err-msg>
        <err-msg :message="errors.patient_id"></err-msg>
      </p>

      <template #footer>
        <div class="flex gap-x-4 flex-row-reverse">
          <b-button @click="assingDocument(infoDocId)">Inviare</b-button>
          <b-button
            variant="primary-alt"
            @click="sendDocumentModal.show = false"
            >Indietro</b-button
          >
        </div>
      </template>
    </b-modal>
    <!-- send document pop out window ends -->
  </div>
</template>

<script>
import useInfoDocuments from "@/compositions/patients/infoDocuments";
import { defineComponent, reactive, ref } from "@vue/composition-api";
import useDocuments from "@/compositions/documents";

import Vue from "vue";

export default defineComponent({
  name: "InfoDocuments",
  props: ["userId"],
  setup(props, ctx) {
    const modals = reactive({
      remind: {
        show: false,
        document: null,
        patient: null,
      },
    });
    const infoDocId = ref(null);
    const sendDocumentModal = reactive({
      show: false,
    });

    const { loading, documents, getDocuments, remindDocument, getPatientInfo } =
      useInfoDocuments();
    const {
      getAllInfoDocuments,
      infoDocuments,
      loading: infoDocloading,
      assignDocumentToPatient,
      errors,
    } = useDocuments();
    getAllInfoDocuments();

    fetchDocuments();
    async function showRemindModal(e, document) {
      modals.remind.patient = await getPatientInfo(document.pivot.patient_id);
      modals.remind.document = document;
      modals.remind.show = true;
    }
    function fetchDocuments() {
      ctx.emit("update-alert", false);

      getDocuments(props.userId)
        .then(() => {
          documents.value.forEach((item) => {
            if (item.pivot.is_read == 0) ctx.emit("update-alert", true);
          });
        })
        .catch((err) => {
          Vue.toasted.error(err?.response?.data?.message || err.message);
        });
    }

    async function remind(event, documentId) {
      const btn = event.target;

      btn.setAttribute("disabled", true);

      try {
        await remindDocument(props.userId, documentId);
        Vue.toasted.success("Success");
        fetchDocuments();
      } catch (err) {
        Vue.toasted.error(err?.response?.data?.message || err.message);
      } finally {
        btn.removeAttribute("disabled");
        modals.remind.document = null;
        modals.remind.show = false;
      }
    }

    function sendDocument() {
      sendDocumentModal.show = true;
      infoDocId.value = null;
    }

    function assingDocument(infoDocumentId) {
      assignDocumentToPatient({
        patient_id: props.userId,
        document_id: infoDocumentId,
      })
        .then((response) => {
          sendDocumentModal.show = false;
          Vue.toasted.success(response.message ?? "Success");
          infoDocId.value = null;
          fetchDocuments();
        })
        .catch((err) => {
          Vue.toasted.error(err?.response?.data?.message || err.message);
        });
    }
    return {
      loading,
      documents,
      remind,
      showRemindModal,
      modals,
      sendDocument,
      sendDocumentModal,
      infoDocuments,
      infoDocloading,
      assingDocument,
      infoDocId,
      errors,
    };
  },
});
</script>
