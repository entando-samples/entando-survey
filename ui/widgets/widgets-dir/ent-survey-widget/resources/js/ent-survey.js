import Vue from './config.js'
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router.js';




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
    }
}

customElements.get('ent-survey-widget') || customElements.define("ent-survey-widget", EntSurvey)
