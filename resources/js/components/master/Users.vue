<template>
<div class="full-screen">
    <div class="content">
        <div class="content-body">
            <v-data-table dense :headers="tableHead" :items="Users" class="elevation-2">
                <template v-slot:[`item.Role`]="{ item }">
                    {{item.Role == 1 ? 'Administrator' : 'User'}}
                </template>
                <template v-slot:[`item.Status`]="{ item }">
                    <v-chip label outlined small :color="item.deleted_at ? 'error' : 'success'">{{item.deleted_at ? 'Deleted' : 'Active'}}</v-chip>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-icon small class="mr-2" @click="openUpdateDialog(item); $v.$reset();">
                        mdi-pencil
                    </v-icon>
                    <v-icon small @click="actionButton(item)">
                        {{ item.deleted_at ? 'mdi-restore' : 'mdi-delete' }}
                    </v-icon>
                </template>
            </v-data-table>
        </div>
        <div class="content-footer">
            <primary-btn @click.native="storeDialog = true; $v.$reset();" text="Add"></primary-btn>
        </div>
    </div>
    <v-dialog persistent v-model="storeDialog" width="400">
        <v-card tile class="store">
            <div class="store-content">
                <v-text-field dense autofocus label="Username" v-model="UserCreate.Username" required :error-messages="storeUsernameValidation" @blur="$v.UserCreate.Username.$touch()"></v-text-field>
                <v-text-field dense label="Password" v-model="UserCreate.Password" :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" :type="showPassword ? 'text' : 'password'" @click:append="showPassword = !showPassword" required :error-messages="storePasswordValidation" @blur="$v.UserCreate.Password.$touch()"></v-text-field>
                <v-select dense label="Role" v-model="UserCreate.RoleId" :items="Roles" item-value="id" item-text="RoleName" required :error-messages="storeRoleIdValidation" @blur="$v.UserCreate.RoleId.$touch()"></v-select>
            </div>
            <div class="store-footer">
                <outline-btn @click.native="storeDialog = false" text="Cancel"></outline-btn>
                <primary-btn @click.native="storeUser()" text="Create" class="ml-2"></primary-btn>
            </div>
        </v-card>
    </v-dialog>
    <v-dialog persistent v-model="updateDialog" width="400">
        <card-progress-bar></card-progress-bar>
        <v-card tile class="dialog-card">
            <div class="update-content">
                <v-text-field dense label="Username" v-model="UserUpdate.Username" required :error-messages="updateUsernameValidation" @blur="$v.UserUpdate.Username.$touch()"></v-text-field>
                <v-text-field dense label="Password" v-model="UserUpdate.Password" :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" :type="showPassword ? 'text' : 'password'" @click:append="showPassword = !showPassword"></v-text-field>
                <v-select dense label="Role" v-model="UserUpdate.RoleId" :items="Roles" item-value="id" item-text="RoleName"></v-select>
            </div>
            <div class="update-footer">
                <outline-btn @click.native="updateDialog = false" text="Cancel"></outline-btn>
                <primary-btn @click.native="updateUser(); updateUserPassword()" text="Update" class="ml-2"></primary-btn>
            </div>
        </v-card>
    </v-dialog>
</div>
</template>

