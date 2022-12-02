import { createApp, defineAsyncComponent } from 'vue'
import App from "./App.vue";
import { createPinia } from 'pinia'
import i18n from '@/i18n.js';
import './index.css'

if(document.getElementById("quizAdmin")){
    const AdminApp = defineAsyncComponent(() => import("./AdminApp.vue"))
    createApp(AdminApp).use(i18n).mount('#quizAdmin')
}

export const QuizApp = createApp(App)
QuizApp.use(createPinia())
QuizApp.use(i18n)
if (document.getElementById('quizApp')) {
    QuizApp.mount('#quizApp')
}
