<script setup>
    import { useQuizStore } from '@/store/quizStore'
    import printPdf from '@/components/printPdf.vue'
    import { useI18n } from 'vue-i18n'
    const {t} = useI18n({})
    const quizStoredData = useQuizStore()
    function restart(){
        location.reload();
    }
</script>
<template>
    <div id="reportDataMobile" class="md:-mx-8 md:hidden">
        <div class="md:px-8 border-b-8 border-tsgray-light pb-4">
            <div v-if="quizStoredData.reportData.showScoring" class="quizH2">{{ t("scores") }}: {{quizStoredData.reportData.score}}</div>
            <div class="quizParagraph" v-html="quizStoredData.reportData.scoreText"></div>
            <div class="my-4 flex" v-for="(link, linkid) in quizStoredData.reportData.scoreTextLinks" :key="linkid">
                <svg class="mr-3 mt-1" xmlns="http://www.w3.org/2000/svg" width="10.743" height="17.244" viewBox="0 0 10.743 17.244">
                    <path id="Path_1" data-name="Path 1" d="M867.422,312.066l6.5,6.5-6.5,6.5" transform="translate(-865.301 -309.945)" fill="none" stroke="#8c2b87" stroke-linecap="round" stroke-width="3"></path>
                </svg>
                <a :href="link.scoreTextLink" class="text-xl font-bold inline-block">{{link.scoreTextLinkText}}</a>
            </div>
            <div class="flex justify-between">
                <div class="quizH2" v-if="quizStoredData.reportData.reportPageTitle"><h4>{{quizStoredData.reportData.reportPageTitle}}</h4> </div>
                <printPdf />
            </div>
        </div>

        <div class="zebra-row" v-for="(item, index) in quizStoredData.reportData.answers" :key="index">
            <div class="">
                <div><b>{{item.num}}: {{item.question}}</b></div><br>

                <div><b>{{ t("your_answers") }}</b></div>
                <div v-if="item.questionType == 'checkbox'">
                    <div v-for="(x, i) in item.answersIndex" :key="i" style="margin-bottom: 20px;">
                        {{item.answersTexts[x]}} <br>
                        <div v-if="item.answerSelectedTexts[x]">
                            <span style="font-weight: bold;">{{ t("answer_feedback") }}:<br></span>
                            <span v-html="item.answerSelectedTexts[x]"></span>
                        </div>
                        <span style="font-weight: bold;">{{ t("recommendations") }}:<br></span>
                        <div v-html="item.answersActions[x]"></div><br>
                    </div>
                </div>
                <div v-else>
                    <span v-for="x in item.answersIndex" :key="x">
                        {{item.answersTexts[x]}} <br>
                    </span>
                </div>

                <div v-if="item.questionType != 'checkbox'"><br>
                    <div style="font-weight: bold; margin-right: 20px;">{{ t("answer_feedback") }}<br></div>
                    <div>
                        <span class="md:w-3/4" v-for="x in item.answersIndex" :key="x">
                            <div v-html="item.answerSelectedTexts[x]"></div>
                        </span>
                    </div>
                </div>

                <div v-if="item.questionType != 'checkbox'"><br>
                    <div style="font-weight: bold; margin-right: 20px;">{{ t("recommendations") }}<br></div>
                    <div>
                        <span class="md:w-3/4" v-for="x in item.answersIndex" :key="x">
                            <div v-html="item.answersActions[x]"></div>
                        </span>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="hidden md:block ">
    <div id="reportData" class="md:-mx-8">
        <div class="md:px-8 border-b-8 border-tsgray-light pb-4">

            <div v-if="quizStoredData.reportData.showScoring" class="quizH2">Pisteesi: {{quizStoredData.reportData.score}}</div>
            <div class="quizParagraph" v-html="quizStoredData.reportData.scoreText"></div>
            <div class="my-4 flex" v-for="(link, linkid) in quizStoredData.reportData.scoreTextLinks" :key="linkid">
                <svg class="mr-3 mt-1" xmlns="http://www.w3.org/2000/svg" width="10.743" height="17.244" viewBox="0 0 10.743 17.244">
                    <path id="Path_1" data-name="Path 1" d="M867.422,312.066l6.5,6.5-6.5,6.5" transform="translate(-865.301 -309.945)" fill="none" stroke="#8c2b87" stroke-linecap="round" stroke-width="3"></path>
                </svg>
                <a :href="link.scoreTextLink" class="text-xl font-bold inline-block">{{link.scoreTextLinkText}}</a>
            </div>
            <div class="flex justify-between">
                <div class="quizH2" v-if="quizStoredData.reportData.reportPageTitle"><h4>{{quizStoredData.reportData.reportPageTitle}}</h4> </div>
                <printPdf />
            </div>
        </div>


        <table id="reportTable" border="0" class="quizReportTable w-full " style="width: 100%; border: none;">
        <tr class="hidden">
            <td>
                <div><h2>{{quizStoredData.reportData.quizName}}</h2></div>
            </td>
        </tr>
        <tr v-for="(item, index) in quizStoredData.reportData.answers" :key="index">
            <td class="px-8 py-8" style="width: 100%; padding: 20px; " >
                <table style="border:none; width: 100%;">
                    <tr style="border:none; vertical-align: top; width: 100%;">
                        <td style="font-weight: bold; margin-right: 20px; width: 25%">{{item.num}}. {{ t("question") }}: </td>
                        <td><b>{{item.question}}</b><br></td>
                    </tr>
                    <tr style="border:none; vertical-align: top;">
                        <td style="font-weight: bold; margin-right: 20px;"><b>{{ t("your_answers") }}</b><br></td>
                        <td style="width: 100%; " v-if="item.questionType == 'checkbox'">
                            <div v-for="(x, i) in item.answersIndex" :key="i" style="margin-bottom: 20px;">
                                {{item.answersTexts[x]}} <br>
                                <div v-if="item.answerSelectedTexts[x]">
                                    <span style="font-weight: bold;">{{ t("answer_feedback") }}:<br></span>
                                    <span v-html="item.answerSelectedTexts[x]"></span>
                                </div>
                                <span style="font-weight: bold;">{{ t("recommendations") }}:<br></span>
                                <div v-html="item.answersActions[x]"></div><br>
                            </div>
                        </td>
                        <td v-else>
                            <span v-for="x in item.answersIndex" :key="x">
                                {{item.answersTexts[x]}} <br>
                            </span>
                        </td>
                    </tr>
                    <tr style="border:none; vertical-align: top;" v-if="item.questionType != 'checkbox'">
                        <td style="font-weight: bold; margin-right: 20px;">{{ t("answer_feedback") }}<br></td>
                        <td>
                            <span class="md:w-3/4" v-for="x in item.answersIndex" :key="x">
                                <div v-html="item.answerSelectedTexts[x]"></div>
                            </span>
                        </td>
                    </tr>

                    <tr style="border:none; vertical-align: top;" v-if="item.questionType != 'checkbox'">
                        <td style="font-weight: bold; margin-right: 20px;">{{ t("recommendations") }}<br></td>
                        <td>
                            <span class="md:w-3/4" v-for="x in item.answersIndex" :key="x">
                                <div v-html="item.answersActions[x]"></div>
                            </span>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
        </table>
    </div>
    </div>
        <div style="text-align:center;">
            <button class="quizBtn" type="button" @click="restart">{{ t("restart") }}</button>
        </div>
        <!--
        <pre>
        {{quizStoredData.reportData}}
        </pre>-->
</template>