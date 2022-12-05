import { defineStore } from 'pinia'

export const useQuizStore = defineStore('quizData', {
state: () => {
    return {
        quizSection: 1,
        loading: true,
        questionsLenght: 1,
        score: 0,
        scoreIndex: [],
        scoreText: '',
        scoreTextLinks: [],
        questionIndex: 0,
        currentQuestion: {},
        selectedAnswer: [],
        currentSelectedText: '',
        currentAnsweredIndex: '',
        currentAnswer: null,
        currentCheckboxAnswer: [],
        answer: '',
        trueOrFalse: null,
        nextQuestionBtnText: 'Seuraava kysymys >',
        showResultsBtn: false,
        answered: false,
        showGuide: false,
        quizData: [],
        showFeedBackForm: false,
        formSubmitMessage: '',
        reportData: {
            quizName: '',
            reportPageTitle: '',
            showScoring: false,
            scoreText: '',
            scoreTextLinks: [],
            score: 0,
            answers: []
        }
    }
},
actions: {
    restart() {
        this.currentQuestion = {};
        this.quizSection = 1;
        this.currentQuestion = this.quizData.questions[0];

        // poistetaan vastaukset
        this.currentQuestion.choices.forEach(element => {
            element.currentSelectedText = ""
        });

        this.answer = "";
        this.questionIndex = 0;
        this.score = 0;
        this.trueOrFalse = '';
        this.currentAnswer = "";
        this.answered = false;
        this.currentSelectedText = '',
        this.currentAnsweredIndex = '',
        this.nextQuestionBtnText = "Seuraava kysymys >";
        this.showGuide = false;

        this.reportData = [];
    },
},
})