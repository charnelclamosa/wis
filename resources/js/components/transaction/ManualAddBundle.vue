<template>
<div>
    <outline-btn @click.native="dialog = true" text="Manual"></outline-btn>
    <v-dialog v-model="dialog">
        <v-card tile>
            <v-card-title class="card-title">Manual add of bundles</v-card-title>
            <card-progress-bar></card-progress-bar>
            <div class="encode">
                <div class="encode-inputs">
                    <div class="input-location">
                        <v-select dense label="Location" v-model="encodeInputs.LocationCode" :items="propLocations" item-value="LocationCode" item-text="LocationName" :error-messages="locationValidation" @blur="$v.encodeInputs.LocationCode.$touch()"></v-select>
                    </div>
                    <div class="input-line">
                        <v-select dense label="Line" v-model="encodeInputs.LineCode" :items="propLines" item-value="LineCode" item-text="LineName" :error-messages="lineValidation" @blur="$v.encodeInputs.LineCode.$touch()"></v-select>
                    </div>
                    <div class="input-bundle">
                        <v-text-field dense label="Bundle No." v-model="encodeInputs.BundleNo" :error-messages="bundleValidation" @blur="$v.encodeInputs.BundleNo.$touch()"></v-text-field>
                    </div>
                    <div>
                        <primary-btn @click.native="addBundle(encodeInputs.BundleNo); $v.encodeInputs.BundleNo.$reset()" text="ADD"></primary-btn>
                    </div>
                </div>
                <div v-if="showErrorMessage">
                    <p class="error--text">
                        <v-icon color="error">mdi-alert-circle</v-icon>{{errorMessage}}
                    </p>
                </div>
                <div class="encode-table">
                    <v-data-table :headers="tableHead" :items="Bundles" class="elevation-1">
                        <template v-slot:[`item.Quantity`]="{item}">
                            {{item.Qty + ' ' + item.QtyUnit + '(s)'}}
                        </template>
                    </v-data-table>
                </div>
                <div class="encode-footer">
                    <outline-btn @click.native="dialog = false; encodeInputs = {}; Bundles = []; $v.encodeInputs.$reset()" text="Cancel"></outline-btn>
                    <primary-btn @click.native="saveBundles(encodeInputs)" text="SAVE" class="ml-2"></primary-btn>
                </div>
                <v-spacer></v-spacer>
            </div>
        </v-card>
    </v-dialog>
</div>
</template>

