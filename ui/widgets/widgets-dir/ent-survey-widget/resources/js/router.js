import { useAuthStore } from "@/stores/auth";
import LoginPage from "@/views/auth/Login.vue";
import LogoutPage from "@/views/auth/Logout.vue";
import ResetPage from "@/views/auth/Reset.vue";
import QuestionFormPage from "@/views/questions/Form.vue";
import QuestionIndexPage from "@/views/questions/Index.vue";
import SurveyIndexPage from "@/views/surveys/Index.vue";
import SurveyFormPage from "@/views/surveys/Form.vue";
import NotFoundPage from "@/views/errors/NotFound.vue";
import AppLayout from "@/views/layouts/AppLayout.vue";
import AuthLayout from "@/views/layouts/AuthLayout.vue";
import ErrorLayout from "@/views/layouts/ErrorLayout.vue";
import ForgetPassword from "@/views/auth/ForgetPassword.vue";
import VueRouter from "vue-router";

const auth = {
    login: LoginPage,
    logout: LogoutPage,
    reset: ResetPage,
    forgetpassword:ForgetPassword,
};

const questions = {
    index: QuestionIndexPage,
    form: QuestionFormPage,
};

const surveys = {
    index: SurveyIndexPage,
    form: SurveyFormPage,
};

const errors = {
    notFound: NotFoundPage,
};

const router = new VueRouter({
    mode: "hash",
    linkActiveClass: "active--nav",
    routes: [
        {
            path: "/",
            component: AuthLayout,
            meta: {
                guest: true,
            },
            redirect: "login",
            children: [
                {
                    path: "login",
                    name: "login",
                    component: auth.login,
                },
                {
                    path: "reset-password",
                    name: "reset.password",
                    component: auth.reset,
                },
                {
                    path:"forget/password",
                    name:"forget.password",
                    component:auth.forgetpassword,
                }
            ],
        },
        {
            path: "/",
            component: AppLayout,
            meta: {
                auth: true,
            },
            children: [
                {
                    path: "questions",
                    name: "questions.index",
                    component: questions.index,
                },
                {
                    path: "questions/create",
                    name: "questions.create",
                    component: questions.form,
                },
                {
                    path: "surveys",
                    name: "surveys.index",
                    component: surveys.index,
                },
                {
                    path: "surveys/create",
                    name: "surveys.create",
                    component: surveys.form,
                },
                {
                    path: "surveys/:id",
                    name: "surveys.edit",
                    component: surveys.form,
                },
                {
                    path: "logout",
                    name: "logout",
                    component: auth.logout,
                },
            ],
        },
        {
            path: "/",
            component: ErrorLayout,
            children: [
                {
                    path: "*",
                    name: "404",
                    component: errors.notFound,
                },
            ],
        },
    ],
});

router.beforeEach((to, _, next) => {
    const authStore = useAuthStore();
    if (to.matched.some((record) => record.meta.auth)) {
        if (authStore.isAuthenticated) {
            next();
        } else {
            if (to.name === "logout") {
                next({ name: "login" });
            } else {
                next({
                    name: "login",
                    query: {
                        redirect: to.fullPath,
                    },
                });
            }
        }
    } else if (to.matched.some((record) => record.meta.guest)) {
        if (authStore.isAuthenticated) {
            next({
                name: "questions",
            });
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