<script>
import { required } from 'vuelidate/lib/validators';
export default {
    data: () => {
        return {
            user: {},
            showPassword: false,
            tableHead: [{
                    text: 'ID',
                    value: 'id'
                },
                {
                    text: 'Username',
                    value: 'Username'
                },
                {
                    text: 'Role',
                    value: 'RoleName'
                },
                {
                    text: 'Status',
                    value: 'Status'
                },
                {
                    text: 'Action',
                    value: 'actions'
                }
            ],
            Users: [],
            Roles: [],
            UserCreate: {},
            UserUpdate: {},
            UserDelete: {},
            storeDialog: false,
            updateDialog: false,
            deleteDialog: false,
        }
    },
    validations: {
        UserCreate: { 
            Username: { required },
            Password: { required },
            RoleId: { required }
         },
        UserUpdate: { 
            Username: { required }
         }
    },
    computed: {
        storeUsernameValidation() {
            const errors = [];
            if(!this.$v.UserCreate.Username.$dirty) return errors;
            !this.$v.UserCreate.Username.required && errors.push('This field is required');
            return errors;
        },
        storePasswordValidation() {
            const errors = [];
            if(!this.$v.UserCreate.Password.$dirty) return errors;
            !this.$v.UserCreate.Password.required && errors.push('This field is required');
            return errors;
        },
        storeRoleIdValidation() {
            const errors = [];
            if(!this.$v.UserCreate.RoleId.$dirty) return errors;
            !this.$v.UserCreate.RoleId.required && errors.push('This field is required');
            return errors;
        },
        updateUsernameValidation() {
            const errors = [];
            if(!this.$v.UserUpdate.Username.$dirty) return errors;
            !this.$v.UserUpdate.Username.required && errors.push('This field is required');
            return errors;
        }
    },
    created() {
        this.getUsers();
        this.getRoles();
    },
    methods: {
        notification(type, text) {
            this.$notify({
                group: 'app',
                type: type,
                title: type == 'success' ? 'Success notification' : 'Error notification',
                text: text
            });
        },
        async getRoles() {
            try {
                const response = await axios.get(`${this.$url}/api/roles`);
                this.Roles = response.data;
            } catch (error) {
                console.log(error)
            }
        },
        async getUsers() {
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.get(`${this.$url}/api/users`);
                this.Users = response.data;
            } catch (error) {
                console.log(error)
            }
            this.$store.dispatch('hideProgressBar');
        },
        async storeUser() {
            this.$v.UserCreate.$touch();
            if(this.$v.UserCreate.$invalid) return;
            try {
                this.$store.dispatch('showCardProgressBar');
                const user = this.$store.getters.getUserData;
                await axios.post(`${this.$url}/api/users`, {
                    User: this.UserCreate,
                    updated_by: user.Username
                });
                await this.getUsers();
                this.storeDialog = false;
                this.UserCreate = {};
                this.notification('success', 'Store successful.');
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
            }
            this.$store.dispatch('hideCardProgressBar');
        },
        openUpdateDialog(parameter) {
            this.UserUpdate = Object.assign({}, parameter);
            this.updateDialog = true;
        },
        async updateUser() {
            this.$v.UserUpdate.$touch();
            if(this.$v.UserUpdate.$invalid) return;
            try {
                this.$store.dispatch('showCardProgressBar');
                const user = this.$store.getters.getUserData;
                const response = await axios.put(`${this.$url}/api/users/` + this.UserUpdate.id, {
                    User: this.UserUpdate
                });
                this.getUsers();
                this.updateDialog = false;
                this.notification('success', 'Update successful.');
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
            }
            this.$store.dispatch('hideCardProgressBar');
        },
        async updateUserPassword() {
            if(!this.UserUpdate.Password) return;
            try {
                const user = this.$store.getters.getUserData;
                await axios.patch(`${this.$url}/api/users/password/` + this.UserUpdate.id, {
                    Password: this.UserUpdate.Password,
                    updated_by: user.Username
                });
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
            }
        },
        actionButton(parameter) {
            let user = {};
            user = Object.assign({}, parameter);
            user.deleted_at ? this.restoreUser(user) : this.deleteUser(user);
        },
        async deleteUser(parameter) {
            try {
                this.$store.dispatch('showProgressBar');
                await axios.patch(`${this.$url}/api/users/delete/` + parameter.id);
                this.getUsers();
                this.notification('success', 'Delete successful.');
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
            }
            this.$store.dispatch('hideProgressBar');
        },
        async restoreUser(parameter) {
            try {
                this.$store.dispatch('showProgressBar');
                await axios.patch(`${this.$url}/api/users/restore/` + parameter.id);
                this.getUsers();
                this.notification('success', 'Restore successful.');
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
            }
            this.$store.dispatch('hideProgressBar');
        },
    }
}
</script>

<style scoped>
.content {
    display: flex;
    flex-direction: column;
    padding: 2rem;
}

.content-body {
    padding-bottom: 1rem;
}

.content-footer {
    display: flex;
    justify-content: flex-end;
}

.update-content {
    display: flex;
    flex-direction: column;
    padding-bottom: 1rem;
}

.update-footer {
    display: flex;
    justify-content: flex-end;
}

.store {
    padding: 2rem;
}

.store-content {
    display: flex;
    flex-direction: column;
    padding-bottom: 1rem;
}

.store-footer {
    display: flex;
    justify-content: flex-end;
}
</style>
