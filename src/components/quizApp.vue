<script setup>
import {reactive, ref, onMounted} from 'vue';
import quizReport from '@/components/quizReport.vue'
import feedbackForm from '@/components/feedbackForm.vue'
import { useQuizStore } from '@/store/quizStore'
import { useI18n } from 'vue-i18n'
const quizStoredData = useQuizStore()
const {t} = useI18n({})

const props = defineProps({
  quizData:{
    type: Object,
    default: {}
  }
})

function changeQuestion(type = 'next'){
  // reset variables when question is changed
  quizStoredData.loading = true;
  quizStoredData.selectedAnswer = [];
  quizStoredData.currentCheckboxAnswer = [];
  quizStoredData.currentSelectedText = '';
  quizStoredData.answered = false;
  quizStoredData.showGuide = false;
  quizStoredData.formSubmitMessage = '';

  let questionIndex = 0;
  if (quizStoredData.questionIndex < quizStoredData.questionsLenght) {
    if(type == 'next'){
      quizStoredData.questionIndex++;
      questionIndex = quizStoredData.quizData.questions[quizStoredData.questionIndex];
      quizStoredData.currentQuestion = questionIndex;

      if(!quizStoredData.currentQuestion.showSelectionText){ // if showSelectionText is missing
          quizStoredData.currentQuestion.showSelectionText = false;
      }

      if(quizStoredData.reportData.answers.length > quizStoredData.questionIndex) {
        getQuestionAnswers();
      }
    } else {
      quizStoredData.questionIndex--;
      questionIndex = quizStoredData.quizData.questions[quizStoredData.questionIndex];
      quizStoredData.currentQuestion = questionIndex;

      if(!quizStoredData.currentQuestion.showSelectionText){ // if showSelectionText is missing
          quizStoredData.currentQuestion.showSelectionText = false;
      }
      getQuestionAnswers();

    }
    // clean answer texts
    quizStoredData.currentQuestion.choices.forEach((element, index) => {
      element.currentSelectedText = ""
    });
  }

  transitionTimeout();
}

// load current answers if already answered
function getQuestionAnswers(){
    const answerIndex = quizStoredData.reportData.answers[quizStoredData.questionIndex].answersIndex;
    if(quizStoredData.currentQuestion.questionType == 'checkbox'){
        quizStoredData.currentCheckboxAnswer = answerIndex;
    }
    if(quizStoredData.currentQuestion.questionType == 'trueFalse' || quizStoredData.currentQuestion.questionType == 'radio'){
        quizStoredData.selectedAnswer = answerIndex;
    }
    if(quizStoredData.currentQuestion.questionType === 'radio' && quizStoredData.currentQuestion.showSelectionText == false && quizStoredData.selectedAnswer.length !== 0) {
      submitAnswer(quizStoredData.selectedAnswer);
    }
}

function transitionTimeout(timer = 500){
  setTimeout(function() {
    quizStoredData.loading = false;
  }, timer);
}

const startQuiz = function(){
  // initial settings when quiz starts
  quizStoredData.reportData.quizName = quizStoredData.quizData.quizName
  quizStoredData.reportData.showScoring = quizStoredData.quizData.showScoring
  quizStoredData.reportData.reportPageTitle = quizStoredData.quizData.reportPageTitle
  quizStoredData.quizSection = 2;
  quizStoredData.currentQuestion = quizStoredData.quizData.questions[0];
  quizStoredData.loading = true;
  quizStoredData.showIntro = false;
  quizStoredData.questionsLenght = Object.keys(quizStoredData.quizData.questions).length
  transitionTimeout(400);
}

function selectButtonAnswer(answer){
  quizStoredData.selectedAnswer = answer;
  if(!quizStoredData.currentQuestion.showSelectionText){
    submitAnswer(answer);
  }
}

