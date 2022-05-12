import axios from "axios";

const client = axios.create({
    baseURL: "/api/v1/",
    withCredentials: true,
});

client.interceptors.request.use(request => {
    request.headers.common['Accept'] = 'application/json';
    request.headers.common['Content-Type'] = 'application/json';
    return request;
});

client.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        if (error.response.status === 401) {
            // this.$router.push({path:'/login'});
            }
        return Promise.reject(error);
    }
);

export default client;
