<template>
    <div>
        <b-page-header title="Gestione domande dei questionari" />

        <div class="flex items-start space-x-7">
            <div class="w-2/5 space-y-2">
                <p class="text-sm">Ricerca per nome</p>
                <div class="border border-primary py-1 px-2 bg-white flex items-center">
                    <input
                        class="w-full bg-transparent"
                        v-model="filterParameters.search"
                        type="text"
                    />
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
            <div class="w-3/5 space-y-2">
                <p class="text-sm">Filtra per</p>
                <div class="flex items-start space-x-3">
                    <div class="w-1/3">
                        <b-select
                            v-model="filterParameters.questions"
                            placeholder="Question"
                            :options="filters.questions"
                            :searchable="true"
                            :multiple="true"
                        ></b-select>
                    </div>
                    <div class="w-1/3">
                        <b-select
                            v-model="filterParameters.protocols"
                            placeholder="Protocol"
                            :options="filters.protocols"
                            :multiple="true"
                        ></b-select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-row-reverse">
            <router-link to="/questions/create">
                <b-button class="py-2">Crea nuova domanda</b-button>
            </router-link>
        </div>
        <b-block class="mt-5">
            <table class="table-auto w-full text-sm">
                <thead class="uppercase text-white bg-ternary">
                    <tr class="font-semibold text-left">
                        <td class="p-2">key</td>
                        <td class="p-2">title</td>
                        <td class="p-2">description</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="!loading">
                        <tr v-if="!questions.data.length">
                            <td colspan="4">
                                <div class="text-gray-600 text-center py-3">No questions available</div>
                            </td>
                        </tr>
                        <tr
                            v-for="(question,index) in questions.data"
                            :key="question.id"
                            :class="index % 2 === 0 ? 'bg-gray-100' : ''"
                        >
                            <td class="px-2 py-3">
                                <div class="text-primary font-semibold">{{ question.key }}</div>
                            </td>
                            <td class="px-2 py-3">
                                <div class="text-primary font-semibold">{{ question.title }}</div>
                            </td>
                            <td class="px-2 py-3">
                                <div>{{ question.description }}</div>
                            </td>
                            <td class="px-2 py-3 flex justify-center">
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="modals.answers.data = question.answers; modals.answers.show = true"
                                        class="whitespace-nowrap px-5 font-semibold text-white bg-primary rounded p-1 border-2 border-primary"
                                    >See Answers</button>

                                    <button
                                        @click="deleteItem($event, question.id)"
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
                    <template v-else-if="loading">
                        <tr>
                            <td colspan="4">
                                <div class="text-gray-600 text-center py-3">Loading questions....</div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </b-block>
        <div class="mt-7">
            <div class="flex flex-row-reverse">
                <b-paginator @change="onPageChange" :data="questions.meta" v-if="questions.meta"></b-paginator>
            </div>
        </div>

        <b-modal
            size="3xl"
            title="Answers"
            v-if="modals.answers.show"
            @close="modals.answers.show = false"
        >
            <ul class="list-disc ml-5" v-if="modals.answers.data">
                <li :key="answer.id" v-for="answer in modals.answers.data">{{ answer.body }}</li>
            </ul>
            <p
                v-else
                class="text-sm text-center text-gray-600"
            >No answers available for the selected question</p>
        </b-modal>
    </div>
</template>

<script>
import { defineComponent, ref, watch, reactive } from "@vue/composition-api";
import useQuestions from "@/compositions/questions";
import debounce from 'lodash/debounce';
import Vue from "vue";

export default defineComponent({
    name: "QuestionIndexPage",
    setup() {
        const modals = reactive({
            answers: {
                show: false,
                data: [],
            }
        });

        const filterParameters = reactive({
            search: '',
            questions: '',
            protocols: '',
        });

        const { getQuestions, questions, loading, deleteQuestion, getFilters, filters } = useQuestions()

        onCreated();

        function onCreated() {
            getFilters();
            getQuestions();
        };

        const onFilterParametersChange = debounce(() => {
            getQuestions({
                ...filterParameters,
                withAnswers: true,
            });
        }, 500)

        watch(filterParameters, onFilterParametersChange)

        async function deleteItem(event, id) {
            const closestRow = event.target.closest('tr');

            if (!confirm("Are you sure ?")) return;

            closestRow.style.opacity = '.6';
            closestRow.style.pointerEvents = 'none';

            await deleteQuestion(id);
            Vue.toasted.success("Success");
            getQuestions();
        }

        function onPageChange({ page }) {
            getQuestions({
                ...filterParameters,
                page,
            })
        }

        return {
            filters,
            modals,
            filterParameters,
            questions,
            loading,
            deleteItem,
            onPageChange
        }
    }
})
</script>
