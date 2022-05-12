import { useAuthStore } from "@/stores/auth";
import LoginPage from "@/views/auth/Login.vue";
import DashboardPage from "@/views/Dashboard.vue";
import LogoutPage from "@/views/auth/Logout.vue";
import ResetPage from "@/views/auth/Reset.vue";
import UserIndexPage from "@/views/users/Index.vue";
import UserFormPage from "@/views/users/Form.vue";
import DocumentFormPage from "@/views/documents/Form.vue";
import DocumentIndexPage from "@/views/documents/Index.vue";
import PathologyFormPage from "@/views/pathologies/Form.vue";
import PathologyIndexPage from "@/views/pathologies/Index.vue";
import MessageTopicFormPage from "@/views/message-topics/Form.vue";
import MessageTopicIndexPage from "@/views/message-topics/Index.vue";
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
import PatientIndexPage from "@/views/patients/Index.vue";
import PatientShowPage from "@/views/patients/Show.vue";
import PatientSurveyDetailPage from "@/views/patients/show/SurveyDetail.vue";
import FaqIndexPage from "@/views/faq/Index.vue";
import FaqFormPage from "@/views/faq/Form.vue";
import CreditFormPage from "@/views/credit/Form.vue";
import UserProfile from "@/views/users/Profile.vue";
import ForgetPassword from "@/views/auth/ForgetPassword.vue";
import VueRouter from "vue-router";

const auth = {
    login: LoginPage,
    logout: LogoutPage,
    reset: ResetPage,
    forgetpassword:ForgetPassword,
};

const users = {
    index: UserIndexPage,
    form: UserFormPage,
    profile: UserProfile,
};


const patients = {
    index: PatientIndexPage,
    show: PatientShowPage,
    surveyShow: PatientSurveyDetailPage,
};

const pathologies = {
    index: PathologyIndexPage,
    form: PathologyFormPage,
};

const documents = {
    index: DocumentIndexPage,
    form: DocumentFormPage,
};

const questions = {
    index: QuestionIndexPage,
    form: QuestionFormPage,
};

const surveys = {
    index: SurveyIndexPage,
    form: SurveyFormPage,
};

const messageTopics = {
    index: MessageTopicIndexPage,
    form: MessageTopicFormPage,
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
    mode: "history",
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
                    path: "dashboard",
                    name: "dashboard",
                    component: DashboardPage,
                },
                {
                    path: "users",
                    name: "users.index",
                    component: users.index,
                },
                {
                    path: "users/create",
                    name: "users.create",
                    component: users.form,
                },
                //change profile
                {
                    path: "change/profile",
                    name: "user.profile",
                    component: users.profile,
                },
                {
                    path: "users/:id/edit",
                    name: "users.edit",
                    component: users.form,
                },
                {
                    path: "patients",
                    name: "patients.index",
                    component: patients.index,
                },
                {
                    path: "patients/:id",
                    name: "patients.show",
                    component: patients.show,
                },
                {
                    path: "patients/:patientId/surveys/:surveyId",
                    name: "patients.surveys.show",
                    component: patients.surveyShow,
                },
                {
                    path: "pathologies",
                    name: "pathologies.index",
                    component: pathologies.index,
                },
                {
                    path: "pathologies/create",
                    name: "pathologies.create",
                    component: pathologies.form,
                },
                {
                    path: "pathologies/:id/edit",
                    name: "pathologies.edit",
                    component: pathologies.form,
                },
                {
                    path: "documents",
                    name: "documents.index",
                    component: documents.index,
                },
                {
                    path: "documents/create",
                    name: "documents.create",
                    component: documents.form,
                },
                {
                    path: "documents/:id/edit",
                    name: "documents.edit",
                    component: documents.form,
                },
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
                    path: "message-topics",
                    name: "messageTopics.index",
                    component: messageTopics.index,
                },
                {
                    path: "message-topics/create",
                    name: "messageTopics.create",
                    component: messageTopics.form,
                },
                {
                    path: "message-topics/:id/edit",
                    name: "messageTopics.edit",
                    component: messageTopics.form,
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
                name: "dashboard",
            });
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
