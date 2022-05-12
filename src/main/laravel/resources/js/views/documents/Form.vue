<template>
    <div class="mt-5 grid grid-cols-12 gap-5 items-start">
        <b-page-header title="Crea nuovo documento" />
        <div class="col-span-12 md:col-span-9 bg-white p-4 rounded-md">
            <h2 class="text-primary font-bold flex items-center text-xl">
                <input
                    v-model="form.title"
                    @blur="onTitleInputBlur"
                    class="bg-transparent border-primary font-bold"
                    :class="{ 'px-2 py-1 border w-full': editTitle }"
                    v-if="editTitle"
                />
                <err-msg :message="errors.title"></err-msg>
                <template v-if="!editTitle">
                    <span>{{ form.title }}</span>

                    <button class="ml-3" @click="editTitle = true">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
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
                    </button>
                </template>
            </h2>
            <div class="mt-2 space-x-4 text-sm" v-if="form.creator || form.created_at">
                <span>Creato da: {{ form.creator ? form.creator.name : '' }}</span>
                <span>Creato il: {{ form.created_at | datetime }}</span>
            </div>
            <div class="mt-4">
                <quill-editor v-model="form.body" :options="editorOptions"></quill-editor>
                <err-msg :message="errors.body"></err-msg>
            </div>

            <b-button
                class="mt-5 w-full py-2"
                @click="save"
                :disabled="loading"
            >Pubblica il documento</b-button>
        </div>
        <div class="col-span-12 md:col-span-3 space-y-5">
            <div class="bg-white p-4 rounded-md">
                <p class="text-lg font-bold">Patologie</p>
                <p class="mt-2 text-xs">Associa le patologie al tuo documento</p>
                <div class="mt-4">
                    <b-select
                        v-model="form.pathologies"
                        placeholder="Associa"
                        :options="filters.pathologies"
                        :reduce="item => item.id"
                        :multiple="true"
                        label="title"
                    ></b-select>
                    <err-msg :message="errors.pathologies"></err-msg>
                </div>
            </div>
            <div class="bg-white p-4 rounded-md">
                <p class="text-lg font-bold">Copertina articolo</p>
                <div class="mt-2 text-xs">
                    <b-file-uploader v-model="form.cover_image" accept="image/*"></b-file-uploader>
                    <err-msg :message="errors.cover_image"></err-msg>
                </div>
            </div>
            <div class="bg-white p-4 rounded-md">
                <p class="text-lg font-bold">Video</p>
                <p class="mt-2 text-xs">Inserisci il link del video</p>
                <div class="mt-4">
                    <input
                        v-model="form.youtube_url"
                        class="w-full border border-primary px-2 py-1 focus-within:outline-none"
                        type="url"
                    />
                    <err-msg :message="errors.youtube_url"></err-msg>
                </div>

                <div class="mt-4">
                    <p class="mt-2 text-xs">Allega un file pdf</p>
                    <div class="mt-2 text-xs">
                        <b-file-uploader v-model="form.file" accept="application/pdf"></b-file-uploader>
                        <span class="text-xs text-red-500">{{ errors.file }}</span>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-md">
                <p class="text-lg font-bold">Immagini</p>

                <div class="mt-2 text-xs">
                    <b-file-uploader v-model="form.images" :multiple="true" accept="image/*"></b-file-uploader>
                    <span class="text-xs text-red-500">{{ errors.images }}</span>
                </div>
            </div>

            <div class="text-gray-600 text-sm px-4" v-if="form.last_updator || form.updated_at">
                <p>Ultimo aggiornamento: {{ form.updated_at | datetime }}</p>
                <p>Aggiornato da: {{ form.last_updator ? form.last_updator.name : '' }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import useDocumentsFilter from '@/compositions/documentFilters';
import useDocuments from '@/compositions/documents';
import router from '@/router';
import 'quill/dist/quill.bubble.css';
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import Vue from 'vue';
import { computed, defineComponent, ref } from '@vue/composition-api';
import { quillEditor, editorOptions } from '@/components/Editor';

export default defineComponent({
    name: "DocumentFormPage",
    components: {
        quillEditor
    },
    setup(props, { root }) {
        const { document: form, saveDocument, getDocument, updateDocument, errors, loading } = useDocuments();
        const { getFilters, filters } = useDocumentsFilter()

        const editTitle = ref(false);

        let isCreating = computed(() => root.$route.name === 'documents.create');

        onCreated();

        function onCreated() {
            getFilters();

            form.title = "Untitled document";

            if (isCreating.value) return;

            getDocument(root.$route.params.id)
                .catch(err => {
                    if (err.response?.status === 404) {
                        router.back();
                    }

                    Vue.toasted.error(err?.response?.data?.message || err.message);
                })
        }

        function save() {
            let data = {
                title: form.title,
                body: form.body,
                youtube_url: form.youtube_url,
                cover_image: form.cover_image,
                file: form.file,
                images: form.images || [],
                pathologies: form.pathologies || [],
            };

            if (isCreating.value) {
                saveDocument(data)
                    .then(({ data: document }) => {
                        Vue.toasted.success('saved successfully');

                        router.push({
                            name: 'documents.edit',
                            params: { id: document.id }
                        })
                    })
                    .catch((err) => {
                        Vue.toasted.error(err?.response?.data?.message || err.message)
                    });


                return;
            }

            updateDocument(root.$route.params.id, data)
                .then(() => {
                    Vue.toasted.success('updated successfully');
                })
                .catch((err) => {
                    if (err.response?.status === 404) {
                        router.back();
                    }

                    Vue.toasted.error(err?.response?.data?.message || err.message)
                });

        }

        return {
            isCreating,
            editTitle,
            form,
            editorOptions,
            save,
            errors,
            filters,
            loading,
            onTitleInputBlur: () => {
                form.title = form.title.trim() ? form.title : 'Untitled document';
                editTitle.value = false
            }
        }
    }
})
</script>

<style>
.ql-container > .ql-editor {
    min-height: 500px;
}
</style>
