import { createApp } from 'vue'
import App from '../App.vue'
import router from '../router'




class EtApp extends HTMLElement {
    connectedCallback() {
        this.mountPoint = document.createElement('span')
        this.render()
    }

    render() {
        const app = createApp(App)
        app.use(router)
        app.mount(this.appendChild(this.mountPoint))
    }
}

customElements.get('et-app') || customElements.define("et-app", EtApp)
