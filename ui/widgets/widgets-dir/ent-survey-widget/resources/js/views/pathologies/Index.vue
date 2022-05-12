<template>
    <div>
        <b-page-header title="Patologie" />

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

        <div class="flex flex-row-reverse">
            <router-link to="/pathologies/create">
                <b-button class="py-2">Aggiungi</b-button>
            </router-link>
        </div>
        <b-block class="mt-5">
            <table class="table-auto w-full text-sm">
                <thead class="uppercase text-white bg-ternary">
                    <tr class="font-semibold text-left">
                        <td class="p-2">Title</td>
                        <td class="p-2">
                            Surveys
                            <small>(count)</small>
                        </td>
                        <td class="p-2">
                            Documents
                            <small>(count)</small>
                        </td>
                        <td class="p-2">
                            Patients
                            <small>(count)</small>
                        </td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="5">
                            <div class="text-gray-600 text-center py-3">Loading pathologies....</div>
                        </td>
                    </tr>
                    <tr v-else-if="!(pathologies || []).length">
                        <td colspan="5">
                            <div class="text-gray-600 text-center py-3">No pathologies available</div>
                        </td>
                    </tr>
                    <template v-else>
                        <tr
                            v-for="(pathology,index) in pathologies"
                            :key="pathology.id"
                            :class="index % 2 === 0 ? 'bg-gray-100' : ''"
                            :data-row="pathology.id"
                        >
                            <td class="px-2 py-3">
                                <div class="text-primary font-semibold">{{ pathology.title }}</div>
                            </td>
                            <td class="px-2 py-3">
                                <router-link
                                    :to="{ name: 'surveys.index', query: { pathology: pathology.id } }"
                                    class="text-primary font-semibold"
                                >{{ pathology.surveys_count }}</router-link>
                            </td>
                            <td class="px-2 py-3">
                                <router-link
                                    :to="{ name: 'documents.index', query: { pathology: pathology.id } }"
                                    class="text-primary font-semibold"
                                >{{ pathology.documents_count }}</router-link>
                            </td>
                            <td class="px-2 py-3">
                                <div class="text-primary font-semibold">{{ 0 }}</div>
                            </td>
                            <td class="px-2 py-3 flex justify-center">
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="flex space-x-3 text-white bg-primary rounded p-1 border-2 border-primary"
                                    >
                                        <router-link
                                            :to="{ name: 'pathologies.edit', params: { id: pathology.id } }"
                                            class="text-center"
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
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                                />
                                            </svg>
                                        </router-link>
                                    </div>

                                    <button
                                        @click="showDeleteModal(pathology.id)"
                                        class="p-1 text-primary hover:text-white border-2 border-primary rounded hover:bg-primary"
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
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </b-block>

        <b-modal
            title="Confirm Delete"
            v-if="modals.delete.show"
            @close="modals.delete.show = false"
        >
            <h3>Are you sure? Following documents/surveys refer to this pathology.</h3>

            <div class="mt-3">
                <p class="font-semibold">Documents:</p>

                <p class="text-sm text-gray-500" v-if="realtionItems.loading">Loading...</p>

                <p
                    class="text-sm text-gray-500"
                    v-else-if="realtionItems.documents.length < 1"
                >No documents</p>

                <ol class="list-decimal ml-6" v-else>
                    <li v-for="(title, id) in realtionItems.documents" :key="id">
                        <router-link
                            class="text-primary"
                            :to="{ name: 'documents.edit', params: { id: id } }"
                        >{{ title }}</router-link>
                    </li>
                </ol>
            </div>

            <div class="mt-3">
                <p class="font-semibold">Surveys:</p>
                <p class="text-sm text-gray-500" v-if="realtionItems.loading">Loading...</p>
                <p
                    class="text-sm text-gray-500"
                    v-else-if="realtionItems.documents.length < 1"
                >No surveys</p>
                <ol class="list-decimal ml-6" v-else>
                    <li v-for="(title, id) in realtionItems.surveys" :key="id">
                        <router-link
                            class="text-primary"
                            :to="{ name: 'surveys.edit', params: { id: id } }"
                        >{{ title }}</router-link>
                    </li>
                </ol>
            </div>

            <template #footer>
                <div class="flex gap-x-4 flex-row-reverse">
                    <b-button
                        :disabled="realtionItems.loading"
                        @click="deleteItem($event, modals.delete.id)"
                    >Confirm</b-button>
                    <b-button variant="primary-alt" @click="modals.delete.show = false">Indietro</b-button>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import usePathologies from "@/compositions/pathologies";
import Vue from "vue";
import { defineComponent, onMounted, ref, watch, reactive } from "@vue/composition-api";
import debounce from 'lodash/debounce';

export default defineComponent({
    name: "PathologyIndexPage",
    setup() {
        const modals = reactive({
            delete: {
                show: false,
                id: null,
            }
        });

        const { pathologies, loading, realtionItems, deletePathology, getPathologies, getRelationItems } = usePathologies()

        const search = ref('');

        onMounted(() => {
            getPathologies();
        });

        function showDeleteModal(id) {
            getRelationItems(id)
            modals.delete.id = id;
            modals.delete.show = true;
        }

        async function deleteItem(event, id) {
            const btn = event.target;
            const row = document.querySelector(`[data-row="${id}"]`)

            btn.disabled = true;

            row.style.opacity = '.6';
            row.style.pointerEvents = 'none';

            await deletePathology(id);
            Vue.toasted.success("Success");
            getPathologies();

            btn.disabled = false;

            modals.delete.show = false;
            modals.delete.id = null;
        }

        const onSearchChange = debounce(() => {
            getPathologies({
                search: search.value,
            });
        }, 500)

        watch(search, onSearchChange);

        return {
            modals,
            realtionItems,
            pathologies,
            loading,
            search,
            deleteItem,
            showDeleteModal,
        }
    }
})
</script>