function submitAnswer(answer){
  quizStoredData.selectedAnswer = answer[0];
  if(answer){
    quizStoredData.currentAnswer = answer
  }

  let curSelectedTexts = []
  let answerActions = []
  let allChoices = []

  quizStoredData.currentQuestion.choices.forEach((element, index) => {
    allChoices.push(element.option)
    if(quizStoredData.currentAnswer.includes(index)){
      quizStoredData.currentSelectedText = element.selectedText
    }
    // push answers to report data
    curSelectedTexts.push(element.selectedText)
    answerActions.push(element.selectedActions)
  });

  // Count the total score
  quizStoredData.currentAnswer.forEach(element => {
    let ratio = quizStoredData.currentQuestion.choices[element].answerRatio
    let calcScore = (quizStoredData.currentQuestion.questionFullScore / 100 ) * ratio
    if(calcScore >= 0 ){
      quizStoredData.scoreIndex[quizStoredData.questionIndex] = calcScore;
      quizStoredData.score = calcTotalScore()
      quizStoredData.reportData.score = calcTotalScore()
    }
  });

  function calcTotalScore(){
    let sum = 0;
    for (const value of quizStoredData.scoreIndex) {
      sum += value;
    }
    return sum;
  }

  quizStoredData.reportData.answers.splice(quizStoredData.questionIndex, 1, {
    num: quizStoredData.questionIndex+1,
    question: quizStoredData.currentQuestion.question,
    questionType: quizStoredData.currentQuestion.questionType,
    answersIndex: quizStoredData.currentAnswer,
    answersTexts: allChoices,
    answersActions: answerActions,
    answerSelectedTexts: curSelectedTexts,
  });

  quizStoredData.answered = true;
  quizStoredData.currentAnswer = [];
  getScoringText();
}

function showResults(){
    quizStoredData.quizSection = 3;
    transitionTimeout();
    document.querySelector('#quizApp').scrollIntoView({
      behavior: 'smooth'
    });
}

function getScoringText(){
  Object.entries(quizStoredData.quizData.scoring).forEach((entry, key) => {
    let arr = entry[1].scores
    let nro = quizStoredData.score
    if(nro <= arr[1] && nro >= arr[0]) {
      quizStoredData.reportData.scoreText = entry[1].scoreText
      quizStoredData.reportData.scoreTextLinks = entry[1].scoreTextLinks
    }
  });
}
// Checkbox field check
function checkBoxSelected(index){
  if(quizStoredData.currentCheckboxAnswer.includes(index)){
    return true
  }
}

