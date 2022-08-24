import Vue from './js/config'
import { createPinia} from 'pinia';
import App from './js/App';
import router from './js/router';
import './assets/tailwind.css'

window.laravel = {
    "appUrl": process.env.VUE_APP_SERVER_SERVLET_CONTEXT_PATH
};

const KEYCLOAK_EVENT_TYPE = 'keycloak'
const subscribeToWidgetEvent = (eventType, eventHandler) => {
    window.addEventListener(eventType, eventHandler)

    return () => {
        window.removeEventListener(eventType, eventHandler)
    }
}


class EntSurvey extends HTMLElement {

    connectedCallback() {
        this.mountPoint = document.createElement('span')
        subscribeToWidgetEvent(KEYCLOAK_EVENT_TYPE, (e) => {
            if(e.detail.eventType==="onReady"){
                this.render()
            }
        })
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

customElements.get('ent-survey-widget') || customElements.define("ent-survey-widget", EntSurvey)
