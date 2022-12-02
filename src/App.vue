<script setup>
import {ref, defineAsyncComponent} from 'vue'
import { useQuizStore } from './store/quizStore'
const quizApp = defineAsyncComponent(() => import("./components/quizApp.vue"))

const quizStore = useQuizStore()

const quizData = ref({});
const loaded = ref(false);
// FOR TESTING
let quizUrl = "./testapi/quiz1.json"
if(typeof quiz_data !== 'undefined'){
  quizUrl = quiz_data.quizurl
}

const initQuiz = async () => {
  // demo json data for development mode
  if(import.meta.env.MODE == 'development'){
    await fetch(quizUrl)
    .then(response => response.json())
    .then(data => quizData.value = data)
    .then(data => quizStore.quizData = data)
    .then(loaded.value = true)
  } else {
    quizStore.quizData = JSON.parse(quiz_data.quizjson)
    loaded.value = true
  }
}

initQuiz()

</script>

<template>
  <div v-if="loaded" class="quizWrapper">
    <quizApp :quizData="quizData" />
  </div>
</template>

<style>
.quizWrapper{
  max-width: 950px;
  margin: 0 auto;
}
</style>