<script>
import {
    required
} from 'vuelidate/lib/validators';
export default {
    props: {
        propLocations: {
            type: Array,
            default: () => []
        },
        propLines: {
            type: Array,
            default: () => []
        }
    },
    data: () => {
        return {
            user: {},
            dialog: false,
            encodeInputs: {},
            showErrorMessage: false,
            errorMessage: null,
            Bundles: [],
            tableHead: [{
                    text: 'No.',
                    value: 'SequenceId',
                    sortable: false
                },
                {
                    text: 'Bundle No.',
                    value: 'BundleNo',
                    sortable: false
                },
                {
                    text: 'Item Id',
                    value: 'ItemId',
                    sortable: false
                },
                {
                    text: 'Description',
                    value: 'Description',
                    sortable: false
                },
                {
                    text: 'Invoice No.',
                    value: 'Invoiceno',
                    sortable: false
                },
                {
                    text: 'Delivery Id',
                    value: 'DeliveryId',
                    sortable: false
                },
                {
                    text: 'Quantity',
                    value: 'Quantity',
                    sortable: false
                },
            ],
        }
    },
    validations: {
        encodeInputs: {
            LocationCode: {
                required
            },
            LineCode: {
                required
            },
            BundleNo: {
                required
            }
        }
    },
    computed: {
        locationValidation() {
            const errors = [];
            if (!this.$v.encodeInputs.LocationCode.$dirty) return errors;
            !this.$v.encodeInputs.LocationCode.required && errors.push('This field is required.');
            return errors;
        },
        lineValidation() {
            const errors = [];
            if (!this.$v.encodeInputs.LineCode.$dirty) return errors;
            !this.$v.encodeInputs.LineCode.required && errors.push('This field is required');
            return errors;
        },
        bundleValidation() {
            const errors = [];
            if (!this.$v.encodeInputs.BundleNo.$dirty) return errors;
            !this.$v.encodeInputs.BundleNo.required && errors.push('This field is required');
            return errors;
        }
    },
    created() {
        this.setUser();
    },
    methods: {
        setUser() {
            this.user = this.$store.getters.getUserData;
        },
        notification(type, text) {
            this.$notify({
                group: 'app',
                type: type,
                title: type == 'success' ? 'Success notification' : 'Error notification',
                text: text
            });
        },
        async isExistsReceived(BundleNo) {
            try {
                const response = await axios.get(`${this.$url}/api/bundles/validate/received/` + BundleNo);
                return response.data[0];
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.')
            }
        },
        async isExistsScanned(BundleNo) {
            try {
                const response = await axios.get(`${this.$url}/api/bundles/validate/scanned/` + BundleNo);
                return response.data[0];
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.')
            }
        },
        async addBundle(BundleNo) {
            this.$v.encodeInputs.BundleNo.$touch();
            if (this.$v.encodeInputs.BundleNo.$invalid) return;
            this.$store.dispatch('showCardProgressBar');  
            let received = await this.isExistsReceived(BundleNo);
            let scanned = await this.isExistsScanned(BundleNo);
            this.$store.dispatch('hideCardProgressBar');
            // Check if exists in BundlesReceivedView
            if (!received) {
                this.errorMessage = 'The bundle you entered is not yet delivered.';
                this.showErrorMessage = true;
                return;
            }
            // Check if exists in ScannedLumberDetails
            if(scanned) {
                this.errorMessage = 'The bundle you entered is already exists in the system.';
                this.showErrorMessage = true;
                return;
            }
            // Check if bundle no is already exist in table
            for (let index = 0; index < this.Bundles.length; index++) {
                if (this.Bundles[index].BundleNo == BundleNo) return;
            }
            this.Bundles.push(received);
            // Add sequence id
            for (let index = 0; index < this.Bundles.length; index++) {
                this.Bundles[index].SequenceId = index + 1;
            }
            this.showErrorMessage = false;
            this.encodeInputs.BundleNo = null;
        },
        async saveBundles(bundleDetails) {
            this.$v.encodeInputs.LocationCode.$touch();
            this.$v.encodeInputs.LineCode.$touch();
            if(this.$v.encodeInputs.LocationCode.$invalid || this.$v.encodeInputs.LineCode.$invalid) return;
            try {
              this.$store.dispatch('showCardProgressBar');  
              const response = await axios.get(`${this.$url}/api/bundles/manual/id/latest`);
              const latestId = response.data;
              await this.saveEncodeHeader(latestId, bundleDetails);
              await this.saveEncodeDetails(latestId, this.Bundles);
              await this.createBundleLogs(this.Bundles, 'Manual add', 'MANUAL')
              this.$store.dispatch('hideCardProgressBar');
              this.dialog = false;
              this.notification('success', 'Data has been created.');
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
                this.$store.dispatch('hideCardProgressBar');
            }
        },
        async saveEncodeHeader(id, bundle) {
            try {
                await axios.post(`${this.$url}/api/bundles/manual/header/` + id, {
                    LocationCode: bundle.LocationCode,
                    LineCode: bundle.LineCode,
                    Username: this.user.Username,
                });
            } catch (error) {
                console.log(error);
            }
        },
        async saveEncodeDetails(id, bundles) {
            try {
                for (let index = 0; index < bundles.length; index++) {
                    await axios.post(`${this.$url}/api/bundles/manual/details/` + bundles[index].BundleNo, {
                        ScannedLumberID: id,
                        BundleCode: bundles[index].BundleNo,
                        Username: this.user.Username,
                    });
                }
            } catch (error) {
                console.log(error);
            }
        },
        async createBundleLogs(bundles, remarks, action) {
            try {
                await axios.post(`${this.$url}/api/logs`, {
                    Bundles: bundles,
                    ActionTaken: action,
                    Remarks: remarks,
                    Username: this.user.Username
                });
            } catch (error) {
                console.log(error);
            }
        },
    }
}
</script>

<style scoped>
.encode {
    display: flex;
    flex-direction: column;
    padding: 2rem;
}

.encode-inputs {
    display: flex;
    width: 50%;
    justify-content: flex-start;
}

.input-location {
    margin-right: 1rem;
}

.input-line {
    margin-right: 1rem;
}

.input-bundle {
    margin-right: 1rem;
}

.encode-table {
    margin: 1rem 0;
}

.encode-footer {
    display: flex;
    justify-content: flex-end;
}
</style>
