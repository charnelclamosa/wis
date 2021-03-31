<template>
<div class="body">
    <progress-bar style="width: 70%;"></progress-bar>
    <v-card tile class="login">
        <div class="login-logo">
            <h3>Wukong Inventory Status</h3>
        </div>
        <div class="login-form">
            <div><h3>Login</h3></div>
                <div><v-text-field dense autofocus label="Username" v-model="Credentials.Username" required :error-messages="usernameValidation" @blur="$v.Credentials.Username.$touch()" @keyup.enter="handleLogin()"></v-text-field></div>
                <div><v-text-field dense type="password" label="Password" v-model="Credentials.Password" required :error-messages="passwordValidation" @blur="$v.Credentials.Password.$touch()" @keyup.enter="handleLogin()"></v-text-field></div>
                <div><block-btn @click.native="handleLogin()" text="Login"></block-btn></div>
        </div>
    </v-card>
    <snack-bar v-if="loginFailed == true" color="error" message="Invalid credentials"></snack-bar>
</div>
</template>

<script>
import { required, minLength, } from 'vuelidate/lib/validators';
export default {
    data: () => {
        return {
            Credentials: {},
            loginFailed: false,
        }
    },
    validations: {
        Credentials: {
            Username: { required, minLength: minLength(5) },
            Password: { required }
        },
    },
    computed: {
        usernameValidation() {
            const errors =[];
            if(!this.$v.Credentials.Username.$dirty) return errors;
            !this.$v.Credentials.Username.required && errors.push('This field is required.');
            !this.$v.Credentials.Username.minLength && errors.push('Must be atleast (5) characters.');
            return errors;
        },
        passwordValidation() {
            const errors =[];
            if(!this.$v.Credentials.Password.$dirty) return errors;
            !this.$v.Credentials.Password.required && errors.push('This field is required.');
            return errors;
        }
    },
    created() {},
    methods: {
        async handleLogin() {
            this.$v.Credentials.$touch()
            if(this.$v.Credentials.$invalid) return;
            this.resetSnackBar();
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.post(`${this.$url}/api/login`, {
                    Username: this.Credentials.Username, 
                    Password: this.Credentials.Password
                });
                await this.success(response.data);
            } catch (error) {
                console.log(error)
                this.fail()
            }
            this.$store.dispatch('hideProgressBar');
        },
        resetSnackBar() {
            this.loginFailed = false;
        },
        async success(parameter) {
            parameter.user.token = parameter.token;
            await this.$store.dispatch('authenticationSuccess', parameter.user);
            this.$router.push({name: 'home'});
        },
        fail() {
            this.loginFailed = true;
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
    height: 50%;
    width: 70%;
}

.login-logo {
    display: flex;
    justify-content: center;
    background: url("../../../assets/images/logo.svg"), url("../../../assets/images/background.svg");
    background-size: cover; 
    width: 70%;
    margin: 1rem;
}

.login-logo h3 {
    color: rgb(33, 33, 33);
    font-family: 'Roboto', sans-serif;
    letter-spacing: 0.2rem;
    padding: 2rem 0;
}

.login-form {
    display: flex;
    flex-direction: column;
    justify-content: center;
    width: 30%;
    padding: 0 2rem;
    border: 2px solid #073C40;
    background-image: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
}
.login-form div {
    margin: 0.3rem 0;
}
.login-form h3 {
    text-align: center;
}

</style>
