export default [
  {
    path: "/satusehat",
    name: "SatusehatView",
  component: () => import("@/views/pages/satusehat/SatusehatDashboardView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/satusehat/practitioner",
    name: "SatusehatPractitionerView",
  component: () => import("@/views/pages/satusehat/SatusehatPractitionerView.vue"),
    meta: {
      layout: "full",
    },
  },
  {
    path: "/satusehat/department",
    name: "SatusehatDepartmentView",
  component: () => import("@/views/pages/satusehat/SatusehatDepartmentView.vue"),
    meta: {
      layout: "full",
    },
  },
]
