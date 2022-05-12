import { useAuthStore } from "@/stores/auth";
import LoginPage from "@/views/auth/Login.vue";
import LogoutPage from "@/views/auth/Logout.vue";
import ResetPage from "@/views/auth/Reset.vue";
import QuestionFormPage from "@/views/questions/Form.vue";
import QuestionIndexPage from "@/views/questions/Index.vue";
import SurveyIndexPage from "@/views/surveys/Index.vue";
import SurveyFormPage from "@/views/surveys/Form.vue";
import MessageIndexPage from "@/views/messages/Index.vue";
import MessageShowPage from "@/views/messages/Show.vue";
import MessageFormPage from "@/views/messages/Form.vue";
import InboundList from "@/views/messages/index/InboundList.vue";
import OutboundList from "@/views/messages/index/OutboundList.vue";
import ReadList from "@/views/messages/index/ReadList.vue";
import RequireCallList from "@/views/messages/index/RequireCallList.vue";
import ArchivedList from "@/views/messages/index/ArchivedList.vue";
import NotFoundPage from "@/views/errors/NotFound.vue";
import AppLayout from "@/views/layouts/AppLayout.vue";
import AuthLayout from "@/views/layouts/AuthLayout.vue";
import ErrorLayout from "@/views/layouts/ErrorLayout.vue";
import FaqIndexPage from "@/views/faq/Index.vue";
import FaqFormPage from "@/views/faq/Form.vue";
import CreditFormPage from "@/views/credit/Form.vue";
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

const messages = {
    index: {
        main: MessageIndexPage,
        inboundList: InboundList,
        readList: ReadList,
        requireCallList: RequireCallList,
        archivedList: ArchivedList,
        outboundList: OutboundList,
    },
    show: MessageShowPage,
    form: MessageFormPage
};

const faq = {
    index: FaqIndexPage,
    form: FaqFormPage,
}

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
                    path: "messages",
                    name: "messages.index",
                    component: messages.index.main,
                    redirect: {
                        name: "messages.index.inbound",
                    },
                    children: [
                        {
                            path: "inbound",
                            name: "messages.index.inbound",
                            component: messages.index.inboundList,
                        },
                        {
                            path: "read",
                            name: "messages.index.read",
                            component: messages.index.readList,
                        },
                        {
                            path: "require-call",
                            name: "messages.index.requireCall",
                            component: messages.index.requireCallList,
                        },
                        {
                            path: "archived",
                            name: "messages.index.archived",
                            component: messages.index.archivedList,
                        }, {
                            path: "outbound",
                            name: "messages.index.outbound",
                            component: messages.index.outboundList,
                        },
                    ],
                },
                {
                    path: "messages/create",
                    name: "messages.create",
                    component: messages.form,
                },
                {
                    path: "messages/:id",
                    name: "messages.show",
                    component: messages.show,
                },
                {
                    path: "faq",
                    name: "faq.index",
                    component: faq.index,
                },
                {
                    path: "faq/create",
                    name: "faq.create",
                    component: faq.form,
                },
                {
                    path: "faq/:id/edit",
                    name: "faq.edit",
                    component: faq.form,
                },
                {
                    path: "credit",
                    name: "credit",
                    component: CreditFormPage,
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
