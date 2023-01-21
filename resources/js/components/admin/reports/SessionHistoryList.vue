<style>
.log-list div:nth-child(odd) {
    background-color: lightgrey;
}

.log-list div:nth-child(even) {
    background-color: white;
}
</style>

<template>
    <div class="">
        <h2>Change Log</h2>
        <select v-model="codeId" class="form-select w-25 mb-2 me-2 float-end" @change="loadReport">
            <option value="0">All Codes</option>
            <option v-for="shortCode in shortCodes" :value="shortCode.name">{{ shortCode.name }}</option>
        </select>
        <button class="btn btn-sm btn-link float-end" @click="loadReport"><b><i class="bi bi-arrow-repeat"></i> Reload</b></button>
        <h6>History of important changes to sessions on the schedule, newest to oldest</h6>
        <p>{{ totalHistory }} Changes found</p>
        <div class="log-list border border-1">
            <div v-for="log in logs" class="d-flex p-2 flex-column">
                <p>
                    {{ formattedDate(log.created_at) }} | <b>{{ log.action_short_code }}</b>
                </p>
                <p>
                    {{ log.action }}
                </p>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-2">
            <Pagination :data="laravelData" :limit="3" :show-disabled="false" @pagination-change-page="loadReport" />
        </div>
    </div>
</template>

<script>
import moment from "moment/moment";

export default {
    props: ['conferenceId'],
    data: function() {
        return {
            shortCodes: {},
            codeId: 0,
            laravelData: {},
            totalHistory: 0,
            logs: {},
        }
    },
    mounted() {
        this.loadReport();
    },
    methods: {
        loadReport: function(page = 1) {
            axios.get(`/api/admin/report/session-history-list/${this.conferenceId}`, { params: { short_code: this.codeId, page: page }})
                .then((response) => {
                    // console.log(response.data.data);
                    this.logs = response.data.data;
                    this.laravelData = response.data;
                    this.totalHistory = response.data.meta.total;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the report`);
                });
        },
        changeCodeId: function() {

        },
        formattedDate: function (date) {
            return moment(date).format('MMM D, YYYY h:mm:ss a');
        },
    }
}
</script>
