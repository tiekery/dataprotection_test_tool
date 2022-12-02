import { createI18n } from 'vue-i18n';
import en from '@/locales/en.json';
import fi from '@/locales/fi.json';
import sv from '@/locales/sv.json';

let lang = "fi"
if(typeof quiz_data !== 'undefined'){
    lang = quiz_data.lang
}

const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: lang,
    fallbackLocale: 'fi',
    messages: {
        en,
        fi,
        sv
    },
});

export default i18n;