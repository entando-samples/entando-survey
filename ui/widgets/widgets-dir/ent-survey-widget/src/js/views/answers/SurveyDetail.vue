<template>
  <div>
    <b-page-header title="My Survey"></b-page-header>
    <!-- <div
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
    </div> -->

    <b-block class="mt-2 py-5"  title="Complete Survey:">
<!--      <ul class="list-disc text-red-500">-->
<!--        <li class="ml-8" v-for="(message, idx) in flatErrors" :key="idx">{{ message }}</li>-->
<!--      </ul>-->
    </b-block>

    <div class="mt-5">





        <b-block class="mt-4 py-5">
          <p
              v-if="loading"
              class="text-gray-500 text-center text-sm"
          >Loading questions....</p>
          <ul v-else-if="survey.questions.length" style="max-height: 500px;">
            <li
                class="border-t py-5 flex flex-col space-x-5 items-start"
                v-for="(question,questionIndex) in survey.questions"
                :key="question.id"
            >

              <label :for="'question-'+question.id">
                <p class="font-semibold text-primary">{{ question.title }}</p>
                <p class="text-sm mt-2">{{ question.description }}</p>
              </label>

              <ul class="flex flex-row my-2">
                <li v-for="answer in question.answers" class="mx-2">
                  <input type="radio" :id="answer.id"  v-model="finalAnswers[questionIndex].answer_id"  :value="answer.id">{{ answer.body }}
                </li>
              </ul>
            </li>
          </ul>
          <p v-else class="text-gray-500 text-center text-sm">
            <template v-if="fullSelectedQuestions.length < 0">No questions available</template>
            <template v-else>No questions selected</template>
          </p>
        </b-block>
        <div class="mt-4 text-right">
          <b-button @click="save">Conferma</b-button>
        </div>


    </div>
  </div>
</template>

<script>
import { computed, defineComponent, reactive,onBeforeMount, ref, watch } from "@vue/composition-api";

import useSurveys from "@/compositions/patients/surveys";

import router from "../../router";
import Vue from "vue";


export default defineComponent({
  name: "SurveyFormPage",

  setup(props, { root }) {
    const {survey, errors, getSurvey, loading,saveSurveyResponse } = useSurveys();
    const fullSelectedQuestions = reactive({});
     fullSelectedQuestions.value = computed(() => {
      return survey.questions
    })

    const finalAnswers = ref("");

    const flatErrors = computed(() => {
      return Object.entries(errors || {}).map(([key, message]) => {
        return message
      }) || [];
    })
    // onCreated();
    onBeforeMount(()=> {
      getSurvey(root.$route.params.id)
          .then(()=>{
            createFinalArray();
          })
          .catch(err => {
            if (err.response?.status === 404) {
              router.back();
            }

            Vue.toasted.error(err?.response?.data?.message || err.message);
          });

    })

    function createFinalArray()
    {
      finalAnswers.value = survey.questions.map((question,index)=>{
        return {
          'question_id':question.id,
          'answer_id':null
        }
      });
      console.log('answers',survey.questions);
      console.log('final',finalAnswers);

    }
    function save() {
      saveSurveyResponse(finalAnswers,survey.id).then(res=>{
        this.$router.push('/surveys')
      });
    }

    return {
      loading,
      flatErrors,
      finalAnswers,
      // filters,
      fullSelectedQuestions,
      survey,
      save,

    }
  }
})
</script>

<style>
.ql-con .ql-con .ql-container > .ql-editor {
  min-height: 250px;
}
