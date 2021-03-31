<template>
<div class="full-screen">
    <div class="selection">
        <v-col cols="2">
            <v-select dense label="Location" v-model="selectedLocation" :items="lumberLocations" item-text="LocationName" return-object required :error-messages="locationValidation" @blur="$v.selectedLocation.$touch()"></v-select>
        </v-col>
        <v-col cols="2">
            <v-select dense label="Line" v-model="selectedLine" :items="lumberLines" item-text="LineName" return-object required :error-messages="lineValidation" @blur="$v.selectedLine.$touch()"></v-select>
        </v-col>
        <v-col cols="2">
            <primary-btn @click.native="getBundles(selectedLocation.LocationCode, selectedLine.LineCode)" text="View"></primary-btn>
        </v-col>
    </div>
    <div class="content">
        <div class="content-body">
            <div>
                <v-data-table dense :headers="tableHead" :items="Items" class="elevation-2">
                    <template v-slot:[`item.Quantity`]='{item}'>
                        {{item.Qty + ' ' + item.QtyUnit + '(s)'}}
                    </template>
                </v-data-table>
            </div>
        </div>
        <div class="content-footer">
            <primary-btn @click.native="printStickers()" text="Print" :disabled="Items.length > 0 ? false : true"></primary-btn>
        </div>
    </div>
</div>
</template>

<script>
import {
    required
} from 'vuelidate/lib/validators';
export default {
    data: () => {
        return {
            lumberLines: [],
            lumberLocations: [],
            tableHead: [
                {
                    text: 'No.',
                    value: 'SequenceId',
                    class: 'table-head',
                    sortable: false
                },
                {
                text: 'Bundle no.',
                value: 'BundleNo',
                class: 'table-head',
                    sortable: false

            },
                {
                    text: 'Item Id',
                    value: 'ItemId',
                    class: 'table-head',
                    sortable: false
                },
                {
                    text: 'Description',
                    value: 'Description',
                    class: 'table-head',
                    sortable: false
                },
                {
                    text: 'Delivery Id',
                    value: 'DeliveryId',
                    class: 'table-head' ,
                    sortable: false
                },
                {
                    text: 'Sawmill Id',
                    value: 'SawmillId',
                    class: 'table-head',
                    sortable: false
                },
                {
                    text: 'Qty',
                    value: 'Quantity',
                    class: 'table-head',
                    sortable: false
                },
                ],
            Items: [],
            selectedLine: {},
            selectedLocation: {},
        }
    },
    validations: {
        selectedLocation: {
            required
        },
        selectedLine: {
            required
        }
    },
    computed: {
        locationValidation() {
            const errors = [];
            if (!this.$v.selectedLocation.$dirty) return errors;
            !this.$v.selectedLocation.required && errors.push('This field is required');
            return errors;
        },
        lineValidation() {
            const errors = [];
            if (!this.$v.selectedLine.$dirty) return errors;
            !this.$v.selectedLine.required && errors.push('This field is required.');
            return errors;
        }
    },
    created() {
        this.getLumberLines();
        this.getLumberLocations();
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
        addSequenceId(array) {
            for (let index = 0; index < array.length; index++) {
                array[index].SequenceId = index + 1;
            }
            return array;
        },
        async getBundles(location, line) {
            this.$v.selectedLocation.$touch();
            this.$v.selectedLine.$touch();
            if (this.$v.selectedLine.$invalid && this.$v.selectedLocation.$invalid) return;
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.get(`${this.$url}/api/bundles/print/` + location + '/' + line)
                this.Items = await this.addSequenceId(response.data);
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
            }
            this.$store.dispatch('hideProgressBar');
        },
        async printStickers() {
            this.$v.selectedLocation.$touch();
            this.$v.selectedLine.$touch();
            if (this.$v.selectedLine.$invalid && this.$v.selectedLocation.$invalid) return;
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.post(`${this.$url}/api/print/bundles`, {
                    Items: this.Items,
                    Location: this.selectedLocation.LocationName,
                    Line: this.selectedLine.LineName
                }, {
                    responseType: 'blob'
                });
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Bundles.pdf');
                document.body.appendChild(link);
                link.click();
                this.$store.dispatch('hideProgressBar');
            } catch (error) {
                console.log(error);
                this.notification('error', 'Oops! Something went wrong.');
                this.$store.dispatch('hideProgressBar');
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

.content {
    padding: 0 2rem;
}

.content-body {
    padding-bottom: 1rem;
}

.content-footer {
    display: flex;
    justify-content: flex-end;
}
</style>
