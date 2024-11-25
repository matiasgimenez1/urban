import { createRouter, createWebHistory } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '@/stores/auth';
import ProtectedRoute from '@/views/pages/auth/Access.vue';
import AppLayout from '@/layout/AppLayout.vue';

const routes = [
    {
        path: '/',
        redirect: (to) => {
            const store = useAuthStore();
            const { isLoggedIn } = storeToRefs(store);
            // Redirige a 'login' si el usuario no está autenticado, de lo contrario a 'dashboard'
            return !isLoggedIn.value ? { name: 'login' } : { name: 'dashboard' };
        }
    },
    {
        path: '/',
        component: AppLayout,
        children: [
            {
                path: '/',
                name: 'dashboard',
                component: () => import('@/views/Dashboard.vue'),
                meta: { auth: true } // Ruta protegida que requiere autenticación
            },

            {
                path: '/jugadores',
                name: 'jugadores',
                component: () => import('@/views/pages/personas/PersonasIndex.vue'),
                meta: { auth: true }
            },
            {
                path: '/jugador/:id',
                name: 'jugador',
                component: () => import('@/views/pages/personas/PersonaShow.vue'),
                meta: { auth: true }
            },
            {
                path: '/jugador',
                name: 'jugador-insert',
                component: () => import('@/views/pages/personas/PersonaShow.vue'),
                meta: { auth: true }
            },
            {
                path: '/juegos',
                name: 'juegos',
                component: () => import('@/views/pages/juegos/JuegosIndex.vue'),
                meta: { auth: true }
            },
            {
                path: '/juego/:id',
                name: 'juego',
                component: () => import('@/views/pages/juegos/JuegoShow.vue'),
                meta: { auth: true }
            },
            {
                path: '/juego',
                name: 'juego-insert',
                component: () => import('@/views/pages/juegos/JuegoShow.vue'),
                meta: { auth: true }
            },
            {
                path: '/productos',
                name: 'productos',
                component: () => import('@/views/pages/productos/ProductosIndex.vue')
            },

            {
                path: '/producto/:id',
                name: 'producto',
                component: () => import('@/views/pages/productos/ProductoShow.vue')
            },
            {
                path: '/producto',
                name: 'producto-insert',
                component: () => import('@/views/pages/productos/ProductoShow.vue')
            },
            {
                path: '/reservas',
                name: 'reservas',
                component: () => import('@/views/pages/reservas/ReservasIndex.vue')
            },

            {
                path: '/reserva/:id',
                name: 'reserva',
                component: () => import('@/views/pages/reservas/ReservaShow.vue')
            },
            {
                path: '/reserva',
                name: 'reserva-insert',
                component: () => import('@/views/pages/reservas/ReservaShow.vue')
            }
        ]
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/pages/auth/Login.vue'),
        meta: { guest: true } // Ruta solo para invitados
    },
    {
        path: '/pages/notfound',
        name: 'notfound',
        component: () => import('@/views/pages/NotFound.vue'),
        meta: { auth: true }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'notfound',
        component: () => import('@/views/pages/NotFound.vue'),
        meta: { auth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { left: 0, top: 0 };
    }
});

// Guarda global de navegación
router.beforeEach((to, from) => {
    const store = useAuthStore();
    const { isLoggedIn } = storeToRefs(store);

    // Verifica si la ruta requiere autenticación y si el usuario está autenticado
    if (to.meta.auth && !isLoggedIn.value) {
        return {
            name: 'login',
            query: { redirect: to.fullPath } // Redirige a login y guarda la ruta de destino
        };
    }
    // Si la ruta es solo para invitados y el usuario ya está autenticado, redirige a dashboard
    else if (to.meta.guest && isLoggedIn.value) {
        return { name: 'dashboard' };
    }
});

export default router;

// Explicación de las Modificaciones
// meta: { auth: true }: Se añade a las rutas que requieren autenticación, lo cual es verificado en beforeEach.
// meta: { guest: true }: Se añade a rutas como el login, donde solo los invitados pueden acceder (usuarios no autenticados).
// beforeEach Guard:
// Verifica si la ruta tiene meta.auth. Si el usuario no está autenticado (isLoggedIn es false), lo redirige a login.
// Si la ruta tiene meta.guest y el usuario ya está autenticado, lo redirige al dashboard.
// Con este ajuste, cualquier intento de acceder a rutas protegidas sin autenticarse redirigirá al usuario al login, y las rutas de invitados estarán bloqueadas para usuarios ya autenticados.
