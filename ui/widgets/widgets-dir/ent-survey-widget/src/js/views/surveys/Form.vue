<template>
    <div>
        <b-page-header title="Crea un nuovo questionario"></b-page-header>
        <div
            class="grid grid-flow-col bg-gray-200 uppercase text-gray-500 text-sm rounded-md multistep"
        >
            <div
                class="flex items-center space-x-3 px-4 border-r border-gray-300 py-5"
                :class="{ active: (activeStep == 1) }"
            >
                <span
                    class="border border-gray-500 rounded-full w-8 h-8 flex justify-center items-center"
                >1</span>
                <p>scegli le domande</p>
            </div>
            <div
                class="flex items-center space-x-3 px-4 border-r border-gray-300 py-5"
                :class="{ active: (activeStep == 2) }"
            >
                <span
                    class="border border-gray-500 rounded-full w-8 h-8 flex justify-center items-center"
                >2</span>
                <p>imposta gli alert</p>
            </div>
            <div
                class="flex items-center space-x-3 px-4 py-5"
                :class="{ active: (activeStep == 3) }"
            >
                <span
                    class="border border-gray-500 rounded-full w-8 h-8 flex justify-center items-center"
                >3</span>
                <p>salva il questionario</p>
            </div>
        </div>

        <b-block class="mt-2 py-5" v-if="flatErrors.length" title="Errors:">
            <ul class="list-disc text-red-500">
                <li class="ml-8" v-for="(message, idx) in flatErrors" :key="idx">{{ message }}</li>
            </ul>
        </b-block>

        <div class="mt-5">
            <div v-if="activeStep == 1">
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
                    <b-button :disabled="form.questions.length < 1" @click="activeStep++">Conferma</b-button>
                </div>
            </div>
            <div v-if="activeStep == 2">
                <b-block>
                    <ul class="space-y-3" style="max-height: 600px;">
                        <li
                            class="border-t py-5 flex space-x-5 items-start"
                            v-for="(question, index) in fullSelectedQuestions"
                            :key="question.id"
                        >
                            <p class="text-primary">{{ index + 1 }}</p>
                            <div>
                                <p class="text-primary">{{ question.title }}</p>
                                <p class="mt-1 text-sm">{{ question.description }}</p>
                                <ul class="mt-2 text-sm space-y-3">
                                    <li
                                        v-for="answer in question.answers"
                                        class="flex items-center space-x-6"
                                    >
                                        <div
                                            class="border border-primary rounded-sm p-2 w-80"
                                        >{{ answer.body }}</div>
                                        <div class="flex items-center">
                                            <input
                                                class="h-4 w-4"
                                                type="checkbox"
                                                :value="answer.id"
                                                v-model="form.warning_answers"
                                            />
                                            <p
                                                class="ml-2 text-xs"
                                            >Imposta un`alert per questa risposta</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </b-block>
                <div class="mt-4 text-right">
                    <b-button variant="primary-alt" @click="activeStep--">Indietro</b-button>
                    <b-button @click="activeStep++">Conferma</b-button>
                </div>
            </div>
            <div v-if="activeStep == 3">
                <b-block>
                    <h2 class="text-primary font-bold flex items-center text-xl">
                        <input
                            v-model="form.title"
                            @blur="onTitleInputBlur"
                            class="bg-transparent border-primary font-bold"
                            :class="{ 'px-2 py-1 border w-full': editTitle }"
                            v-if="editTitle"
                        />
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
                    <div class="mt-8">
                        <p class="text-sm font-semibold">Associa le patologie al questionario</p>
                        <div class="mt-4 space-x-5">
                            <label>Cerca tra le patalogie:</label>
                            <b-select
                                v-model="form.pathologies"
                                class="inline-block w-80"
                                placeholder="Patologia"
                                label="title"
                                :options="filters.pathologies"
                                :reduce="item => item.id"
                                :multiple="true"
                            ></b-select>
                            <b-button @click="selectAllPathologies">select all</b-button>
                        </div>
                    </div>
                    <div class="mt-8">
                        <p class="text-sm font-semibold">Description</p>
                        <quill-editor
                            class="mt-2"
                            v-model="form.description"
                            :options="editorOptions"
                        ></quill-editor>
                    </div>
                </b-block>
                <div class="mt-4 text-right">
                    <b-button variant="primary-alt" @click="activeStep--">Indietro</b-button>
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
    name: "SurveyFormPage",
    components: { quillEditor },
    setup(props, { root }) {
        const editTitle = ref(false);
        const questionListType = ref("all");
        const { survey: form, saveSurvey, errors, getSurvey, updateSurvey, loading } = useSurveys();
        const activeStep = ref(1);
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
            activeStep,
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
