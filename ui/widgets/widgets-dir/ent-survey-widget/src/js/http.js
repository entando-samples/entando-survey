import axios from "axios";
import useAuth from "./compositions/auth";

const client = axios.create({
    baseURL: process.env.VUE_APP_SERVER_SERVLET_CONTEXT_PATH + "api/v1/",
    withCredentials: true,
});

window.addEventListener('keycloak', function (e) {
    // console.log(e);
    // console.log(e.detail.eventType);
    if (e.detail.eventType === 'onTokenExpired') {
        entando.keycloak.updateToken(300);
    }
    if (e.detail.eventType === 'onReady' || 
        e.detail.eventType === 'onAuthRefreshSuccess') {
        client.interceptors.request.use(request => {
            request.headers.common['Accept'] = 'application/json';
            request.headers.common['Content-Type'] = 'application/json';
            request.headers.common['Authorization'] = `Bearer ${entando.keycloak.token}`;

            return request;
        });
    }
    // TODO: Update this for keycloak.
    // const useAuthStore = useAuth();
    // useAuthStore.getUser().then(r => console.log(r));
});

client.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        // if (error.response.status === 401) {
            // this.$router.push({path:'/login'});
            // }
        console.log(error);
        return Promise.reject(error);
    }
);

export default client;
