<script setup>
    import {reactive, ref, computed} from 'vue'
    import { quillEditor, Quill } from 'vue3-quill'
    const showDevTools = ref(false);

    // Quilljs options
    const state = reactive({
        content: '',
        _content: '',
        editorOption: {
            placeholder: 'core',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ header: [ 2, 3, 4, false] }],
                    [{ align: [] }],
                    ['clean'],
                    ['link', 'image']
                ]
            // other moudle options here
            // otherMoudle: {}
            },
            // more options
        },
        disabled: false
    })

    const isVisible = ref(false);

    // toggle current question
    const toggler_info = ref([]);

    function showDetails(index) {
        if(toggler_info.value.includes(index)){
            console.log('löytyy ' + index)
            toggler_info.value = toggler_info.value.filter(function(item) {
                return item !== index
            })
        } else {
            toggler_info.value.push(index);
        }
    }

    function isOpen(index){
        if(toggler_info.value.includes(index)){
            console.log('löytyy ' + index)
            return true;
        }
    }

    // Default values to Quiz admin fields
    const quizDataUpdate = reactive({
        quizName: quiz_data.quiztitle,
        questionsCount: 1,
        introText: "",
        showScoring: true,
        reportPageTitle: "Raportti",
        introButtonText: "Aloita testi",
        questions: [
            {
                questionType: "trueFalse",
                questionFullScore: 4,
                question: "Uiko kala vedessä?",
                showSelectionText: true,
                choices: [
                    {
                        option: 'Kyllä',
                        choiceText: "",
                        answerRatio: 100,
                        rightAnswer: true,
                        selectedText: "",
                        selectedActions: "",
                    },
                    {
                        option: 'Ei',
                        choiceText: "",
                        answerRatio: 0,
                        rightAnswer: false,
                        selectedText: "",
                        selectedActions: "",
                    },
                ],
                guideText: "",
            },
        ],
        scoring: [
            {
                scores: [3,4],
                scoreText: "Kuulut parhaimpaan a-ryhmään. Onneksi olkoon",
                scoreTextLinks: []
            },
            {
                scores: [0,2],
                scoreText: "Ei mennyt ihan nappiin! Suosittelemme lähtötestin suorittamista!",
                scoreTextLinks: []
            }
        ]
    });
    if(typeof quiz_data !== 'undefined' && quiz_data.quizdata !== ''){
        Object.assign(quizDataUpdate, JSON.parse(quiz_data.quizdata))
    }
    const jsonSTR = computed(() => JSON.stringify(quizDataUpdate))

    const questionTypes = {
        "True / False - one right answer": "trueFalse",
        "Multiple answers - one right answer (Radio buttons)": "radio",
        "Multiple answers (Checkbox)": "checkbox"
    }

    function addScoringRow(){
        quizDataUpdate.scoring.push({scores: [0,0], scoreText: "", scoringTextLinks: []});
    }
    function removeScoringRow(key){
        quizDataUpdate.scoring.splice(key,1);
    }

    function addQuestionRow(){
        // close all opened windows
        toggler_info.value = [];
        quizDataUpdate.questions.push({questionType: "", question: "", choices: []})
        quizDataUpdate.questionsCount++;
    }
    function removeQuestionRow(key){
        // close all opened windows
        toggler_info.value = [];
        quizDataUpdate.questions.splice(key,1);
        quizDataUpdate.questionsCount--;
    }
    function duplicateQuestionRow(key){
        // close all opened windows
        toggler_info.value = [];
        quizDataUpdate.questions.push(quizDataUpdate.questions[key])
        quizDataUpdate.questionsCount++;
    }

    function removeQuestionOptionRow(key, index){
        quizDataUpdate.questions[key].choices.splice(index,1);
    }
    function addQuestionOptionRow(key){
        quizDataUpdate.questions[key].choices.push({option: ""})
    }


    function addScoringTextLink(key){
        if(!quizDataUpdate.scoring[key].scoreTextLinks){
            quizDataUpdate.scoring[key]['scoreTextLinks'] = []
        }
        quizDataUpdate.scoring[key].scoreTextLinks.push({
            scoreTextLink: '',
            scoreTextLinkText: ''
        });
    }
    function removeScoringTextLink(key, linkrow, rowkey){
        //console.log(key, index)
        console.log(quizDataUpdate.scoring[key])
        quizDataUpdate.scoring[key]['scoreTextLinks'].splice(rowkey,1);
    }



    Array.prototype.move = function(from, to) {
        this.splice(to, 0, this.splice(from, 1)[0]);
        return this;
    };

    function move(from, to) {
        // close all opened windows
        toggler_info.value = [];
        // change order
        quizDataUpdate.questions.move(from, to);
    }

    function checkQuizName(){
        if(!quizDataUpdate.quizName){
            quizDataUpdate.quizName = quiz_data.quiztitle;
        }
    }
    checkQuizName()