</script>
<template>
  <div class="quizH1" :class="{'text-tsgray-medium' : (quizStoredData.quizSection == 2)}">{{quizStoredData.quizData.quizName}}</div>
  <div v-if="quizStoredData.quizSection == 1">
    <p class="quizParagraph">{{quizStoredData.quizData.introText}}</p>
    <div class="text-center my-4">
      <button class="quizBtn" v-on:click.prevent="startQuiz()">{{quizStoredData.quizData.introButtonText}}</button>
    </div>
  </div>

  <div v-if="quizStoredData.quizSection == 2">
      <transition name="fade" mode="out-in">
      <form v-if="!quizStoredData.loading">
          <div class="flex justify-between">
            <div>
              <div v-if="quizStoredData.currentQuestion.guideText">
                <a href="#" @click.prevent="quizStoredData.showGuide = !quizStoredData.showGuide" class="text-sm font-bold no-underline flex items-center hover:underline text-tsprimary" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#7c3987" class="mr-2"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-.001 5.75c.69 0 1.251.56 1.251 1.25s-.561 1.25-1.251 1.25-1.249-.56-1.249-1.25.559-1.25 1.249-1.25zm2.001 12.25h-4v-1c.484-.179 1-.201 1-.735v-4.467c0-.534-.516-.618-1-.797v-1h3v6.265c0 .535.517.558 1 .735v.999z"/></svg> {{ $t("info_about_question") }}
                </a>
              </div>
            </div>
            <div class="text-sm bg-tspink py-2 px-4 rounded-md font-bold text-tsgray-dark"> {{ t("question") }} {{quizStoredData.questionIndex + 1}}/{{quizStoredData.questionsLenght}}</div>
          </div>
          <transition name="fade">
            <div v-if="quizStoredData.showGuide" class="my-4 text-sm leading-5 border border-tsgray-light p-4 bg-tsgray-light rounded-lg" v-html="quizStoredData.currentQuestion.guideText" ></div>
          </transition>
          <div class="quizH2">{{ quizStoredData.currentQuestion.question }}</div>

          <div v-if="quizStoredData.currentQuestion.questionType == 'trueFalse'" class="quizButtonsHolder">

            <div v-if="quizStoredData.currentQuestion.showSelectionText">
              <div class="flex">
                  <div v-for="(c, index) of quizStoredData.currentQuestion.choices" :key="index">
                  <button :disabled="quizStoredData.answered && quizStoredData.currentQuestion.showSelectionText"  :class='[{hidden : (index!=quizStoredData.selectedAnswer && quizStoredData.answered)}, {quizBtnSelected : index == quizStoredData.selectedAnswer && quizStoredData.selectedAnswer.length }]' class="quizBtn mr-4" :value="[index]" @click.prevent="selectButtonAnswer([index])">{{c.option}}</button>
                </div>
              </div>
              <transition name="fade">
                <div v-if="quizStoredData.currentSelectedText && quizStoredData.currentQuestion.showSelectionText" class="quizP currentSelectedText" v-html="quizStoredData.currentSelectedText"></div>
              </transition>
              <div v-if="!quizStoredData.answered && quizStoredData.selectedAnswer.length" class="text-center">
                <button class="quizBtn" type="button" @click="submitAnswer(quizStoredData.selectedAnswer)">{{ t("answer_question") }}</button>
              </div>
            </div>

            <div v-else>
              <div class="flex">
                  <div v-for="(c, index) of quizStoredData.currentQuestion.choices" :key="index">
                  <button :class='[{quizBtnSelected : index === quizStoredData.selectedAnswer }]' class="quizBtn mr-4" :value="[index]" @click.prevent="selectButtonAnswer([index])">{{c.option}}</button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="quizStoredData.currentQuestion.questionType == 'radio'" class="quizRadioButtonsHolder">
              <div v-if="quizStoredData.currentQuestion.showSelectionText">
                <div class="radio" v-for="(c, index) of quizStoredData.currentQuestion.choices" :key="`option-${index}`" :class='[{hidden : (index!=quizStoredData.selectedAnswer && quizStoredData.answered)}]'>
                  <input :disabled="quizStoredData.answered" type="radio" :id="`option-${index}`" :checked="index == parseInt(quizStoredData.selectedAnswer)" name="radioButtons" class="quizRadio" @change="selectButtonAnswer([index])" :key="`option-${index}`">
                  <label class="radio-label" :for="`option-${index}`" >
                    {{c.option}}
                  </label>
                  <transition name="fade">
                    <div v-if="index == quizStoredData.selectedAnswer && quizStoredData.currentQuestion.showSelectionText && quizStoredData.answered" class="quizP currentSelectedText mt-4" v-html="quizStoredData.currentSelectedText"></div>
                  </transition>
                </div>
                <div v-if="!quizStoredData.answered && quizStoredData.selectedAnswer.length" class="text-center">
                  <button class="quizBtn" type="button" @click="submitAnswer(quizStoredData.selectedAnswer)">{{ t("answer_question") }}</button>
                </div>
              </div>
              <div v-else>
                <div class="radio" v-for="(c, index) of quizStoredData.currentQuestion.choices" :key="`option-${index}`" >
                  <input type="radio" :id="`option-${index}`" :checked="index == parseInt(quizStoredData.selectedAnswer)" name="radioButtons" class="quizRadio" @change="selectButtonAnswer([index])" :key="`option-${index}`">
                  <label class="radio-label" :for="`option-${index}`" >
                    {{c.option}}
                  </label>
                </div>
              </div>
          </div>

          <div v-if="quizStoredData.currentQuestion.questionType == 'checkbox'" class="quizCheckboxHolder">
            <div class="quizCheckboxBtnsWrapper" v-for="(c, index) of quizStoredData.currentQuestion.choices" :key="`option-${index}`">
              <label class="checkboxContainer" :for="`chkbx-${index}`">
                <input type="checkbox" :disabled="quizStoredData.answered" :id="`chkbx-${index}`" :name="`chkbx-${index}`" class="quizCheckbox" :value="index" v-model="quizStoredData.currentCheckboxAnswer" >
                {{c.option}}
                <span class="checkmark"></span>
              </label>
              <transition name="fade">
                <div v-if="c.selectedText && quizStoredData.currentQuestion.showSelectionText && quizStoredData.answered && checkBoxSelected(index)" class="quizP currentSelectedText" v-html="c.selectedText"></div>
              </transition>
            </div>
            <div v-if="!quizStoredData.answered" class="text-center">
              <button class="quizBtn" type="button" @click="submitAnswer(quizStoredData.currentCheckboxAnswer)">{{ t("answer_question") }}</button>
            </div>
          </div>

      </form>
      </transition>

      <div v-if="quizStoredData.answered" class="text-center">
        <button v-if="quizStoredData.questionIndex < quizStoredData.questionsLenght - 1" class="quizBtn quizBtnNext" href="#" v-on:click.prevent="changeQuestion()" >{{ t("next_question") }} ></button>
        <button v-else class="quizBtn quizBtnNext hover:text-white" href="#" v-on:click.prevent="showResults()" >{{ t("show_results") }}</button>
      </div>
      <div class="text-center">
        <a class="previousQuestionLink" href="#" v-if="quizStoredData.questionIndex > 0"  v-on:click.prevent="changeQuestion('prev')">&lt; {{ t("previous_question") }}</a>
      </div>

    </div>

    <quizReport v-if="quizStoredData.quizSection == 3" />

    <feedbackForm />

