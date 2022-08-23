<template>
    <div>
        <b-page-header title="Nuovo questionario"></b-page-header>
        <b-block class="mt-2 py-5" v-if="flatErrors.length" title="Errors:">
            <ul class="list-disc text-red-500">
                <li class="ml-8" v-for="(message, idx) in flatErrors" :key="idx">
                    {{ message }}
                </li>
            </ul>
        </b-block>
        <div v-if="!loading" class="mt-5">
            <b-block>
                <ul class="space-y-3" style="max-height: 600px;">
                    <li
                        v-for="(question, index) in fullSelectedQuestions"
                        :key="question.id"
                        class="py-5 flex space-x-5 items-start"
                        :class="{'border-t': index > 0}"
                    >
                        <p class="text-primary">
                            {{ index + 1 }}
                        </p>
                        <div>
                            <p class="text-primary">
                                {{ question.title }}
                            </p>
                            <p class="mt-1 text-sm">
                                {{ question.description }}
                            </p>
                            <ul class="mt-2 text-sm space-y-3">
                                <li
                                    v-for="(answer, answerIndex) in question.answers"
                                    :key="answerIndex"
                                    class="flex items-center space-x-6"
                                >
                                    <div 
                                        :class="{'bg-primary text-white': listOfAnswers.find(a => a.answerId === answer.id)}" 
                                        class="border border-primary rounded-sm p-2 w-80 hover:bg-primary hover:text-white cursor-pointer" 
                                        @click="sendAnswer(question, answer)"
                                    >
                                        {{ answer.body }}
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </b-block>
            <div class="mt-4 text-right">
                <b-button @click="save" :disabled="loading">
                    Salva questionario
                </b-button>
            </div>
        </div>
        <template v-else-if="loading">
            <tr>
              <td colspan="5">
                <div class="text-gray-600 text-center py-3">
                  Loading survey....
                </div>
              </td>
            </tr>
        </template>
    </div>
</template>

<script>
import { computed, defineComponent, reactive, ref, watch } from "@vue/composition-api";
import useQuestions from "@/compositions/questions";
import debounce from "lodash/debounce";
import useSurveys from "@/compositions/surveys";
import router from "../../router";
import Vue from "vue";

export default defineComponent({
  name: "SurveyFormPage",
  setup(props, { root }) {
    const {
      survey: form,
      errors,
      getSurvey,
      answerSurvey,
      loading,
    } = useSurveys();

    const {
      loading: loadingQuestions,
      getQuestions,
      questions,
      filters: questionsFilter,
      getFilters: getQuestionsFilter,
    } = useQuestions(); 

    const formQuestionFilters = reactive({
      search: "",
    });

    const fullSelectedQuestions = computed(() => {
      const currentQuestions = questions.data.filter((item) => {
        return form.questions.includes(item.id);
      });
      return currentQuestions;
    });

    const listOfAnswers = ref([]);
    
    const flatErrors = computed(() => {
      return (
        Object.entries(errors || {}).map(([key, message]) => {
          return message;
        }) || []
      );
    });

    onCreated();

    function onCreated() {
      getQuestionsFilter();
      getQuestions({ paginated: false, withAnswers: true });
      getSurvey(root.$route.params.id).catch((err) => {
        if (err.response?.status === 404) {
          router.back();
        }
        Vue.toasted.error(err?.response?.data?.message || err.message);
      });
    }

    const onFormQuestionFiltersChange = debounce(() => {
      getQuestions({
        ...formQuestionFilters,
      });
    }, 500);

    watch(formQuestionFilters, onFormQuestionFiltersChange);

    async function save() {
        for (let index = 0; index < listOfAnswers.value.length; index++) {
          const element = listOfAnswers.value[index];
          await answerSurvey(root.$route.params.id, element.questionId, {answer: element.answerId})
            .catch((err) => {
              Vue.toasted.error(err?.response?.data?.message || err.message);
            });
        }
        Vue.toasted.success("Saved successfully");
        router.push({
          name: "surveys.index",
        });
    }

    function sendAnswer(question, answer) {
        const exist = listOfAnswers.value.find(item => item.questionId === question.id);
        if (exist) { 
            listOfAnswers.value.splice(exist, 1);
            listOfAnswers.value.push({ questionId: question.id, answerId: answer.id});
        } else {
            listOfAnswers.value.push({ questionId: question.id, answerId: answer.id});
        }
    }

    return {
      loading,
      questionsFilter,
      flatErrors,
      formQuestionFilters,
      loadingQuestions,
      questions,
      fullSelectedQuestions,
      form,
      listOfAnswers,
      save,
      sendAnswer,
    };
  },
});
</script>

<style>
.ql-con .ql-con .ql-container > .ql-editor {
  min-height: 250px;
}
