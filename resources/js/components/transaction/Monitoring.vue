<template>
<div class="full-screen">
    <div class="selection">
        <v-select dense label="Location" v-model="inputSelects.LocationCode" :items="lumberLocations" item-value="LocationCode" item-text="LocationName" :error-messages="locationValidation" @blur="$v.inputSelects.LocationCode.$touch()"></v-select>
        <v-select dense label="Line" v-model="inputSelects.LineCode" :items="lumberLines" item-value="LineCode" item-text="LineName" :error-messages="lineValidation" @blur="$v.inputSelects.LineCode.$touch()"></v-select>
        <v-text-field dense label="Item Id" v-model="inputSelects.ItemId" :error-messages="itemIdValidation" @blur="$v.inputSelects.ItemId.$touch()"></v-text-field>
        <v-text-field dense label="Invoice No." v-model="inputSelects.InvoiceNo" :error-messages="invoiceNoValidation" @blur="$v.inputSelects.InvoiceNo.$touch()"></v-text-field>
        <v-menu v-model="date" :close-on-content-click="false" transition="fade-transition" offset-y min-width="290px">
            <template v-slot:activator="{ on, attrs }">
                <v-text-field dense readonly label="Date Created" v-model="inputSelects.Date" v-bind="attrs" v-on="on" :error-messages="dateValidation" @blur="$v.inputSelects.Date.$touch()"></v-text-field>
            </template>
            <v-date-picker no-title v-model="inputSelects.Date" @input="date = false"></v-date-picker>
        </v-menu>
        <primary-btn @click.native="getBundles(inputSelects)" text="View"></primary-btn>
    </div>
    <v-divider class="divider"></v-divider>
    <div class="content">
        <div class="content-body">
            <div class="content-legend">
                <h5>Legend:</h5>
                <h6>
                    <v-icon color="#078391">mdi-circle</v-icon>- Scan Details
                </h6>
                <h6>
                    <v-icon color="#4DB6AC">mdi-circle</v-icon>- IN Details
                </h6>
                <h6>
                    <v-icon color="#0ABCD0">mdi-circle</v-icon>- OUT Details
                </h6>
                <v-spacer></v-spacer>
                <manual-add-bundle :propLocations="lumberLocations" :propLines="lumberLines"></manual-add-bundle>
            </div>
            <v-data-table dense v-model="selected" show-select item-key="SequenceId" :headers="mainTableHead" :items="Bundles" class="elevation-2">
                <template v-slot:[`item.Quantity`]="{item}">
                    {{item.Qty + ' ' + item.QtyUnit}}
                </template>
                <template v-slot:[`item.BundleType`]="{item}">
                    <span :class="item.ScannedFlag == 'S' ? 'green--text' : 'blue--text'">{{item.ScannedFlag == 'S' ? 'Scanned' : 'Encoded'}}</span>
                </template>
                <template v-slot:[`item.BundleStatus`]="{item}">
                    <v-chip label outlined small :color="item.ShippedDate ? 'error' : 'success'">{{item.ShippedDate ? 'OUT' : 'IN'}}</v-chip>
                </template>
                <template v-slot:[`item.ShippedDate`]="{item}">
                    {{item.ShippedDate ? item.ShippedDate : '-'}}
                </template>
                <template v-slot:[`item.BundleItemShippingId`]="{item}">
                    {{item.BundleItemShippingId ? item.BundleItemShippingId : '-'}}
                </template>
                <template v-slot:[`item.Action`]="{item}">
                    <v-icon @click="getBundleLogs(item.BundleNo); bundleLogsDialog = true">mdi-dots-horizontal-circle-outline</v-icon>
                </template>
            </v-data-table>
        </div>
        <div class="content-footer">
            <!-- <primary-btn @click.native="saveForDraftsDialog = true" text="Save for Drafting" :disabled="selected.length > 0 ? false : true"></primary-btn> -->
            <outline-btn @click.native="bundlesOut(selected); outBundlesDialog = true" text="Out" :disabled="selected.length > 0 ? false : true" class="mx-2"></outline-btn>
            <primary-btn @click.native="bundlesIn(selected); inBundlesDialog = true" text="In" :disabled="selected.length > 0 ? false : true"></primary-btn>
        </div>
    </div>
    <v-dialog persistent v-model="outBundlesDialog">
        <v-card tile>
            <v-card-title class="card-title">Shipped out Bundles</v-card-title>
            <card-progress-bar></card-progress-bar>
            <div class="in-out-content">
                <div class="ship-out-form">
                    <div class="ship-first-col">
                        <v-select dense label="Purpose" v-model="outDetails.purposeId" :items="purposes" item-value="ItemShippingPurposeId" item-text="ItemShippingPurpose" :error-messages="outPurposeValidation" @blur="$v.outDetails.purposeId.$touch()"></v-select>
                        <v-menu v-model="inputDate" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y min-width="auto">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field dense v-model="outDetails.date" label="Shipped Date" prepend-inner-icon="mdi-calendar" readonly v-bind="attrs" v-on="on" :error-messages="outDateValidation" @blur="$v.outDetails.date.$touch()"></v-text-field>
                            </template>
                            <v-date-picker v-model="outDetails.date" @input="inputDate = false"></v-date-picker>
                        </v-menu>
                    </div>
                    <div class="ship-second-col">
                        <v-select dense label="Division" v-model="outDetails.divisionId" :items="divisions" item-value="DivisionId" item-text="Description" :error-messages="outDivisionValidation" @blur="$v.outDetails.divisionId.$touch()"></v-select>
                        <v-select dense label="Section" v-model="outDetails.sectionId" :items="sections" item-value="ItemShippingSectionId" item-text="ItemShippingSection" :error-messages="outSectionValidation" @blur="$v.outDetails.sectionId.$touch()"></v-select>
                    </div>
                    <div class="ship-third-col">
                        <v-textarea dense rows="2" no-resize label="Remarks" v-model="outDetails.remarks" :error-messages="outRemarksValidation" @blur="$v.outDetails.remarks.$touch()"></v-textarea>
                    </div>
                </div>
                <div class="ship-out-table">
                    <v-data-table dense class="elevation-1" :headers="inOutTableHead" :items="forOutProcess">
                        <template v-slot:[`item.Quantity`]="{item}">
                            {{item.Qty + ' ' + item.QtyUnit + '(s)'}}
                        </template>
                    </v-data-table>
                </div>
            </div>
            <div class="in-out-footer">
                <outline-btn @click.native="outBundlesDialog = false; $v.outDetails.$reset(); outDetails = {}" text="Cancel"></outline-btn>
                <primary-btn @click.native="saveBundlesOut(forOutProcess, outDetails.remarks)" text="OUT" class="ml-2"></primary-btn>
            </div>
        </v-card>
    </v-dialog>
    <v-dialog v-model="inBundlesDialog">
        <v-card tile>
            <v-card-title class="card-title">In Bundles</v-card-title>
            <card-progress-bar></card-progress-bar>
            <div class="in-out-content">
                <v-col cols="3">
                    <v-text-field dense rows="2" no-resize label="Remarks" v-model="inDetails.remarks" :error-messages="inRemarksValidation" @blur="$v.inDetails.remarks.$touch()"></v-text-field>
                </v-col>
                <div>
                    <v-data-table dense class="elevation-1" :headers="inOutTableHead" :items="forInProcess">
                        <template v-slot:[`item.Quantity`]="{item}">
                            {{item.Qty + ' ' + item.QtyUnit + '(s)'}}
                        </template>
                    </v-data-table>
                </div>
            </div>
            <div class="in-out-footer">
                <outline-btn @click.native="inBundlesDialog = false; $v.inDetails.$reset(); inDetails = {}" text="Cancel"></outline-btn>
                <primary-btn @click.native="saveBundlesIn(forInProcess, inDetails.remarks)" text="IN" class="ml-2"></primary-btn>
            </div>
        </v-card>
    </v-dialog>
    <v-dialog persistent v-model="bundleLogsDialog" max-width="800">
        <v-card tile>
            <v-card-title class="card-title">Bundle Logs</v-card-title>
            <div class="content">
                <div class="content-body">
                    <v-data-table dense class="elevation-1" :headers="bundleLogsTableHead" :items="bundleLogsRecords">
                        <template v-slot:[`item.Action`]="{item}">
                            <span :class="item.ActionTaken == 'OUT' ? 'error--text' : item.ActionTaken == 'IN' ? 'success--text' : 'blue--text'">{{item.ActionTaken}}</span>
                        </template>
                    </v-data-table>
                </div>
                <div class="content-footer">
                    <outline-btn @click.native="bundleLogsDialog = false" text="Close"></outline-btn>
                </div>
            </div>
        </v-card>
    </v-dialog>
    <!-- <v-dialog persistent v-model="saveForDraftsDialog" width="400">
        <v-card class="drafts">
            <div class="drafts-content">
                <p>Are you sure you want save the selected {{selected.length}} bundles for drafting?</p>
            </div>
            <div class="drafts-footer">
                <outline-btn @click.native="saveForDraftsDialog = false;" text="Cancel" class="mr-2"></outline-btn>
                <primary-btn @click.native="saveForDrafts(selected)" text="Save"></primary-btn>
            </div>
        </v-card>
    </v-dialog> -->
