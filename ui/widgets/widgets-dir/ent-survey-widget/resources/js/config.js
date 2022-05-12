import vueCompositionApi from "@vue/composition-api";
import { createPinia, PiniaVuePlugin } from "pinia";
import PortalVue from "portal-vue";
import Vue from "vue";
import VueRouter from "vue-router";
import { Tab, Tabs } from "vue-tabs-component";
import ToastedPlugin from "vue-toasted";
import App from "./App.vue";
import BaseBlock from "./components/Base/Block.vue";
import BaseButton from "./components/Base/Button.vue";
import BaseFileUploader from "./components/Base/FileUploader.vue";
import BaseModal from "./components/Base/Modal.vue";
import BasePageHeader from "./components/Base/PageHeader.vue";
import Paginator from "./components/Base/Paginator.vue";
import BaseSelect from "./components/Base/Select.vue";
import ErrorMessage from "./components/ErrorMessage.vue";
import router from "./router";
import moment from "moment";
import { isString } from "lodash";

console.log('loaded');
Vue.filter("datetime", function (value) {
    if (value) {
        return moment(String(value)).format("MM/DD/YYYY hh:mm");
    }
});

Vue.filter("date", function (value) {
    if (value) {
        return moment(String(value)).format("MM/DD/YYYY");
    }
});

Vue.filter("strlimit", function (value, limit = 25, prepend = '...') {
    if (isString(value) && value.length > limit) {
        return value.substring(0, limit) + prepend;
    }
    return value;
});

Vue.use(vueCompositionApi);
Vue.use(PiniaVuePlugin);
Vue.use(VueRouter);
Vue.use(PortalVue);
Vue.use(ToastedPlugin, {
    theme: "bubble",
    iconPack: "fontawesome",
    position: "bottom-right",
    duration: 3500,
});

Vue.component("b-paginator", Paginator);
Vue.component("b-button", BaseButton);
Vue.component("b-button", BaseButton);
Vue.component("b-modal", BaseModal);
Vue.component("b-page-header", BasePageHeader);
Vue.component("b-select", BaseSelect);
Vue.component("b-block", BaseBlock);
Vue.component("b-file-uploader", BaseFileUploader);
Vue.component("b-tabs", Tabs);
Vue.component("b-tab", Tab);
Vue.component("err-msg", ErrorMessage);

export default Vue;
