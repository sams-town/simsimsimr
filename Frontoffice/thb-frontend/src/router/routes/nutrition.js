export default [
    {
        path: '/nutrition',
        name: 'NutritionListView',
        component: () => import('@/views/pages/nutrition/NutritionView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/nutrition/list/label',
        name: 'NutritionListLabelView',
        component: () => import('@/views/pages/nutrition/print/NutritionLabelView.vue'),
        meta: {
            layout: 'full'
        }
    },
    {
        path: '/nutrition/list/struk',
        name: 'NutritionListStrukView',
        component: () => import('@/views/pages/nutrition/print/NutritionStrukView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
