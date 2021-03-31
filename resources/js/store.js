import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        user: {},
        progressBar: false,
        cardProgressBar: false,
    },
    mutations: {
        showProgressBar: (state) => {
            (state.progressBar = true)
        },
        hideProgressBar: (state) => {
            (state.progressBar = false)
        },
        showCardProgressBar: (state) => {
            (state.cardProgressBar = true)
        },
        hideCardProgressBar: (state) => {
            (state.cardProgressBar = false)
        },
        authenticationSuccess: (state, payload) => {
            (state.user = payload)
        },
        authenticationClear: (state) => {
            (state.user = {})
        }
    },
    actions: {
        showProgressBar: (context) => {
            context.commit('showProgressBar')
        },
        hideProgressBar: (context) => {
            context.commit('hideProgressBar')
        },
        showCardProgressBar: (context) => {
            context.commit('showCardProgressBar')
        },
        hideCardProgressBar: (context) => {
            context.commit('hideCardProgressBar')
        },
        authenticationSuccess: (context, payload) => {
            context.commit('authenticationSuccess', payload)
        },
        authenticationClear: (context) => {
            context.commit('authenticationClear')
        }
    },
    getters: {
        getUserData: (state) => {
            return state.user;
        }
    },
    plugins: [createPersistedState()]
});
