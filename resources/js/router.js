import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store'

import login from '../views/general/TheLogin';
import home from '../views/general/TheHome';
import navDrawer from './components/general/TheNavigation';
import sticker from '../views/transaction/PrintSticker';
import monitoring from '../views/transaction/Monitoring';
import users from '../views/master/Users';


Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            redirect: '/login'
        },
        {
            path: '/login',
            name: 'login',
            component: login
        },
        {
            path: '/home',
            name: 'home',
            meta: {
                requiresAuth: true
            },
            components: {
                default: home,
                navigation: navDrawer
            },
            beforeEnter: () => {
                
            }
        },
        {
            path: '/print',
            name: 'print',
            meta: {
                requiresAuth: true
            },
            components: {
                default: sticker,
                navigation: navDrawer
            }
        },
        {
            path: '/monitoring',
            name: 'monitoring',
            meta: {
                requiresAuth: true
            },
            components: {
                default: monitoring,
                navigation: navDrawer
            }
        },
        {
            path: '/users',
            name: 'users',
            meta: {
                requiresAuth: true
            },
            components: {
                default: users,
                navigation: navDrawer
            }
        },
    ]
})
router.beforeEach((to, from, next) => {
    if(to.meta.requiresAuth) {
        const user = store.getters.getUserData;
        if(!user.hasOwnProperty('token')) {
            next('/login');
        } else {
            next();
        }
    } else {
        next();
    }
});
export default router