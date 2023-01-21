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
            <option value="">All Codes</option>
            <option value="added_to_schedule">added_to_schedule</option>
            <option value="updated_schedule">updated_schedule</option>
            <option value="updated_session_description">updated_session_description</option>
            <option value="updated_session_participants">updated_session_participants</option>
            <option value="updated_session_title">updated_session_title</option>
        </select>
        <button class="btn btn-sm btn-link float-end" @click="loadReport"><b><i class="bi bi-arrow-repeat"></i> Reload</b></button>
        <h6>History of important changes to sessions on the schedule, newest to oldest</h6>
        <p>{{ totalHistory }} Changes found</p>
        <div class="log-list border border-1">
            <div v-for="log in logs" class="d-flex p-2 flex-column">
                <p>
                    {{ formattedDate(log.created_at) }} | {{ log.user?.first_name }} {{ log.user?.last_name }} | <b>{{ log.action_short_code }}</b>
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
            codeId: '',
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
                    this.logs = response.data.data;
                    this.laravelData = response.data;
                    this.totalHistory = response.data.meta.total;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the report`);
                });
        },
        formattedDate: function (date) {
            return moment(date).format('MMM D, YYYY h:mm:ss a');
        },
    }
}
</script>