</div>
</template>

<script>
import {
    required
} from 'vuelidate/lib/validators';
export default {
    data: () => {
        return {
            user: {},
            divisions: [],
            sections: [],
            purposes: [],
            mainTableHead: [{
                    text: 'Location',
                    value: 'LocationName',
                    class: 'table-head',
                    sortable: false
                },
                {
                    text: 'Line',
                    value: 'LineName',
                    class: 'table-head px-10',
                    sortable: false
                },
                {
                    text: 'Bundle No.',
                    value: 'BundleNo',
                    class: 'table-head',
                    sortable: false
                },
                {
                    text: 'Type',
                    value: 'BundleType',
                    class: 'table-head',
                    sortable: false
                },
                {
                    text: 'Status',
                    value: 'BundleStatus',
                    class: 'table-head',
                    sortable: false
                },
                {
                    text: 'Invoice No.',
                    value: 'Invoiceno',
                    class: 'table-head-in',
                    sortable: false
                },
                {
                    text: 'Supplier',
                    value: 'SupplierId',
                    class: 'table-head-in',
                    sortable: false
                },
                {
                    text: 'Delivery Id',
                    value: 'DeliveryId',
                    class: 'table-head-in',
                    sortable: false
                },
                {
                    text: 'Item Id',
                    value: 'ItemId',
                    class: 'table-head-in',
                    sortable: false
                },
                {
                    text: 'Qty',
                    value: 'Quantity',
                    class: 'table-head-in',
                    sortable: false
                },
                {
                    text: 'Received Date',
                    value: 'ReceivedDate',
                    class: 'table-head-in',
                    sortable: false
                },
                {
                    text: 'Shipped Date',
                    value: 'ShippedDate',
                    class: 'table-head-out',
                    sortable: false
                },
                {
                    text: 'Shipping Id',
                    value: 'BundleItemShippingId',
                    class: 'table-head-out',
                    sortable: false
                },
                {
                    text: 'Action',
                    value: 'Action',
                    sortable: false
                }
            ],
            inOutTableHead: [{
                    text: 'Location',
                    value: 'LocationCode',
                    sortable: false
                },
                {
                    text: 'Line',
                    value: 'LineCode',
                    sortable: false
                },
                {
                    text: 'Bundle No',
                    value: 'BundleNo',
                    sortable: false
                },
                {
                    text: 'InvoiceNo',
                    value: 'Invoiceno',
                    sortable: false
                },
                {
                    text: 'Supplier',
                    value: 'SupplierId',
                    sortable: false
                },
                {
                    text: 'Delivery Id',
                    value: 'DeliveryId',
                    sortable: false
                },
                {
                    text: 'Item ID',
                    value: 'ItemId',
                    sortable: false
                },
                {
                    text: 'Description',
                    value: 'Description',
                    sortable: false
                },
                {
                    text: 'Quantity',
                    value: 'Quantity',
                    sortable: false
                },
            ],
            outDetails: {},
            inDetails: {},
            inputDate: null,
            lumberLocations: [],
            lumberLines: [],
            inputSelects: {
                LocationCode: '001',
                LineCode: '001',
                ItemId: 'P002817',
                InvoiceNo: 'AX1706061/IFW',
                Date: '2021-03-02'
            },
            date: null,
            Bundles: [],
            selected: [],
            inBundlesDialog: false,
            outBundlesDialog: false,
            saveForDraftsDialog: false,
            forOutProcess: [],
            forInProcess: [],
            bundleLogsDialog: false,
            bundleLogsTableHead: [{
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
                    text: 'Action Taken',
                    value: 'Action',
                    sortable: false
                },
                {
                    text: 'Remarks',
                    value: 'Remarks',
                    sortable: false
                },
                {
                    text: 'Action Taken Date',
                    value: 'created_at',
                    sortable: false
                },
                {
                    text: 'Action Taken By',
                    value: 'updated_by',
                    sortable: false
                }
            ],
            bundleLogsRecords: []
        }
    },
    validations: {
        inputSelects: {
            LocationCode: {
                required
            },
            LineCode: {
                required
            },
            ItemId: {
                required
            },
            InvoiceNo: {
                required
            },
            Date: {
                required
            }
        },
        outDetails: {
            purposeId: {
                required
            },
            divisionId: {
                required
            },
            sectionId: {
                required
            },
            date: {
                required
            },
            remarks: {
                required
            }
        },
        inDetails: {
            remarks: {
                required
            }
        }
    },
    computed: {
        locationValidation() {
            const errors = [];
            if (!this.$v.inputSelects.LocationCode.$dirty) return errors;
            !this.$v.inputSelects.LocationCode.required && errors.push('This field is required');
            return errors;
        },
        lineValidation() {
            const errors = [];
            if (!this.$v.inputSelects.LineCode.$dirty) return errors;
            !this.$v.inputSelects.LineCode.required && errors.push('This field is required.');
            return errors;
        },
        itemIdValidation() {
            const errors = [];
            if (!this.$v.inputSelects.ItemId.$dirty) return errors;
            !this.$v.inputSelects.ItemId.required && errors.push('This field is required.');
            return errors;
        },
        invoiceNoValidation() {
            const errors = [];
            if (!this.$v.inputSelects.InvoiceNo.$dirty) return errors;
            !this.$v.inputSelects.InvoiceNo.required && errors.push('This field is required.');
            return errors;
        },
        dateValidation() {
            const errors = [];
            if (!this.$v.inputSelects.Date.$dirty) return errors;
            !this.$v.inputSelects.Date.required && errors.push('This field is required.');
            return errors;
        },
        outPurposeValidation() {
            const errors = [];
            if (!this.$v.outDetails.purposeId.$dirty) return errors;
            !this.$v.outDetails.purposeId.required && errors.push('This field is required.');
            return errors;
        },
        outDivisionValidation() {
            const errors = [];
            if (!this.$v.outDetails.divisionId.$dirty) return errors;
            !this.$v.outDetails.divisionId.required && errors.push('This field is required.');
            return errors;
        },
        outSectionValidation() {
            const errors = [];
            if (!this.$v.outDetails.sectionId.$dirty) return errors;
            !this.$v.outDetails.sectionId.required && errors.push('This field is required.');
            return errors;
        },
        outDateValidation() {
            const errors = [];
            if (!this.$v.outDetails.date.$dirty) return errors;
            !this.$v.outDetails.date.required && errors.push('This field is required.');
            return errors;
        },
        outRemarksValidation() {
            const errors = [];
            if (!this.$v.outDetails.remarks.$dirty) return errors;
            !this.$v.outDetails.remarks.required && errors.push('This field is required.');
            return errors;
        },
        inRemarksValidation() {
            const errors = [];
            if (!this.$v.inDetails.remarks.$dirty) return errors;
            !this.$v.inDetails.remarks.required && errors.push('This field is required.');
            return errors;
        },
    },
    created() {
        this.setUser();
        this.getLumberLocations();
        this.getLumberLines();
        this.getDivisions();
        this.getSections();
        this.getPurposes();
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
        async getLumberLines() {
            try {
                const response = await axios.get(`${this.$url}/api/lumbers/lines`);
                this.lumberLines = response.data;
            } catch (error) {
                console.log(error)
            }
        },
        async getLumberLocations() {
            try {
                const response = await axios.get(`${this.$url}/api/lumbers/locations`);
                this.lumberLocations = response.data;
            } catch (error) {
                console.log(error);
            }
        },
        async getDivisions() {
            try {
                const response = await axios.get(`${this.$url}/api/divisions`);
                this.divisions = response.data;
            } catch (error) {
                console.log(error);
            }
        },
        async getSections() {
            try {
                const response = await axios.get(`${this.$url}/api/sections`);
                this.sections = response.data;
            } catch (error) {
                console.log(error);
            }
        },
        async getPurposes() {
            try {
                const response = await axios.get(`${this.$url}/api/purposes`);
                this.purposes = response.data;
            } catch (error) {
                console.log(error);
            }
        },
        async getBundles(object) {
            this.$v.inputSelects.$touch();
            if (this.$v.inputSelects.$invalid) return;
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.post(`${this.$url}/api/bundles/` + object.LocationCode + '/' + object.LineCode, {
                    LocationCode: object.LocationCode,
                    LineCode: object.LineCode,
                    ItemId: object.ItemId,
                    InvoiceNo: object.InvoiceNo,
                    Date: object.Date
                });
                for (let index = 0; index < response.data.length; index++) {
                    response.data[index].SequenceId = index + 1
                    response.data[index].ShippedDate = response.data[index].ShippedDate ? response.data[index].ShippedDate.substring(0, 10) : response.data[index].ShippedDate;
                    response.data[index].ReceivedDate = response.data[index].ReceivedDate ? response.data[index].ReceivedDate.substring(0, 10) : response.data[index].ReceivedDate;
                }
                this.Bundles = response.data;
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
            }
            this.$store.dispatch('hideProgressBar');
        },
        filterBundles(bundles, process) {
            let newArray = [];
            if (process == 'Out') {
                for (let index = 0; index < bundles.length; index++) {
                    if (!bundles[index].ShippedDate) {
                        newArray.push(bundles[index]);
                    }
                }
            }
            if (process == 'In') {
                for (let index = 0; index < bundles.length; index++) {
                    if (bundles[index].ShippedDate) {
                        newArray.push(bundles[index]);
                    }
                }
            }
            return newArray;
        },
        bundlesOut(bundles) {
            this.forOutProcess = [];
            this.forOutProcess = this.filterBundles(bundles, 'Out');
        },
        async saveBundlesOut(bundles, remarks) {
            this.$v.outDetails.$touch();
            if (this.$v.outDetails.$invalid) return;
            try {
                this.$store.dispatch('showCardProgressBar');
                const response = await axios.get(`${this.$url}/api/bundles/id/latest`);
                const latestId = response.data;
                // Out Header
                this.saveShippedOutHeader(latestId, this.outDetails);
                // Out Details
                this.saveShippedOutDetails(latestId, bundles);
                // Out Logs
                this.createBundleLogs(bundles, remarks, 'OUT');
                this.$store.dispatch('hideCardProgressBar');
                this.outBundlesDialog = false;
                this.outDetails = {};
                this.$v.outDetails.$reset();
                this.selected = [];
                this.notification('success', 'Bundles out successful.');
                await this.getBundles(this.inputSelects);
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
                this.$store.dispatch('hideCardProgressBar');
            }
        },
        async updateBundlesFlag(lumbers, flag) {
            try {
                this.$store.dispatch('showProgressBar');
                for (let index = 0; index < lumbers.length; index++) {
                    await axios.patch(`${this.$url}/api/lumbers/update/flag/` + lumbers[index].ScannedLumberID, {
                        ScannedLumberID: parseInt(lumbers[index].ScannedLumberID),
                        CheckFlag: flag,
                        UpdatedBy: this.user.Username
                    });
                }
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
            }
            this.$store.dispatch('hideProgressBar');
        },
        saveShippedOutHeader(id, details) {
            try {
                axios.post(`${this.$url}/api/bundles/out/header/` + id, {
                    BundleItemShippingId: id,
                    ShippedDate: details.date,
                    PurposeId: details.purposeId,
                    DivisionId: details.divisionId,
                    SectionId: details.sectionId
                });
            } catch (error) {
                console.log(error);
            }
        },
        saveShippedOutDetails(id, bundles) {
            try {
                axios.post(`${this.$url}/api/bundles/out/details/` + id, {
                    BundleItemShippingId: id,
                    Bundles: bundles
                });
            } catch (error) {
                console.log(error);
            }
        },
        createBundleLogs(bundles, remarks, action) {
            try {
                axios.post(`${this.$url}/api/logs`, {
                    Bundles: bundles,
                    ActionTaken: action,
                    Remarks: remarks,
                    Username: this.user.Username
                });
            } catch (error) {
                console.log(error);
            }
        },
        bundlesIn(bundles) {
            this.forInProcess = [];
            this.forInProcess = this.filterBundles(bundles, 'In');
        },
        async saveBundlesIn(bundles, remarks) {
            this.$v.inDetails.$touch();
            if (this.$v.inDetails.$invalid) return;
            try {
                this.$store.dispatch('showCardProgressBar');
                // Details
                await axios.delete(`${this.$url}/api/bundles/in/details/` + bundles[0].BundleItemShippingId, {BundleItemShippingId: parseInt(bundles[0].BundleItemShippingId)});
                // Header
                await axios.delete(`${this.$url}/api/bundles/in/header/` + bundles[0].BundleItemShippingId, {BundleItemShippingId: parseInt(bundles[0].BundleItemShippingId)});
                // Logs
                await this.createBundleLogs(bundles, remarks, 'IN');
                this.$store.dispatch('hideCardProgressBar');
                this.inBundlesDialog = false;
                this.inDetails = {};
                this.$v.inDetails.$reset();
                this.selected = [];
                this.notification('success', 'Bundles in successful.');
                await this.getBundles(this.inputSelects);
            } catch (error) {
                console.log(error);
                this.$store.dispatch('hideCardProgressBar');
            }
        },
        // async saveForDrafts(lumbers) {
        //     try {
        //         this.$store.dispatch('showProgressBar');
        //         await this.updateBundlesFlag(lumbers, 1)
        //         this.notification('success', 'Data has been updated.');;
        //         this.$store.dispatch('hideProgressBar');
        //     } catch (error) {
        //         console.log(error);
        //         this.notification('error', 'Oops! Something went wrong.');
        //         this.$store.dispatch('hideProgressBar');
        //     }
        // },
        async getBundleLogs(bundle) {
            try {
                const response = await axios.get(`${this.$url}/api/logs/` + bundle);
                for (let index = 0; index < response.data.length; index++) {
                    response.data[index].SequenceId = index + 1;
                    response.data[index].created_at = response.data[index].created_at.substring(0, 10);
                }
                this.bundleLogsRecords = response.data;
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
            }
        }
    }
}
</script>

