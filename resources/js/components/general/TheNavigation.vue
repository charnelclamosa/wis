<template>
<div>
    <progress-bar></progress-bar>
    <v-app-bar class="app-bar" dark>
        <v-app-bar-nav-icon @click="drawer = true"></v-app-bar-nav-icon>
        <v-toolbar-title class="system-font">{{systemName}}</v-toolbar-title>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" absolute temporary>
        <template v-slot:prepend>
            <v-list-item two-line>
                <v-list-item-avatar>
                    <img src="https://randomuser.me/api/portraits/lego/5.jpg">
                </v-list-item-avatar>

                <v-list-item-content>
                    <v-list-item-title>Charnel S. Clamosa</v-list-item-title>
                    <v-list-item-subtitle>System Administrator</v-list-item-subtitle>
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
                <v-list-item to="/print">
                    <v-list-item-icon>
                        <v-icon>mdi-printer</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Print Stickers</v-list-item-title>
                </v-list-item>
                <v-list-item to="/monitoring">
                    <v-list-item-icon>
                        <v-icon>mdi-table</v-icon>
                    </v-list-item-icon>
                    <v-list-item-title>Monitoring</v-list-item-title>
                </v-list-item>
                <v-list-group no-action prepend-icon="mdi-cog">
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
        <template v-slot:append>
            <div class="pa-2">
                <block-btn @click.native="logout()" text="Logout"></block-btn>
            </div>
        </template>
    </v-navigation-drawer>
</div>
</template>

<script>
export default {
    data: () => {
        return {
            drawer: false,
            group: null,
            systemName: 'WIS'

        }
    },
    created() {
        this.initializeToken()
    },
    methods: {
        initializeToken() {
            const user = this.$store.getters.getUserData;
            axios.defaults.headers.common['Authorization'] = `Bearer ${user.token}`;
        },
        async logout() {
            try {
                await axios.post(`${this.$url}/api/logout`);
                this.$store.dispatch('authenticationClear');
                this.$router.push({
                    name: 'login'
                })
            } catch (error) {
                console.log(error)
            }
        }
    }

}
</script>

<style scoped>
.app-bar {
    background-image: linear-gradient(15deg, #033E44 0%, #0ABCD0 100%);
}
</style>
