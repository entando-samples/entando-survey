import client from "@/http";
import Validator from "@/utils/validator";
import { reactive, ref } from "@vue/composition-api"
import Vue from "vue";

export default function useUsers() {
    const validator = Validator({});

    const loading = reactive({
        doctor: false,
        doctors: false,
        users: false,
        user: false,
        userForm: false,
    });

    const roles = ref([]);

    const users = reactive({
        data: [],
        meta: null
    });

    const user = reactive({
        name: null,
        email: null,
        role: null,
        password: null,
        password_confirmation: null,
        email_report: false,
    })

    const doctors = reactive({
        data: [],
        meta: null
    });

    function getRoles() {
        return client.get("/backend/roles")
            .then(res => {
                roles.value = res.data.data;
            })
    }

    function getUsers({ search = null, page = 1, roles = [] } = {}) {
        loading.users = true;

        return client.get(`/backend/users`, { params: { search, page, roles } })
            .then(res => {
                users.data = res.data.data;
                users.meta = res.data.meta;
            })
            .finally(() => loading.users = false);
    }

    function saveUser(data) {
        validator.reset();
        loading.userForm = true;

        return client.post(`/backend/users`, data)
            .then(res => res.data.data)
            .catch(err => {
                if (validator.isValidationError(err)) {
                    validator.adaptErr(err);
                }

                throw err;
            })
            .finally(() => loading.userForm = false)
    }

    function getUser(id) {
        loading.userForm = true;

        return client.get(`/backend/users/${id}`)
            .then(({ data }) => {
                data = data.data;
                user.name = data.name;
                user.email = data.email;
                user.role = data.role;
                user.email_report = data.email_report;
            })
            .finally(() => loading.userForm = false)
    }

    function updateUser(id, data) {
        validator.reset();
        loading.userForm = true;

        return client.put(`/backend/users/${id}`, data)
            .then(({ data }) => {
                return data.data;
            })
            .catch(err => {
                if (validator.isValidationError(err)) {
                    validator.adaptErr(err);
                }

                throw err;
            })
            .finally(() => loading.userForm = false)

    }

    function getDoctors({ search = '', page = 1 } = {}) {
        loading.doctors = true;

        return client.get(`/backend/doctors`, { params: { search, page } })
            .then(({ data }) => {
                doctors.data = data.data;
            })
            .finally(() => loading.doctors = false)
    }

    function updateProfile(data){
        return client.post('/update/profile',data)
        .then(res => {
            return res.data;
        })
        .catch((err) => {
            if (validator.isValidationError(err)) {
                validator.adaptErr(err);
            }

            throw err;
        });
    }

    return {
        errors: validator.errors,
        loading,
        doctors,
        roles,
        user,
        users,
        getRoles,
        getUsers,
        getDoctors,
        saveUser,
        getUser,
        updateUser,
        updateProfile
    }
}
