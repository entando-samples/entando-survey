import Vue from './js/config'
import { createPinia} from 'pinia';
import App from './js/App';
import router from './js/router';
import './assets/tailwind.css'
// import Keycloak from 'keycloak-js';

let initOptions = {
    url: 'http://localhost:9080/auth',
    realm: 'entando',
    clientId: 'web_app',
    onLoad: 'login-required'
}

// let keycloak = Keycloak(initOptions);




class EntSurvey extends HTMLElement {

    connectedCallback() {
        this.mountPoint = document.createElement('span')
        this.render()
    }



    render() {
        // debugger
        // keycloak.init({ onLoad: initOptions.onLoad })
        //     .success((auth) => {
        //     debugger
        //     console.log(auth);
        //     if (!auth) {
        //         window.location.reload();
        //     } else {
        //        console.log("Authenticated");
        //        console.log(keycloak.token);

        new Vue({
            el: this.appendChild(this.mountPoint),
            render: (h) => h(App),
            pinia: createPinia(),
            router,
        });
            // }


//Token Refresh
//             setInterval(() => {
//                 keycloak.updateToken(70).then((refreshed) => {
//                     if (refreshed) {
//                        console.log('Token refreshed' + refreshed);
//                     } else {
//                         console.log('Token not refreshed, valid for '
//                             + Math.round(keycloak.tokenParsed.exp + keycloak.timeSkew - new Date().getTime() / 1000) + ' seconds');
//                     }
//                 }).catch(() => {
//                     console.log('Failed to refresh token');
//                 });
//             }, 6000)

        };
        //     .catch(() => {
        //    console.log("Authenticated Failed");
        // });










    // }
}

customElements.get('ent-survey-widget') || customElements.define("ent-survey-widget", EntSurvey)
