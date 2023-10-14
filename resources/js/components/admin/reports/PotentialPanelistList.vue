<style>
.table-free-width td, th {
    min-width: 10px;
}
</style>

<template>
    <div class="">
        <h2>Potential Participant List</h2>
        <div class="float-end">
            <select v-model="type_id" @change="loadReport" class="form-select">
                <option value="1">Panelists</option>
                <option value="2">Presenters</option>
            </select>
        </div>
        <h6>Sortable list of potential panelists/presenters</h6>
        {{ data.length }} people found

        <table class="table table-scrollable table-free-width table-sm table-striped bg-white mt-2">
            <thead style="z-index: 500">
            <tr class="bg-secondary text-white">
                <td></td>
                <td class="ts-sc"><button class="btn btn-link" @click="sortList('last_name')"><i v-if="sort == 'last_name'" :class="[desc ? 'bi bi-caret-down' : 'bi bi-caret-up']"></i>Name</button></td>
                <td class="ts-sc">Badge Name</td>
                <td class="ts-sc text-center"><button class="btn btn-link" @click="sortList('total')"><i v-if="sort == 'total'" :class="[desc ? 'bi bi-caret-down' : 'bi bi-caret-up']"></i>Total</button></td>
            </tr>
            </thead>
            <tbody >
            <tr v-for="d in data">
                <td><a :href="`/admin/user-profile/${d.user_id}`" target="_blank" class="btn btn-link">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </td>
                <td>
                    <a :href="`/admin/user-profile/${d.user_id}`" target="_blank" class="text-decoration-none text-body fw-bold">
                        {{ d.last_name }}, {{ d.first_name }}
                    </a>
                </td>
                <td class="text-nowrap">{{d.badge_name}}</td>
                <td class="text-center"><button class="btn btn-sm btn-outline-secondary mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#participant-session-data" @click="getModalSessionData(d.user_id, 1, 'interests')">{{ d.total }}</button></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="participant-session-data" aria-hidden="true">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Session {{ this.modalSessionTitle }} </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped" v-if="this.modalSessionData.length > 0">
                        <tbody>
                        <tr v-for="sessionData in modalSessionData">
                            <td v-if="this.viewAdmin">
                                <a :href="'/admin/conference-session/' + sessionData.conference_session_id" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-up-right-circle"></i></a>
                            </td>
                            <td>{{ sessionData.conference_session.name }}</td>
                            <td class="border-start">{{ sessionData.conference_session.track_name }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div v-else class="text-center">
                        <h5><i class="bi bi-exclamation-diamond"></i > No sessions found</h5>

                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <Pagination :data="modalSessionlaravelData" :limit="3" :show-disabled="false" @pagination-change-page="getModalSessionData" />

                </div>
            </div>
        </div>
    </div>
</template>


<script>
import moment from 'moment';

export default {
    props: ['conferenceId'],
    data: function () {
        return {
            data: {},
            type_id: 1,
            sort: 'last_name',
            desc: false,
            modalSessionData: {},
            modalSessionTitle: '',
            modalSessionlaravelData: {},

        }
    },
    mounted() {
        this.loadReport();

    },
    methods: {
        loadReport: function() {
            axios.get(`/api/admin/report/potential-panelist-list/${this.conferenceId}`, { params: { type_id: this.type_id, sort: this.sort, desc: this.desc } })
                .then((response) => {
                    this.data = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the report`);
                });
        },
        sortList: function(sortVal) {
            if(this.sort == sortVal) {
                this.desc = !this.desc;
            } else {
                this.sort = sortVal;
                this.desc = false;
            }
            this.loadReport();
        },
        getModalSessionData: function(user, page = 1, cat = null) {
            this.modalSessionTitle = cat ? cat : this.modalSessionTitle;
            this.modalSessionData = {};
            axios.get(`/api/user/${user}/sessions/${this.modalSessionTitle}`, { params: { page: page }})
                .then((response) => {
                    this.modalSessionData = response.data.data;
                    this.modalSessionlaravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the user's session info`);
                });
        },
    },
}
</script>
