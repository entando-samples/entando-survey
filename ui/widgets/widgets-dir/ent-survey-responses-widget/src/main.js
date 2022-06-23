import Vue from './js/config'
import { createPinia} from 'pinia';
import App from './js/App';
import router from './js/router';
import './assets/tailwind.css'

window.laravel = {
    "appUrl": process.env.VUE_APP_SERVER_SERVLET_CONTEXT_PATH
};

class EntSurvey extends HTMLElement {

    connectedCallback() {
        this.mountPoint = document.createElement('span')
        this.render()
    }

    render() {
        new Vue({
            el: this.appendChild(this.mountPoint),
            render: (h) => h(App),
            pinia: createPinia(),
            router,
        });
    };
}

customElements.get('ent-survey-responses-widget') || customElements.define("ent-survey-responses-widget", EntSurvey)
