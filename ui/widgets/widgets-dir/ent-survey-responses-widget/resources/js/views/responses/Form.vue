<template>
    <div>
        <b-page-header title="Crea un nuovo questionario"></b-page-header>
        <div
            class="grid grid-flow-col bg-gray-200 uppercase text-gray-500 text-sm rounded-md multistep"
        >
            <div
                class="flex items-center space-x-3 px-4 border-r border-gray-300 py-5 active"
            >
                <span
                    class="border border-gray-500 rounded-full w-8 h-8 flex justify-center items-center"
                >1</span>
                <p>scegli le domande</p>
            </div>
        </div>

        <b-block class="mt-2 py-5" v-if="flatErrors.length" title="Errors:">
            <ul class="list-disc text-red-500">
                <li class="ml-8" v-for="(message, idx) in flatErrors" :key="idx">{{ message }}</li>
            </ul>
        </b-block>

        <div class="mt-5">
            <div>
                <div class="mt-5 flex items-center space-x-4">
                    <label for="search">Cerca tra le domande:</label>
                    <input
                        class="w-80 border border-primary px-2 py-1 focus-within:outline-none"
                        type="search"
                        v-model="formQuestionFilters.search"
                    />
                </div>

                <div class="mt-5 flex items-center gap-x-10">
                    <span>Filtra Per</span>
                    <div class="flex items-center space-x-4">
                        <b-select
                            v-model="formQuestionFilters.questions"
                            class="inline-block w-80"
                            placeholder="Question"
                            :options="questionsFilter.questions"
                            :multiple="true"
                        ></b-select>
                    </div>
                    <div class="flex items-center space-x-4">
                        <b-select
                            v-model="formQuestionFilters.protocols"
                            class="inline-block w-80"
                            placeholder="Protocol"
                            :options="questionsFilter.protocols"
                            :multiple="true"
                        ></b-select>
                    </div>
                </div>

                <div class="mt-8 flex items-center space-x-10 text-sm font-semibold">
                    <label class="flex items-center space-x-2">
                        <input
                            class="h-5 w-5"
                            name="question-list"
                            value="all"
                            type="radio"
                            v-model="questionListType"
                        />
                        <span>Scorri tra domande</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input
                            class="h-5 w-5"
                            name="question-list"
                            value="selected"
                            v-model="questionListType"
                            type="radio"
                        />
                        <span>Domande selezionate</span>
                    </label>
                </div>

                <b-block class="mt-4 py-5">
                    <p
                        v-if="loadingQuestions"
                        class="text-gray-500 text-center text-sm"
                    >Loading questions....</p>
                    <ul v-else-if="listableQuestions.length" style="max-height: 500px;">
                        <li
                            class="border-t py-5 flex space-x-5 items-start"
                            v-for="question in listableQuestions"
                            :key="question.id"
                        >
                            <div>
                                <input
                                    :id="`question-${question.id}`"
                                    type="checkbox"
                                    :value="question.id"
                                    v-model="form.questions"
                                />
                            </div>
                            <label :for="`question-${question.id}`">
                                <p class="font-semibold text-primary">{{ question.title }}</p>
                                <p class="text-sm mt-2">{{ question.description }}</p>
                            </label>
                        </li>
                    </ul>
                    <p v-else class="text-gray-500 text-center text-sm">
                        <template v-if="questionListType === 'all'">No questions available</template>
                        <template v-else>No questions selected</template>
                    </p>
                </b-block>
                <div class="mt-4 text-right">
                    <b-button @click="save" :disabled="loading">Salva questionario</b-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { computed, defineComponent, reactive, ref, watch } from "@vue/composition-api";
import useQuestions from "@/compositions/questions";
import debounce from 'lodash/debounce';
import useSurveys from "@/compositions/surveys";
import 'quill/dist/quill.bubble.css';
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.snow.css';
import { quillEditor } from 'vue-quill-editor';
import useDocumentsFilter from "@/compositions/documentFilters";
import router from "../../router";
import Vue from "vue";

const editorOptions = {
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline',],
            [{ 'align': [] }, { 'indent': '-1' }, { 'indent': '+1' }],
            ['link'],
        ]
    },
    placeholder: 'Write here...',
    theme: 'snow'
};

export default defineComponent({
    name: "ResponsesFormPage",
    components: { quillEditor },
    setup(props, { root }) {
        const editTitle = ref(false);
        const questionListType = ref("all");
        const { survey: form, saveSurvey, errors, getSurvey, updateSurvey, loading } = useSurveys();
        const { loading: loadingQuestions, getQuestions, questions, filters: questionsFilter, getFilters: getQuestionsFilter } = useQuestions();
        const { filters, getFilters } = useDocumentsFilter();

        const formQuestionFilters = reactive({
            search: '',
            questions: null,
            protocols: null,
        });

        const isCreating = computed(() => root.$route.name === 'surveys.create');

        const fullSelectedQuestions = computed(() => {
            return questions.data.filter(item => {
                return form.questions.includes(item.id);
            })
        })

        const flatErrors = computed(() => {
            return Object.entries(errors || {}).map(([key, message]) => {
                return message
            }) || [];
        })

        onCreated();

        function onCreated() {
            getQuestionsFilter();
            getQuestions({ paginated: false, withAnswers: true });
            getFilters();

            if (isCreating.value) return;

            getSurvey(root.$route.params.id)
                .catch(err => {
                    if (err.response?.status === 404) {
                        router.back();
                    }

                    Vue.toasted.error(err?.response?.data?.message || err.message);
                })
        }

        const onFormQuestionFiltersChange = debounce(() => {
            getQuestions({
                ...formQuestionFilters
            });
        }, 500)

        watch(formQuestionFilters, onFormQuestionFiltersChange)

        function save() {
            if (isCreating.value) {
                saveSurvey(form)
                    .then(res => {
                        Vue.toasted.success("saved successfully");
                        router.push({
                            name: "surveys.index"
                        })
                    })
                    .catch((err) => {
                        Vue.toasted.error(err?.response?.data?.message || err.message)
                    });

                return
            }

            updateSurvey(root.$route.params.id, form)
                .then(res => {
                    Vue.toasted.success("updated successfully");
                    router.push({
                        name: "surveys.index"
                    })
                })
                .catch((err) => {
                    Vue.toasted.error(err?.response?.data?.message || err.message)
                });
        }

        return {
            loading,
            questionListType,
            listableQuestions: computed(() => {
                if (questionListType.value === "all") {
                    return questions.data;
                }

                return questions.data.filter(item => {
                    return form.questions.includes(item.id);
                })
            }),
            questionsFilter,
            isCreating,
            flatErrors,
            formQuestionFilters,
            filters,
            loadingQuestions,
            questions,
            fullSelectedQuestions,
            editorOptions,
            form,
            editTitle,
            save,
            onTitleInputBlur: () => {
                form.title = form.title.trim() ? form.title : 'Untitled survey';
                editTitle.value = false
            },
            selectAllPathologies: () => {
                form.pathologies = filters.pathologies.map((item) => item.id)
            },
        }
    }
})
</script>

<style>
.ql-con .ql-con .ql-container > .ql-editor {
    min-height: 250px;
}
