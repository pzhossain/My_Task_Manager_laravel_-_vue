import { createRouter, createWebHistory } from "vue-router";
import LoginPage from "./pages/LoginPage.vue";
import RegistrationPage from "./pages/RegistrationPage.vue";
import DashboardPage from "./pages/DashboardPage.vue";
import CreateTask from "./components/Tasks/CreateTask.vue";
import NewTasks from "./components/Tasks/NewTasks.vue";
import ProgressTasks from "./components/Tasks/ProgressTasks.vue";
import CompletedTasks from "./components/Tasks/CompletedTasks.vue";
import CanceledTasks from "./components/Tasks/CanceledTasks.vue";
import EditTask from "./components/Tasks/EditTask.vue";
import TrashedTasks from "./components/Tasks/TrashedTasks.vue";
import SummaryPage from "./components/SummaryPage.vue";
import ProfilePage from "./components/ProfilePage.vue";
import NProgress from "nprogress";
import { useAuthStore } from "./stores/authStore";

const routes = [
  {
    path: "/",
    redirect: "/login",
  },
  {
    path: "/login",
    component: LoginPage,
    name: "login",
  },
  {
    path: "/register",
    component: RegistrationPage,
    name: "register",
  },
  {
    path: "/dashboard",
    component: DashboardPage,
    name: "dashboard",
    meta: { requiresAuth: true },
    children: [
      {
        path: "summary",
        component: SummaryPage,
        name: "summary",
      },
      {
        path: "profile",
        component: ProfilePage,
        name: "profile",
      },
      {
        path: "create",
        component: CreateTask,
        name: "create",
      },
      {
        path: "newtasks",
        component: NewTasks,
        name: "newtasks",
      },
      {
        path: "inprogress",
        component: ProgressTasks,
        name: "inprogress",
      },
      {
        path: "completed",
        component: CompletedTasks,
        name: "completed",
      },
      {
        path: "cancelled",
        component: CanceledTasks,
        name: "cancelled",
      },
      {
        path: "tasks/:id/edit",
        name: "edittask",
        component: EditTask,
      },
      {
        path: "tasks/trashed",
        component: TrashedTasks,
        name: "trashed",
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const auth = useAuthStore();

  const isAuthenticated = auth.isAuthenticated;

  // if user is not logged in but trying to access dashboard or other protected routes, redirect to login page
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next({ name: "login" });
  }

  //if user is already logged in but tryin to access login/ registration page then redirect to dashboard page
  if ((to.name === "login" || to.name === "register") && isAuthenticated) {
    return next({ name: "summary" });
  }

  next();
});

// Routing Progress
router.beforeResolve((to, from, next) => {
  if (to.name) {
    NProgress.start();
  }
  next();
});

router.afterEach(() => {
  NProgress.done();
});

NProgress.configure({
  showSpinner: false,
  template:
    '<div class="bar" role="bar" style="background-color: red"><div class="peg"><div class="peg" ></div></div><div  class="spinner" role="spinner"><div class="spinner-icon"></div></div>',
});

export default router;