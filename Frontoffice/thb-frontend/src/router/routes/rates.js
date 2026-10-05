export default [
    // Master Tarif
    {
        path: '/rates/master',
        name: 'RatesMasterView',
        component: () => import('@/views/pages/rates/rates_master/RatesMasterView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // Mapping tarif perusahaan
    {
        path: '/rates/mapping',
        name: 'RatesMappingView',
        component: () => import('@/views/pages/rates/rates_mapping/RatesMappingView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // Master Data Jenis Tarif
    {
        path: '/rates/types',
        name: 'RatesTypesView',
        component: () => import('@/views/pages/rates/rates_type/RatesTypeView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // Master Data Sub-Jenis Tarif
    {
        path: '/rates/subtypes',
        name: 'RatesSubtypesView',
        component: () => import('@/views/pages/rates/rates_subtype/RatesSubtypeView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // Master Promo 
    {
        path: '/rates/promo',
        name: 'RatesPromoView',
        component: () => import('@/views/pages/rates/rates_promo/RatesPromoView.vue'),
        meta: {
            layout: 'full'
        }
    },

    {
        path: '/rates/promo/detail',
        name: 'RatesPromoDetailView',
        component: () => import('@/views/pages/rates/rates_promo/PromoDetailView.vue'),
        meta: {
            layout: 'full'
        }
    },

    // Master Paket
    {
        path: '/rates/package',
        name: 'RatesPackageView',
        component: () => import('@/views/pages/rates/rates_package/RatesPackageView.vue'),
        meta: {
            layout: 'full'
        }
    },
]
