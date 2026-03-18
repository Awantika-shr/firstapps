import { createRouter, createWebHistory } from "vue-router";
import ListView from "../components/ListView.vue";
import EditView from "../components/EditView.vue";

const routes = [
  { path: "/", redirect: "/listview" },
  { path: "/listview", component: ListView },
  { path: "/editview", component: EditView }
];

const router = createRouter({
  history: createWebHistory(), // history mode required for nice URLs
  routes
});

export default router;