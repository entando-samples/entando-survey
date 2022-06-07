import axios from "axios";
import useAuth from "./compositions/auth";

const client = axios.create({
    baseURL: window.laravel.appUrl + "/api/v1/",
    withCredentials: true,
});

window.addEventListener('keycloak', function (e) {
    // console.log(e);
    const useAuthStore = useAuth();
    console.log(entando.keycloak.token);
    client.interceptors.request.use(request => {
        request.headers.common['Accept'] = 'application/json';
        request.headers.common['Content-Type'] = 'application/json';
        request.headers.common['Authorization'] = `Bearer ${entando.keycloak.token}`;

        return request;
    });
    useAuthStore.getUser().then(r => console.log(r));
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
