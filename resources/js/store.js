import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        user: {},
        progressBar: false,
        cardProgressBar: false,
        totalBundles: 0,
        totalScanned: 0,
        totalEncoded: 0,
        totalOutBundles: 0,
        outBundlesOverview: [],
        userActivity: []
    },
    mutations: {
        showProgressBar: (state) => {state.progressBar = true},
        hideProgressBar: (state) => {state.progressBar = false},
        showCardProgressBar: (state) => {state.cardProgressBar = true},
        hideCardProgressBar: (state) => {state.cardProgressBar = false},
        authenticationSuccess: (state, payload) => {state.user = payload},
        authenticationClear: (state) => {state.user = {}},
        setTotalBundles: (state, payload) => {state.totalBundles = payload},
        setTotalScanned: (state, payload) => {state.totalScanned = payload},
        setTotalEncoded: (state, payload) => {state.totalEncoded = payload},
        setTotalOutBundles: (state, payload) => {state.totalOutBundles = payload},
        setBundlesOverview: (state, payload) => {state.outBundlesOverview = payload},
        setUserActivity: (state, payload) => {state.userActivity = payload}
    },
    actions: {
        showProgressBar: (context) => {context.commit('showProgressBar')},
        hideProgressBar: (context) => {context.commit('hideProgressBar')},
        showCardProgressBar: (context) => {context.commit('showCardProgressBar')},
        hideCardProgressBar: (context) => {context.commit('hideCardProgressBar')},
        authenticationSuccess: (context, payload) => {context.commit('authenticationSuccess', payload)},
        authenticationClear: (context) => {context.commit('authenticationClear')},
        setTotalBundles: (context, payload) => {context.commit('setTotalBundles', payload)},
        setTotalScanned: (context, payload) => {context.commit('setTotalScanned', payload)},
        setTotalEncoded: (context, payload) => {context.commit('setTotalEncoded', payload)},
        setTotalOutBundles: (context, payload) => {context.commit('setTotalOutBundles', payload)},
        setBundlesOverview: (context, payload) => {context.commit('setBundlesOverview', payload)},
        setUserActivity: (context, payload) => {context.commit('setUserActivity', payload)},
    },
    getters: {
        getUserData: (state) => { return state.user},
        getTotalBundles: (state) => {return state.totalBundles},
        getTotalScanned: (state) => {return state.totalScanned},
        getTotalEncoded: (state) => {return state.totalEncoded},
        getTotalOutBundles: (state) => {return state.totalOutBundles},
        getBundlesOverview: (state) => {return state.outBundlesOverview},
        getUserActivity: (state) => {return state.userActivity},
    },
    plugins: [createPersistedState()]
});