</template>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.mode-fade-enter-active, .mode-fade-leave-active {
  transition: opacity .5s ease
}

.mode-fade-enter-from, .mode-fade-leave-to {
  opacity: 0
}

.radio {
  margin: 0.5rem;
}
.radio input[type="radio"] {
    position: absolute;
    opacity: 0;
}
.radio-label{
  @apply font-bold text-tsgray-dark cursor-pointer
}
.radio input[type="radio"] + .radio-label:before {
  @apply rounded-full border-2 border-tsgray-dark inline-block relative cursor-pointer text-center w-6 h-6 top-0 mr-4;
    content: '';
    vertical-align: top;
    transition: all 250ms ease;
}
.radio input[type="radio"]:checked + .radio-label:before {
  @apply bg-tsgray-dark;
  box-shadow: inset 0 0 0 4px #FFF;
}
.radio input[type="radio"]:disabled + .radio-label:hover
{
  cursor: default;
}

/* Customize the label (the container) */
.checkboxContainer {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-weight: bold;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.checkboxContainer input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 24px;
  width: 24px;
  background-color: #eee;
  border-radius: 5px;
}

/* On mouse-over, add a grey background color */
.checkboxContainer:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.checkboxContainer input:checked ~ .checkmark {
  background-color: #000;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.checkboxContainer input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.checkboxContainer .checkmark:after {
  left: 8px;
  top: 2px;
  width: 9px;
  height: 17px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}


.quizParagraph p{
  margin-bottom: 1rem;
}

.quizP{
  @apply text-base md:text-lg;
  line-height: 140%;
  margin-bottom: 1em;
}
.quizH1{
  @apply font-black text-4xl md:text-5xl my-4;
}
.quizH2{
  @apply font-bold text-xl md:text-2xl;
  margin: 20px 0;
  line-height: 120%;
}
.quizH3{
  font-weight: bold;
  font-size: 22px;
  margin: 20px 0;
  line-height: 120%;
}
#quizApp a,
a.quizLink {
  color: #7c3987;
}
#quizApp a:hover,
a.quizLink:hover {
  @apply underline;
}
#quizApp ul{
  list-style-type: disc;
}
#quizApp ol{
  list-style-type: decimal;
}
#quizApp ul, #quizApp ol{
  margin-bottom: 1rem;
  margin-left: 1rem;
}

.previousQuestionLink{
  @apply no-underline text-lg md:text-xl text-center font-bold px-4 md:px-6 py-2 md:py-3 text-tsprimary rounded-full inline-block hover:underline my-4;
  min-width: 150px;
  cursor: pointer;
}

.zebra-row{
  @apply p-4;
}
div.zebra-row:nth-child(even) {
  @apply bg-tsgray-light;
}

.currentSelectedText{
  @apply p-6 bg-tsgray-light text-left rounded-2xl block;
}

.quizButtonsHolder{
  @apply mt-8 mb-16;
}
.quizRadioBtnsWrapper{
  margin-bottom: 18px;
}
.quizRadioBtnsWrapper input{
  margin-right: 5px;
}
label.quizRadioBtnLabel{
  line-height: 25px;
  font-size: 18px;
  cursor: pointer;
  margin-top: -5px;
}
input[type=button].quizBtn,
button.quizBtn,
a.quizBtn{
  @apply no-underline text-lg md:text-xl text-center font-bold px-4 md:px-6 py-2 md:py-3 text-tsprimary border-3 border-tsprimary rounded-full my-4 hover:text-white hover:bg-tsprimary;
  min-width: 150px;
  cursor: pointer;
}
a.quizBtn:hover{
  @apply text-white;
}
button.quizBtnSelected{
  @apply text-white bg-tsprimary;
}
button.quizBtn:disabled{
  pointer: default;
}
p.quizParagraph{
  @apply text-lg leading-tight;
}
.quizReportTable > tr:nth-child(even) {
  @apply bg-tsgray-light;
}
.quizReportTable > tr td{
  @apply p-2;
}

.quizWrongAnswer{
  background: rgb(250, 239, 241);
  margin: 20px 0;
  padding: 0 15px;
  border: solid 1px rgb(197, 102, 102);
}
.quizRightAnswer{
  background: rgb(238, 252, 238);
  margin: 20px 0;
  padding: 0 15px;
  border: solid 1px rgb(103, 151, 103);
}
.quizWrongAnswer .quizH3{
  color: rgb(221, 86, 86);
}
.quizRightAnswer .quizH3{
  color: rgb(68, 143, 68);
}
input[type=button].quizBtn:hover{
  background: #5e2766;
}
</style>