</script>
<template>

    <div style="margin-bottom: 20px;">
        <div style="display:flex">
                <label style="width: 20%" for="">Name of the survey: </label>
                <input type="text" v-model="quizDataUpdate.quizName" style="width: 80%">
        </div>
        <p style="display:flex">
            <label style="width: 20%" for="">Intro text:</label>
            <input type="text" v-model="quizDataUpdate.introText" style="width: 80%">
        </p>
        <p style="display:flex">
            <label style="width: 20%" for="">Start button text: </label>
            <input type="text" v-model="quizDataUpdate.introButtonText">
        </p>
        <p style="display:flex">
            <label style="width: 20%" for="">Report page title: </label>
            <input type="text" v-model="quizDataUpdate.reportPageTitle">
        </p>
        <p style="display:flex">
            <label style="width: 20%" for="showScoring">Show scores on report page: </label>
            <input type="checkbox" id="showScoring" v-model="quizDataUpdate.showScoring">
        </p>
        <h3>Scoring</h3>
        <div style="margin-bottom: 20px; position:relative;" v-for="(row, key) in quizDataUpdate.scoring" :key="key">
            <div style="position: absolute; top: 10px; right: 10px;">
                <button @click.prevent="removeScoringRow(key)">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
            <div style="display: flex">
                <div style="width: 20%;">Results: </div>
                <p>
                    <input type="number" size="4" v-model="row.scores[0]">  <span> – </span> <input type="number" v-model="row.scores[1]">
                </p>
            </div>
            <div style="display: flex; border-bottom: solid 1px #CCC; padding-bottom: 20px;">
                <div style="width: 20%;"> Feedback: </div>
                <div style="width: 80%;">
                    <quill-editor  v-model:value="quizDataUpdate.scoring[key].scoreText" :options="state.editorOption" :disabled="state.disabled"></quill-editor>
                    <br>
                    <div v-for="(linkrow, rowkey) in row.scoreTextLinks" :key="rowkey">
                        <label for="">Buttons link: </label> <input type="text" v-model="linkrow.scoreTextLink" placeholder="https://urladdress.fi">
                        <label for="">Buttons text: </label> <input type="text" v-model="linkrow.scoreTextLinkText">
                        <a href="#" class="button" @click.prevent="removeScoringTextLink(key, linkrow, rowkey)"><span class="dashicons dashicons-trash"></span></a>
                        <br><br>
                    </div>
                    <a class="button" href="#" @click.prevent="addScoringTextLink(key)">Add link button +</a>
                </div>
            </div>
        </div>
        <input type="submit" class="button button-primary button-large" href="#" @click.prevent="addScoringRow" value="Add scoring row +">
    </div>

    <h3>Questions</h3>
    <div class="questionRow" :class="{'active' : isOpen(index)}" v-for="(row, index) in quizDataUpdate.questions" :key="index">
        <div style="position: absolute; top:10px; right:10px;">
            <button class="questionIconBtn" @click.prevent="move(index,index-1)" v-if="index !== 0" title="Move up">
                <span class="dashicons dashicons-arrow-up-alt"></span>
            </button>
            <button class="questionIconBtn" @click.prevent="move(index,index+1)" v-if="index !== quizDataUpdate.questions.length-1" title="Move down">
                <span class="dashicons dashicons-arrow-down-alt"></span>
            </button>
            <button class="questionIconBtn" @click.prevent="duplicateQuestionRow(index)" title="Duplicate question">
                <span class="dashicons dashicons-admin-page"></span>
            </button>
            <button @click.prevent="removeQuestionRow(index)" title="Delete question">
                <span class="dashicons dashicons-trash"></span>
            </button>
        </div>
        <div style="padding-right: 150px;">
            <h3>{{index + 1}}: {{row.question}}</h3>
        </div>

        <transition name="fade">
        <div v-if="isOpen(index)" style="margin-bottom: 20px; padding: 0 10px;">
            <p>
                <label class="questionLabel">Question: </label>
                <input type="text" v-model="row.question" style="width: 100%">
            </p>
            <div>
                <label class="questionLabel">Question type: </label>
                <select v-model="row.questionType">
                    <option v-for="(value, name, index) in questionTypes" :key="index" :value="value">
                        {{name}}
                    </option>
                </select>
            </div>
            <div>
                <label class="questionLabel">Questions full score: </label>
                <input type="number" v-model="row.questionFullScore">
            </div>
            <div>
                <div>
                    <label class="questionLabel">"Question info" box text </label>
                </div>
                <quill-editor  v-model:value="row.guideText" :options="state.editorOption" :disabled="state.disabled"></quill-editor>
            </div>

            <p> <label>Feedback is shown after answer </label> <input type="checkbox" v-model="row.showSelectionText"></p>

            <h4>Answers: </h4>

            <div>
                <div style="border: solid 2px #D0D0D0; margin-bottom: 10px; padding: 10px; position: relative; " v-for="(item, index2) in row.choices" :key="index2">
                    <div style="position: absolute; top: 10px; right: 10px;">
                        <button title="Poista rivi" @click.prevent="removeQuestionOptionRow(index, index2)"><span class="dashicons dashicons-trash"></span></button>
                    </div>
                    <div>
                        <label class="questionLabel">Answer:</label>
                        <input type="text" v-model="item.option">
                        <input style="margin-left: 20px;" :id="`qchkbx-${index2}`" type="checkbox" v-model="item.rightAnswer">
                        <label :for="`qchkbx-${index2}`"> Right answer </label>
                    </div>
                    <div>
                        <label class="questionLabel">Ratio of scores %</label>
                        <input type="number" v-model="item.answerRatio">
                    </div>
                    <div class="two-columns">
                        <div class="editor-column">
                            <label class="questionLabel">Answers feedback:</label>
                            <quill-editor  v-model:value="item.selectedText" :options="state.editorOption" :disabled="state.disabled"></quill-editor>
                        </div>
                        <div class="editor-column">
                            <label class="questionLabel">Answers actions:</label>
                            <quill-editor  v-model:value="item.selectedActions" :options="state.editorOption" :disabled="state.disabled"></quill-editor>
                        </div>
                    </div>

                </div>
                <div>
                    <a class="button button-primary button-large" href="#" @click.prevent="addQuestionOptionRow(index)">Add row</a>
                </div>
            </div>


        </div>
        </transition>

        <button class="dashicon-button" @click.prevent="showDetails(index)">
            <template v-if="!isOpen(index)">
                <span class="dashicons dashicons-edit"></span> Edit
            </template>
            <template v-else >
                <span class="dashicons dashicons-no"></span> Close editing
            </template>
        </button>

    </div>
    <input type="submit" class="button button-primary button-large" href="#" @click.prevent="addQuestionRow" value="Add question">


    <div style="border-top: solid 1px #CCC; margin-top: 50px;">
        <a href="#" @click.prevent="showDevTools = !showDevTools" style="padding: 1rem 0; display: inline-block; color:#999;">Development tools</a>
        <div v-show="showDevTools" style="margin-top: 30px;">
            <textarea rows="10" cols="50" name="quiz_json" id="quiz_json" :value="jsonSTR"></textarea>
        </div>
    </div>

    <div class="fade-enter-active fade-leave-active fade-enter-from fade-leave-to"></div>

</template>
<style>
    button:hover{
        cursor: pointer;
    }
    .two-columns{
        @apply flex;
    }
    .editor-column{
        @apply m-2 w-1/2;
    }
    .questionIconBtn{
        @apply mr-2;
    }
    .questionLabel{
        @apply font-bold leading-8 my-4 mr-4;
    }
    .questionRow{
        position: relative;
        border: solid 1px #CCC;
        padding: 0 10px 20px 10px;
        /*border-radius: 5px;*/
        margin-bottom: 10px;
    }
    .questionRow.active{
        border-color: #2271b1;
    }
    .dashicon-button{
        line-height: 20px;
        cursor: pointer;
    }
    .hiddenItem{
        display: none;
    }
    .ql-container{
        height: auto;
        display: block;
        min-height: 100px;
    }
    .ql-editor p{
        margin: 1rem 0;
    }
    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s ease;
    }
    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }
</style>