<template>
    <div>
        <file-pond
            ref="pond"
            label-idle="Drag & Drop your files or <u>Browse</u>"
            :icon-remove="getRemoveIcon"
            :icon-retry="getRetryIcon"
            :allow-multiple="multiple"
            :accepted-file-types="accept || ''"
            :server="serverOptions"
            :files="files"
            :allowReorder="true"
            :instant-upload="true"
            :max-parallel-uploads="500"
            @processfile="emitFiles"
            @removefile="emitFiles"
            :onreorderfiles="emitFiles"
            @activatefile="openFile"
        />
    </div>
</template>


<script>
import { defineComponent, computed, ref } from "@vue/composition-api";

import client from '@/http';

import vueFilePond from "vue-filepond";

import "filepond/dist/filepond.min.css";

import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';


const FilePond = vueFilePond(FilePondPluginImagePreview, FilePondPluginFileValidateType);

export default defineComponent({
    name: "BaseFileUploader",
    props: {
        value: [String, Array],
        multiple: {
            multiple: {
                type: Boolean,
                default: false
            },
        },
        accept: String,
    },
    components: { FilePond },
    setup(props, { emit }) {
        const pond = ref(null);

        const files = computed(() => {
            if (!props.value) return [];

            if (!Array.isArray(props.value)) {
                return [
                    {
                        source: props.value,
                        options: { type: "local" }
                    }
                ];
            }

            return (props.value || []).map(url => {
                return {
                    source: url,
                    options: { type: "local" }
                };
            });
        });

        function getFiles(files = null) {
            let data = (files || pond.value.getFiles())
                .map(item => item.serverId)
                .filter(item => item);

            if (props.multiple) return data;

            return data[0] || null;
        }

        return {
            pond,
            files,
            serverOptions: {
                url: "/api/v1/uploads",
                process: {
                    withCredentials: true
                },
                load: (source, load, err) => {
                    client('stream?path=' + source, {
                        responseType: 'blob',
                    })
                        .then(function (response) {
                            load(response.data);
                        })
                        .catch((error) => {
                            console.log(error);
                            err("Failed to load image")
                        });
                }
            },
            emitFiles() {
                if ([3, 9].includes(pond.value._pond.status)) {
                    return;
                }
                emit("input", getFiles());
            },
            openFile(file) {
                if (typeof file.source !== "string") return;
                const win = window.open(file.source);

                win.focus();
            },
            getRetryIcon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-refresh" width="26"><circle style="fill:none" cx="12" cy="12" r="10"/><path style="fill:white" d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"/></svg>',
            getRemoveIcon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" class="icon-close-circle"><circle style="fill:none" cx="12" cy="12" r="10"/><path style="fill:white" d="M13.41 12l2.83 2.83a1 1 0 0 1-1.41 1.41L12 13.41l-2.83 2.83a1 1 0 1 1-1.41-1.41L10.59 12 7.76 9.17a1 1 0 0 1 1.41-1.41L12 10.59l2.83-2.83a1 1 0 0 1 1.41 1.41L13.41 12z"/></svg>',
            getPlaceholderLabel: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="35" class="icon-cloud-upload mr-3"><path class="fill-dark-gray" d="M18 14.97c0-.76-.3-1.51-.88-2.1l-3-3a3 3 0 0 0-4.24 0l-3 3A3 3 0 0 0 6 15a4 4 0 0 1-.99-7.88 5.5 5.5 0 0 1 10.86-.82A4.49 4.49 0 0 1 22 10.5a4.5 4.5 0 0 1-4 4.47z"/><path class="fill-dark-gray" d="M11 14.41V21a1 1 0 0 0 2 0v-6.59l1.3 1.3a1 1 0 0 0 1.4-1.42l-3-3a1 1 0 0 0-1.4 0l-3 3a1 1 0 0 0 1.4 1.42l1.3-1.3z"/></svg> Drop files or click here to upload'
        }
    }
})
</script>
