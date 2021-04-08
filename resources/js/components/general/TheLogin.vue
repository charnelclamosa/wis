<template>
<div class="body">
    <v-card tile class="login">
        <div class="login-logo">
            <v-img class="image" src="images/V1.png" alt="" eager />
        </div>
        <div class="login-form">
            <h3>Welcome back!</h3>
            <div>
                <v-text-field dense autofocus label="Username" v-model="Credentials.Username" required :error-messages="usernameValidation" @blur="$v.Credentials.Username.$touch()" @keyup.enter="handleLogin(Credentials)"></v-text-field>
            </div>
            <div>
                <v-text-field dense type="password" label="Password" v-model="Credentials.Password" required :error-messages="passwordValidation" @blur="$v.Credentials.Password.$touch()" @keyup.enter="handleLogin(Credentials)"></v-text-field>
            </div>
            <div>
                <block-btn @click.native="handleLogin(Credentials)" text="Login" :disabled="btnLoader" :loading="btnLoader"></block-btn>
            </div>
        </div>
    </v-card>
</div>
</template>

<script>
import {
    required,
    minLength,
} from 'vuelidate/lib/validators';
export default {
    data: () => {
        return {
            Credentials: {},
            loginFailed: false,
            url: './',
            btnLoader: false,
        }
    },
    validations: {
        Credentials: {
            Username: {
                required,
                minLength: minLength(5)
            },
            Password: {
                required
            }
        },
    },
    computed: {
        usernameValidation() {
            const errors = [];
            if (!this.$v.Credentials.Username.$dirty) return errors;
            !this.$v.Credentials.Username.required && errors.push('This field is required.');
            !this.$v.Credentials.Username.minLength && errors.push('Must be atleast (5) characters.');
            return errors;
        },
        passwordValidation() {
            const errors = [];
            if (!this.$v.Credentials.Password.$dirty) return errors;
            !this.$v.Credentials.Password.required && errors.push('This field is required.');
            return errors;
        }
    },
    created() {
        this.$store.dispatch('authenticationClear');
        this.bundles();
        this.scannedBundles();
        this.encodedBundles();
        this.outBundles();
        this.outOverview();
        this.activityLogs();
    },
    methods: {
        async notification(type, text) {
            this.$notify({
                group: 'app',
                type: type,
                title: type == 'success' ? 'Success notification' : 'Error notification',
                text: text
            });
        },
        async handleLogin(Credentials) {
            this.$v.Credentials.$touch()
            if (this.$v.Credentials.$invalid) return;
            try {
                this.btnLoader = true;
                const response = await axios.post(`${this.$url}/api/login`, {
                    Username: Credentials.Username,
                    Password: Credentials.Password
                });
                if (!response.data) {
                    await this.notification('error', 'Login failed.');
                    return;
                };
                const info = await this.getEmployeeInfo(Credentials.Username);
                await this.success(response.data, info);
                this.btnLoader = false;
            } catch (error) {
                console.log(error)
                this.notification('error', 'Oops something went wrong.');
                this.btnLoader = false;
            }
        },
        async success(data, info) {
            data.user.Token = await data.token;
            data.user.EmployeeName = await info.EmployeeName;
            data.user.Gender = await info.Gender;
            await this.$store.dispatch('authenticationSuccess', data.user);
            this.$router.push({
                name: 'home'
            });
        },
        async getEmployeeInfo(username) {
            try {
                const response = await axios.get(`${this.$url}/api/users/` + username);
                return response.data;
            } catch (error) {
                console.log(error);
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
.body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.login {
    display: flex;
    background-image: linear-gradient(to top, #d5d4d0 0%, #d5d4d0 1%, #eeeeec 31%, #efeeec 75%, #e9e9e7 100%);
}

.login-logo {
    display: flex;
    flex: auto;
    align-content: center;
    justify-content: center;
    padding: 0 2rem;
}

.image {
    position: relative;
    width: 100%;
    max-width: 455px;
    height: auto;
}

.login-form {
    display: flex;
    flex-direction: column;
    justify-content: center;
    width: 40%;
    padding: 0 2rem;
    border: 2px solid #073C40;
}

.login-form div {
    margin: 0.3rem 0;
}

.login-form h3 {
    text-align: center;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    letter-spacing: 0.1rem;
    margin: 1rem 0;
}

@media screen and (max-width: 414px) {
    .login-logo {
        display: none;
    }
    .login-form {
        width: 100%;
        padding: 4rem 2rem;
    }
}
</style>
