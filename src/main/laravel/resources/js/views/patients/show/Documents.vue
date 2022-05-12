<template>
    <div>
        <p v-if="loading" class="text-gray-500 text-center">Loading document...</p>
        <p v-else-if="!folders.length" class="text-gray-500 text-center">No document available</p>
        <accordion v-else>
            <accordion-item v-for="(folder, folderIdx) in folders" :key="folder.id">
                <template slot="accordion-trigger">
                    <h3>{{ folder.title }}</h3>
                </template>
                <template
                    slot="accordion-trigger-end"
                >Date creazione: {{ (folder.updated_at || folder.created_at) | datetime }}</template>
                <template slot="accordion-content">
                    <table class="w-full">
                        <tr
                            class="border-b"
                            v-for="(document, docIndex) in folder.documents"
                            :key="document.id"
                        >
                            <td class="py-3">
                                <div class="flex item-center space-x-2 text-gray-500 font-semibold">
                                    <svg
                                        class="w-5 h-5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <p>{{ document.title }}</p>
                                </div>
                            </td>
                            <td class="py-3">
                                <div>
                                    <p v-if="document.read_at">{{ document.read_at | datetime }}</p>
                                    <b-button
                                        variant="primary-alt"
                                        v-else
                                        @click="readDocument($event, document, folderIdx, docIndex)"
                                    >Mark as read</b-button>
                                </div>
                            </td>
                            <td class="py-3 flex flex-row-reverse">
                                <div class="rounded-md bg-primary flex items-center text-white">
                                    <button class="p-2" @click.prevent="showDocument(document)">
                                        <svg
                                            class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path
                                                fill-rule="evenodd"
                                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                    <a
                                        :href="`/api/v1/backend/patients/documents/${document.id}/download`"
                                        download
                                        class="p-2"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </table>
                </template>
            </accordion-item>
        </accordion>

        <b-modal
            size="3xl"
            title="Documents"
            v-if="modals.document.open"
            @close="modals.document.open = false"
        >
            <div>
                <p class="font-semibold">Attachments</p>
                <ul class="list-disc" v-if="modals.document.data.attachments.length">
                    <li
                        class="flex items-center space-x-3"
                        v-for="(attachment, idx) in modals.document.data.attachments"
                        :key="idx"
                    >
                        <a class="text-primary" :href="attachment" target="_blank">{{ attachment }}</a>
                        <a :href="attachment" download>
                            <svg
                                class="w-6 h-6 text-primary"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </a>
                    </li>
                </ul>
                <p v-else class="text-sm text-gray-500">No attachments</p>
            </div>
            <div class="mt-4" v-if="modals.document.data.voice_message">
                <p class="font-semibold flex items-center space-x-5">
                    <span>Voice Message:</span>
                    <a :href="modals.document.data.voice_message" download>
                        <svg
                            class="w-6 h-6 text-primary"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </a>
                </p>
                <audio class="mt-2" controls>
                    <source :src="modals.document.data.voice_message" type="audio/mpeg" />Your browser does not support the audio tag.
                </audio>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { defineComponent, reactive } from "@vue/composition-api";
import Accordion, { AccordionItem } from "@/components/Accordion.vue";
import useDocuments from "@/compositions/patients/documents";
import Vue from "vue";

export default defineComponent({
    name: "Documents",
    components: { Accordion, AccordionItem },
    props: ['userId'],
    setup(props) {
        const { loading, folders, getDocuments, markAsRead } = useDocuments();

        const modals = reactive({
            document: { open: false, data: null },
        });

        getDocuments(props.userId)

        function readDocument(event, { id }, folderIdx, docIdx) {
            const btn = event.target;

            btn.setAttribute('disabled', true);
            markAsRead(id)
                .then(res => {
                    folders.value[folderIdx].documents[docIdx].read_at = res.data.read_at;
                    Vue.toasted.success(res.message);
                })
                .catch(err => {
                    Vue.toasted.error(err.response?.data?.message || err.message)
                })
                .finally(() => {
                    btn.removeAttribute('disabled');
                })
        }

        function showDocument(document) {
            modals.document.data = document;
            modals.document.open = true;
        }

        return {
            loading,
            modals,
            folders,
            showDocument,
            readDocument,
        }
    }
})
</script>
