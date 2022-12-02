<script setup>
import {ref} from 'vue';
import { useQuizStore } from '@/store/quizStore'
import { useI18n } from 'vue-i18n'
const {t} = useI18n({})
const showForm = ref(false);
const message = ref('')
const formMessage = ref('');
const honey1 = ref('');
const honey2 = ref('');
const quizStoredData = useQuizStore()
let ajaxurl = ""
let formnonce = ''
let quizFormInfotext = ""
if(typeof quiz_data !== 'undefined'){
    ajaxurl = quiz_data.ajaxurl
    formnonce = quiz_data.nonce
    quizFormInfotext = quiz_data.quizFormInfotext
}
function submitForm(){
    var data = new FormData();
    data.append( 'action', 'iasy_form_response' );
    data.append( 'nonce', formnonce );
    data.append( 'quizname', quizStoredData.quizData.quizName );
    data.append( 'question', quizStoredData.currentQuestion.question );
    data.append( 'message', message.value);
    const params = new URLSearchParams(data);
    if(message.value != ''){
        // Check honeypot fields to prevent Spam bots
        if(honey1.value != '' || honey2.value != ''){
            return;
        }

        fetch(ajaxurl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Cache-Control': 'no-cache',
        },
        body: params
        }).then(async response => {
        const data = await response.json();

        // check for error response
        if (!response.ok) {
            const error = (data && data.message) || response.statusText;
            return Promise.reject(error);
        } else {
            quizStoredData.formSubmitMessage = t("feedback_thank_you") //'Kiitos. Palaute lähetetty!'
            formMessage.value = t("feedback_thank_you") //'Kiitos. Palaute lähetetty!'
            quizStoredData.showFeedBackForm = false
            message.value = ''
        }

        })
        .catch(error => {
            this.errorMessage = error;
            console.error("There was an error!", error);
        });
    }

}
function openForm(){
    quizStoredData.showFeedBackForm = !quizStoredData.showFeedBackForm
    quizStoredData.formSubmitMessage = ''
    formMessage.value = ''
}

</script>
<template>

    <div style="border-top: solid 1px #CCC; padding-top: 10px;" v-if="quizStoredData.quizSection == 2">
        <a style="text-align: center; text-decoration: none; color: #666; font-size: 14px; display: flex;" href="#" @click.prevent="openForm()">
            <img id="infoicon2" src="/src/assets/info-icon2.svg" alt="" style="margin-right: 10px; width: 20px; height: 20px;"> {{ $t("give_feedback") }}
        </a>
        <p class="my-4 font-bold">
            {{quizStoredData.formSubmitMessage}}
        </p>
        <div v-if="quizStoredData.showFeedBackForm">
            <div class="my-4 text-sm" v-html="quizFormInfotext"> </div>
            <textarea style="border: solid 1px #CCC; padding: 10px;" name="" id="" cols="50" rows="10" v-model="message"></textarea>
            <!-- H o n e y p o t -->
            <label class="ohnohoney" for="name"></label>
            <input class="ohnohoney" autocomplete="off" type="text" id="name" name="name" placeholder="Your name here" v-model="honey1">
            <label class="ohnohoney" for="email"></label>
            <input class="ohnohoney" autocomplete="off" type="email" id="email" name="email" placeholder="Your e-mail here" v-model="honey2">
            <div>
                <button class="quizBtn" @click="submitForm(quizValues)">{{ t("submit_feedback") }}</button>
            </div>
        </div>
    </div>

</template>

<style>
    .ohnohoney{
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        height: 0;
        width: 0;
        z-index: -1;
    }
</style>