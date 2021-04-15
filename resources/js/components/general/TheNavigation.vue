<template>
<div>
    <progress-bar></progress-bar>
    <v-app-bar class="app-bar" dark>
        <v-app-bar-nav-icon @click="drawer = true"></v-app-bar-nav-icon>
        <v-toolbar-title class="system-font">{{systemName}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn small icon @click="logout()" :disabled="btnLoader" :loading="btnLoader"><v-icon>mdi-logout</v-icon></v-btn>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" absolute temporary>
        <template v-slot:prepend>
            <v-list-item two-line>
                <v-list-item-avatar size="60">
                    <v-img :src="user.Gender == 'F' ? '/lim/images/Female.png' : '/lim/images/Male.png'" alt="" eager />
                </v-list-item-avatar>
                <v-list-item-content>
                    <v-list-item-title>{{user.EmployeeName ? user.EmployeeName : user.Username}}</v-list-item-title>
                    <v-list-item-subtitle>{{user.RoleId == 1 ? 'Administrator' : 'User'}}</v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>
        </template>

        <v-divider></v-divider>
        <v-list nav dense>
            <v-list-item-group v-model="group" active-class="teal--text text--accent-4">
                <v-list-item to="/home">
                    <v-list-item-icon>
                        <v-icon>mdi-home</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Home</v-list-item-title>
                </v-list-item>
                <v-list-item to="/monitoring">
                    <v-list-item-icon>
                        <v-icon>mdi-table</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Monitoring</v-list-item-title>
                </v-list-item>
                <v-list-item to="/print">
                    <v-list-item-icon>
                        <v-icon>mdi-printer</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Print Bundles</v-list-item-title>
                </v-list-item>
                <v-list-group no-action prepend-icon="mdi-cog" v-show="user.RoleId == 1">
                    <template v-slot:activator>
                        <v-list-item-title>Admin Panel</v-list-item-title>
                    </template>
                    <v-list-item to="/users">
                        <v-list-item-title>Users</v-list-item-title>
                        <v-list-item-icon>
                            <v-icon>mdi-account-circle</v-icon>
                        </v-list-item-icon>
                    </v-list-item>
                </v-list-group>
            </v-list-item-group>
        </v-list>
        <!-- <template v-slot:append>
            <div class="px-2 pb-3">
                <block-btn @click.native="logout()" text="Logout" :disabled="btnLoader" :loading="btnLoader"></block-btn>
            </div>
        </template> -->
    </v-navigation-drawer>
</div>
</template>

<script>
export default {
    data: () => {
        return {
            drawer: false,
            group: null,
            systemName: 'Lumber Inventory Monitoring',
            user: {},
            male: '/images/Male.png',
            female: '/images/',
            btnLoader: false,

        }
    },
    created() {
        this.initializeToken();
        setInterval(() => {
            this.bundles();
            this.scannedBundles();
            this.encodedBundles();
            this.outBundles();
            this.outOverview();
            this.activityLogs();
        }, 60*1000);
    },
    methods: {
        initializeToken() {
            this.user = this.$store.getters.getUserData;
            axios.defaults.headers.common['Authorization'] = `Bearer ${this.user.Token}`;
        },
        async logout() {
            try {
                this.btnLoader = true;
                await axios.post(`${this.$url}/api/logout`);
                await this.$store.dispatch('authenticationClear');
                this.btnLoader = false;
                this.$router.push({ name: 'login'})
            } catch (error) {
                console.log(error);
                this.btnLoader = false;
            }
        },
        async bundles() {
            try {
                const response = await axios.get(`${this.$url}/api/count/bundles`);
                this.$store.dispatch('setTotalBundles', response.data);
            } catch (error) {
                console.log(error);
            }
        },
        async scannedBundles() {
            try {
                const response = await axios.get(`${this.$url}/api/count/bundles/scanned`);
                this.$store.dispatch('setTotalScanned', response.data);
            } catch (error) {
                console.log(error);
            }
        },
        async encodedBundles() {
            try {
                const response = await axios.get(`${this.$url}/api/count/bundles/encoded`);
                this.$store.dispatch('setTotalEncoded', response.data);
            } catch (error) {
                console.log(error);
            }
        },
        async outBundles() {
            try {
                const response = await axios.get(`${this.$url}/api/count/bundles/out`);
                this.$store.dispatch('setTotalOutBundles', parseInt(response.data[0].Count));
            } catch (error) {
                console.log(error);
            }
        },
        async outOverview() {
            try {
                const response = await axios.get(`${this.$url}/api/overviews/bundles-out`)
                for (let index = 0; index < response.data.length; index++) {
                    response.data[index].SequenceId = index + 1;
                    response.data[index].CreatedDate = response.data[index].CreatedDate.substring(0, 10);
                }
                this.$store.dispatch('setBundlesOverview', response.data);
            } catch (error) {
                console.log(error);
            }
        },
        async activityLogs() {
            try {
                const response = await axios.get(`${this.$url}/api/dashboard/logs`);
                this.$store.dispatch('setUserActivity', response.data);
            } catch (error) {
                console.log(error);
            }
        },
    }

}
</script>

<style scoped>
.app-bar {
    background-image: linear-gradient(15deg, #033E44 0%, #0ABCD0 100%);
}
</style>