<style scoped>
.selection {
    display: flex;
    padding: 1rem 2rem 0 2rem;
}

.selection div {
    margin-right: 2rem;
}

.divider {
    margin: 0 2rem;
}

.content {
    padding: 2rem;
}

.content-body {
    padding-bottom: 1rem;
}

.content-footer {
    display: flex;
    justify-content: flex-end;
}

.in-out-content {
    display: flex;
    flex-direction: column;
    margin: 1rem 2rem;
}

.ship-out-legend {
    font-size: small;
    margin-bottom: 0.5rem;
}

.ship-out-form {
    display: flex;
}

.ship-first-col {
    flex: 20%;
    padding-right: 4rem;
}

.ship-second-col {
    flex: 20%;
    padding-right: 4rem;

}

.ship-third-col {
    flex: 20%;
}

.in-out-footer {
    display: flex;
    justify-content: flex-end;
    padding: 0 2rem 1rem 0;
}

.drafts {
    display: flex;
    flex-direction: column;
    padding: 1rem 2rem;
}

.drafts-footer {
    display: flex;
    justify-content: flex-end;
}

.content-legend {
    display: flex;
    color: #616161 !important;
    margin-left: 0.5rem;
    padding-bottom: 0.5rem;
}

.content-legend h5 {
    margin-right: 1rem;
}

.content-legend h6 {
    margin-right: 1rem;
}
</style>
