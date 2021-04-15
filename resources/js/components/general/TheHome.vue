<template>
<div id="dashboard">
    <div class="cards">
        <div id="first-card">
            <dashboard-card color="primary" icon="mdi-view-parallel" title="Bundles" :value="totalBundles"></dashboard-card>
        </div>
        <div id="second-card">
            <dashboard-card color="success" icon="mdi-barcode-scan" title="Scanned" :value="totalScanned"></dashboard-card>
        </div>
        <div id="third-card">
            <dashboard-card color="blue" icon="mdi-keyboard-outline" title="Manual Encoded" :value="totalEncoded"></dashboard-card>
        </div>
        <div id="fourth-card">
            <dashboard-card color="error" icon="mdi-arrow-right-bold-box-outline" title="OUT" :value="totalOutBundles"></dashboard-card>
        </div>
    </div>
    <div id="content">
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
                            <v-row dense>
                                <v-col cols="4">
                                    <strong>{{logs.updated_by}}</strong>
                                </v-col>
                                <v-col class="pr-1">
                                    <strong>{{logs.ActionTaken}}</strong>
                                    <div class="caption">
                                        {{logs.BundleNo}} - {{logs.Remarks}}
                                        <span class="grey--text">{{formatDate(logs.created_at)}}</span>
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
import moment from 'moment';
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
            userActivity: [],
        }
    },
    created() {
        this.initializeData();
        this.updateData();
    },
    methods: {
        initializeData() {
            this.totalBundles = this.$store.getters.getTotalBundles;
            this.totalScanned = this.$store.getters.getTotalScanned;
            this.totalEncoded = this.$store.getters.getTotalEncoded;
            this.totalOutBundles = this.$store.getters.getTotalOutBundles;
            this.outBundlesOverview = this.$store.getters.getBundlesOverview;
            this.userActivity = this.$store.getters.getUserActivity;
        },
        updateData() {
            setInterval(() => {
                this.totalBundles = this.$store.getters.getTotalBundles;
                this.totalScanned = this.$store.getters.getTotalScanned;
                this.totalEncoded = this.$store.getters.getTotalEncoded;
                this.totalOutBundles = this.$store.getters.getTotalOutBundles;
                this.outBundlesOverview = this.$store.getters.getBundlesOverview;
                this.userActivity = this.$store.getters.getUserActivity;
            }, 60 * 1000);
        },
        formatDate(date) {
            return moment(date).format('lll');
        },
    }
}
</script>

<style scoped>
#dashboard {
    margin: 4rem 2rem 1rem 2rem;
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

.caption {
    display: flex;
    flex-direction: column;
    word-spacing: 1px;
}

.v-card__title {
    letter-spacing: 1px;
    font-size: medium;
}

@media screen and (max-width: 414px) {
    .cards, #content {
        display: flex;
        flex-direction: column;
    }
    .cards div:not(:last-child) {
    margin-right: 0;
    }
    .cards div {
        margin: 0.5rem 0;
    }
    .table {
        flex: none;
    }
    .timeline {
        width: 100%;
        margin: 0;
    } 
}
@media screen and (max-width: 601px) {
    .cards {
        display: flex;
        flex-wrap: wrap;
    }
    #content {
        display: flex;
        flex-direction: column;
    }
    .cards div:not(:last-child) {
    margin-right: 0;
    }
    .cards div {
        margin: 0.5rem 0.3rem;
    }
    .table {
        flex: none;
    }
    .timeline {
        width: 100%;
        margin: 0;
    } 
}
@media screen and (min-width: 602px) and (max-width: 800px) and (orientation: portrait) {
    .cards {
        display: flex;
        flex-wrap: wrap;
    }
    #content {
        display: flex;
        flex-direction: column;
    }
    .cards div:not(:last-child) {
    margin-right: 0;
    }
    .cards div {
        margin: 0.5rem 0.3rem;
    }
    .table {
        flex: none;
    }
    .timeline {
        width: 100%;
        margin: 0;
    } 
}
</style>
