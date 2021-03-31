<template>
<div id="dashboard">
    <div class="cards">
        <div id="first-card">
            <dashboard-card color="primary" icon="mdi-view-parallel" title="Bundles" :value="totalBundles"></dashboard-card>
        </div>
        <div id="second-card">
            <dashboard-card color="success" icon="mdi-home" title="Scanned" :value="totalScanned"></dashboard-card>
        </div>
        <div id="third-card">
            <dashboard-card color="blue" icon="mdi-account" title="Manual Encoded" :value="totalEncoded"></dashboard-card>
        </div>
        <div id="fourth-card">
            <dashboard-card color="error" icon="mdi-arrow-right-bold-box-outline" title="OUT" :value="totalOutBundles"></dashboard-card>
        </div>
    </div>
    <div id="content">
        <!-- <div class="scanned"></div> -->
        <div class="table">
            <v-card>
                <v-card-title>Bundles with <span class="error--text mx-1">OUT</span> status</v-card-title>
                <div class="out-bundles">
                    <v-data-table dense :headers="outBundlesTableHead" :items="outBundlesOverview"></v-data-table>
                </div>
            </v-card>
        </div>
        <div class="timeline">
            <v-card>
                <v-card-title>User activity logs</v-card-title>
                <div class="timeline-content">
                    <v-timeline align-top dense>
                    <v-timeline-item :color="logs.ActionTaken == 'OUT' ? 'error' : logs.ActionTaken == 'MANUAL' ? 'blue' : 'success'" small v-for="logs in userActivity" :key="logs.id">
                        <v-row class="pt-1">
                            <v-col cols="4">
                                <strong>{{logs.updated_by}}</strong>
                            </v-col>
                            <v-col>
                                <strong>{{logs.ActionTaken}}</strong>
                                <div class="caption">
                                    {{logs.BundleNo}} - {{logs.Remarks}}
                                </div>
                            </v-col>
                        </v-row>
                    </v-timeline-item>
                </v-timeline>
                </div>
            </v-card>
        </div>
    </div>
</div>
</template>

<script>
export default {
    data: () => {
        return {
            totalBundles: 0,
            totalScanned: 0,
            totalEncoded: 0,
            totalOutBundles: 0,
            outBundlesOverview: [],
            outBundlesTableHead: [{
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
                    text: 'Location',
                    value: 'LocationName',
                    sortable: false
                },
                {
                    text: 'Line',
                    value: 'LineName',
                    sortable: false
                },
                {
                    text: 'Item Id',
                    value: 'ItemId',
                    sortable: false
                },
                {
                    text: 'Invoice No.',
                    value: 'InvoiceNo',
                    sortable: false
                },
                {
                    text: 'Created Date',
                    value: 'CreatedDate',
                    sortable: false
                },
            ],
            userActivity: []
        }
    },
    created() {
        this.bundles();
        this.scannedBundles();
        this.encodedBundles();
        this.outBundles();
        this.outOverview();
        this.activityLogs();
    },
    methods: {
        async bundles() {
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.get(`${this.$url}/api/count/bundles`);
                this.totalBundles = response.data;
                this.$store.dispatch('hideProgressBar');
            } catch (error) {
                console.log(error);
                this.$store.dispatch('hideProgressBar');
            }
        },
        async scannedBundles() {
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.get(`${this.$url}/api/count/bundles/scanned`);
                this.totalScanned = response.data;
                this.$store.dispatch('hideProgressBar');
            } catch (error) {
                console.log(error);
                this.$store.dispatch('hideProgressBar');
            }
        },
        async encodedBundles() {
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.get(`${this.$url}/api/count/bundles/encoded`);
                this.totalEncoded = response.data;
                this.$store.dispatch('hideProgressBar');
            } catch (error) {
                console.log(error);
                this.$store.dispatch('hideProgressBar');
            }
        },
        async outBundles() {
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.get(`${this.$url}/api/count/bundles/out`);
                this.totalOutBundles = parseInt(response.data[0].Count);
                this.$store.dispatch('hideProgressBar');
            } catch (error) {
                console.log(error);
                this.$store.dispatch('hideProgressBar');
            }
        },
        async outOverview() {
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.get(`${this.$url}/api/overviews/bundles-out`)
                for (let index = 0; index < response.data.length; index++) {
                    response.data[index].SequenceId = index + 1;
                    response.data[index].CreatedDate = response.data[index].CreatedDate.substring(0, 10);
                }
                this.outBundlesOverview = await response.data;
                this.$store.dispatch('hideProgressBar');
            } catch (error) {
                console.log(error);
                this.$store.dispatch('hideProgressBar');
            }
        },
        async activityLogs() {
            try {
                this.$store.dispatch('showProgressBar');
                const response = await axios.get(`${this.$url}/api/dashboard/logs`);
                this.userActivity = response.data;
                this.$store.dispatch('hideProgressBar');
            } catch (error) {
                console.log(error);
                this.$store.dispatch('hideProgressBar');
            }
        }
    }
}
</script>

<style scoped>
#dashboard {
    margin: 4rem 2rem;
}

.cards {
    display: flex;
}

.cards div:not(:last-child) {
    margin-right: 1.5rem;
}

#first-card {
    flex: auto;
}

#second-card {
    flex: auto;
}

#third-card {
    flex: auto;
}

#fourth-card {
    flex: auto;
}

#content {
    display: flex;
}

.table {
    margin: 2rem 0;
    flex: auto;
}

.out-bundles {
    padding: 0 2rem;
}

.timeline {
    margin: 2rem 0 2rem 2rem;
    width: 30%;
}

.timeline-content {
    padding: 0 0 2rem 0;
}

.v-card__title {
    letter-spacing: 1px;
    font-size: medium;
}
</style>
