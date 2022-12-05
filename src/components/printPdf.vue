<script setup>
import {ref} from 'vue';
import { useQuizStore } from '@/store/quizStore'
import { useI18n } from 'vue-i18n'
const {t} = useI18n({})
const message = ref('')
const formMessage = ref('');
const quizStoredData = useQuizStore()

let ajaxurl = "/wp-admin/admin-ajax.php";
if(typeof iasy_nonce !== 'undefined'){
    ajaxurl = iasy_nonce.url
}
const formnonce = ''

function printPDF(){
    let data = new FormData();
    data.append( 'action', 'iasy_print_pdf' );
    data.append( 'nonce', formnonce );
    data.append( 'quizname', quizStoredData.quizData.quizName );
    data.append( 'message', JSON.stringify(quizStoredData.reportData));
    data.append( 'markup', document.getElementById("reportData").outerHTML);
    const params = new URLSearchParams(data);
    for (var key of data.entries()) {
        console.log(key[0] + ', ' + key[1]);
    }
    //if(message.value != ''){
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
        console.log(data)
        window.location.href = "?pdf=" + data.pdf;
        // check for error response
        if (!response.ok) {
            const error = (data && data.message) || response.statusText;
            return Promise.reject(error);
        } else {
            console.log(response.data)
            //formMessage.value = 'Kiitos. Palaute lähetetty!'
        }

        })
        .catch(error => {
            //this.errorMessage = error;
            console.error("There was an error!", error);
        });
    //}

}

</script>
<template>
    <a href="#" class="mx-4 flex items-center hideinpdf" @click.prevent="printPDF">
        <div class="mr-4">{{ t("download_report") }} </div>
        <svg xmlns="http://www.w3.org/2000/svg" width="33.008" height="44.105" viewBox="0 0 33.008 44.105">
        <g id="pdf-file-2614" transform="translate(94.325 126.036)">
            <g id="Group_31" data-name="Group 31" transform="translate(-94.325 -126.036)">
            <path id="Path_5" data-name="Path 5" d="M73.4,63.215a1.261,1.261,0,0,1-.965-.454l-.075-.116a2.91,2.91,0,0,1,.777-3.383,13.322,13.322,0,0,1,5.011-2.928,40.791,40.791,0,0,0,2.016-4.325c-1.34-2.2-1.9-4.52-1.348-6.038a2.08,2.08,0,0,1,1.707-1.428l.069,0a1.686,1.686,0,0,1,1.718,1.434c.315,1.3-.167,3.506-1.061,5.915a13.376,13.376,0,0,0,1.235,1.6,16.534,16.534,0,0,0,1.335,1.432,12.563,12.563,0,0,1,5.575.19A2.067,2.067,0,0,1,90.98,57v.01a1.592,1.592,0,0,1-1.058,1.607c-1.365.5-3.937-.4-6.447-2.671a27.276,27.276,0,0,0-4.638,1.167c-1.562,2.835-3.325,5.217-4.594,5.872A1.846,1.846,0,0,1,73.4,63.215Zm-.18-1.05c.078.069.224.128.573-.051.958-.494,2.3-2.243,3.6-4.439a10.754,10.754,0,0,0-3.539,2.255A2.016,2.016,0,0,0,73.22,62.165Zm11.574-6.392C86.85,57.414,88.762,58,89.586,57.7A.609.609,0,0,0,90,57.022c0-.165-.028-.645-.951-.993A10.231,10.231,0,0,0,84.794,55.773Zm-3.975-2.784c-.4.965-.848,1.944-1.331,2.895a28.645,28.645,0,0,1,3.135-.768q-.445-.464-.88-.986A14.263,14.263,0,0,1,80.818,52.989Zm-.191-7.469a1.1,1.1,0,0,0-.89.787c-.352.968-.071,2.672.873,4.492a9.622,9.622,0,0,0,.747-4.594C81.217,45.625,80.927,45.526,80.628,45.519Z" transform="translate(-65.039 -36.745)" fill="#8c2b87"/>
            <path id="Path_6" data-name="Path 6" d="M64.132,8.491,56.228.586A1.988,1.988,0,0,0,54.813,0h-21.1a2,2,0,0,0-2,2V42.1a2,2,0,0,0,2,2H62.718a2,2,0,0,0,2-2V9.906A1.985,1.985,0,0,0,64.132,8.491Zm-1.087.3h-6.23a.888.888,0,0,1-.887-.887V1.673ZM33.712.98h21.1a1.028,1.028,0,0,1,.135.013V7.9A1.869,1.869,0,0,0,56.815,9.77h6.91a.973.973,0,0,1,.014.136v19.8H32.691V2A1.022,1.022,0,0,1,33.712.98ZM62.718,43.125H33.712A1.022,1.022,0,0,1,32.691,42.1V30.688H63.738V42.1A1.022,1.022,0,0,1,62.718,43.125Z" transform="translate(-31.711)" fill="#8c2b87"/>
            <path id="Path_7" data-name="Path 7" d="M80.266,189.253H78.123a.49.49,0,0,0-.49.49v6.68a.49.49,0,1,0,.98,0V194.2h1.653a1.883,1.883,0,0,0,1.881-1.881v-1.184A1.883,1.883,0,0,0,80.266,189.253Zm.9,3.064a.9.9,0,0,1-.9.9H78.613v-2.985h1.653a.9.9,0,0,1,.9.9Z" transform="translate(-69.598 -156.139)" fill="#8c2b87"/>
            <path id="Path_8" data-name="Path 8" d="M115.278,196.913h-2a.49.49,0,0,1-.49-.49v-6.68a.49.49,0,0,1,.49-.49h2a2.026,2.026,0,0,1,2.024,2.024v3.612A2.027,2.027,0,0,1,115.278,196.913Zm-1.51-.98h1.51a1.045,1.045,0,0,0,1.044-1.044v-3.612a1.045,1.045,0,0,0-1.044-1.044h-1.51Z" transform="translate(-98.602 -156.139)" fill="#8c2b87"/>
            <path id="Path_9" data-name="Path 9" d="M152.663,189.253h-3.534a.49.49,0,0,0-.49.49v6.68a.49.49,0,1,0,.98,0v-2.85h1.824a.49.49,0,1,0,0-.98h-1.824v-2.36h3.044a.49.49,0,0,0,0-.981Z" transform="translate(-128.18 -156.139)" fill="#8c2b87"/>
            </g>
        </g>
        </svg>
    </a>
</template>