<style>
.table-free-width td, th {
    min-width: 10px;
}
</style>
<template>
    <div class="">
        <h2>Schedule List</h2>
        <h6>Alphabetical list of sessions on the schedule including participants and errors</h6>
        {{ data.length }} sessions found
        <select v-model="trackId" class="form-select w-25 mb-2 me-2 float-end" @change="loadReport">
            <option value="0">All Tracks</option>
            <option v-for="track in tracks" :value="track.id">{{ track.name }}</option>
        </select>
        <button class="btn btn-sm btn-link float-end" @click="loadReport"><b><i class="bi bi-arrow-repeat"></i> Reload</b></button>
        <table class="table table-scrollable table-free-width table-sm table-striped bg-white">
            <thead style="z-index: 500">
            <tr class="bg-secondary text-white">
                <td class="ts-sc"><button class="btn btn-sm btn-link" @click="scrollToTop"><i class="bi bi-chevron-bar-up"></i></button></td>
                <td class="ts-sc w-100">Session Name</td>
                <td class="ts-sc text-center">Track</td>
                <td class="ts-sc text-center">Type</td>
                <td class="ts-sc text-center">Date & Time </td>
                <td class="ts-sc text-center">Room</td>
                <td class="ts-sc w-auto pe-2">Status</td>
                <td class="ts-sc w-auto pe-2"></td>
            </tr>
            </thead>
            <tbody v-for="d in data">
            <tr>
                <td><button v-if="d.errors"
                            ref="errors"
                            :class="['btn btn-sm', d.ignore_errors ? 'btn-warning':'btn-danger']"
                            data-bs-toggle="modal"
                            data-bs-target="#session-errors-modal"
                            @click="populateErrorModal(d)"
                >
                <i  class="bi bi-exclamation-octagon-fill "></i></button></td>
                <td class="w-100">
                    <a :href="`/admin/conference-session/${d.session_id}`" target="_blank" class="text-decoration-none text-body fw-bold">
                        {{ d.session_name }} <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </td>
                <td class="text-nowrap text-center">{{ d.track_name }}</td>
                <td class="text-center">{{ d.session_type }}</td>
                <td class="text-nowrap text-center">
                    {{ formattedDate(d.date_time[0]) }} <span v-if="d.date_time.length > 1">+{{ d.date_time.length - 1 }} </span>
                </td>
                <td class="text-nowrap  text-center">{{ d.room_name }} {{ d.capacity }} <i v-if="d.has_av" class="bi bi-person-video3"></i></td>
                <td class="text-center"><i :class="[goodStatuses.includes(d.status_id) ? 'bi-check-square text-success':'bi-exclamation-triangle-fill text-warning']" ></i></td>
                <td class="text-center"><button class="btn btn-sm btn-link float-end" @click="loadReport"><b><i class="bi bi-arrow-repeat"></i></b></button></td>
            </tr>
            <tr>
                <td colspan="8">
                    <table class="table table-sm table-borderless table-striped w-100">
                        <tr v-for="p in d.sublist">
                            <td class="ps-4"><i v-if="p.is_moderator" class="bi bi-mic-fill"></i></td>
                            <td class="w-100">
                                <a :href="`/admin/user-profile/${p.user_id}`" target="_blank">
                                    {{ p.badge_name }}
                                </a>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="modal" id="session-errors-modal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <a :href="`/admin/conference-session/${sessionErrors.session_id}`" target="_blank" class="text-decoration-none text-body fw-bold">
                            {{ sessionErrors.session_name }} <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetModal"></button>
                </div>
                <div class="modal-body">
                    Session Errors:
                    <ul>
                        <li v-for="error in sessionErrors.errors">{{ error }}</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal" @click="ignoreErrors(1)">Ignore</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="ignoreErrors(0)">Reset</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="resetModal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import moment from 'moment';
import { ref } from "vue";

const errors = ref([]);

export default {
    props: ['conferenceId'],
    data: function () {
        return {
            data: {},
            tracks: {},
            trackId: 0,
            goodStatuses: [4,5],
            sessionErrors: {},
            errorNumber: 1,

        }
    },
    emits: ['queryParams'],
    mounted() {
        this.loadTracks();
        this.loadReport();

    },
    methods: {
        loadTracks: function () {
            axios.get('/api/admin/track')
                .then((response) => {
                    this.tracks = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the tracks`);
                });
        },
        loadReport: function() {
            axios.get(`/api/admin/report/schedule-list/${this.conferenceId}`, { params: { track_id: this.trackId } })
                .then((response) => {
                    this.data = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the report`);
                });


        },
        ignoreErrors: function(value) {
            this.sessionErrors.ignore_errors = value;
            axios.put(`/api/admin/conference-session/${this.sessionErrors.session_id}/ignore_errors`, {id: this.sessionErrors.session_id, ignore_errors: value})
                .then((response) => {
                    if (value == 1) {
                        this.$toast.success(`Ignoring errors`);
                    }
                })
                .catch((error) => {
                    this.$toast.error(`Could not save the ignore errors setting`);
                });
        },
        formattedDate: function (date) {
            return moment(date).format('MM-DD HH:mm');
        },
        populateErrorModal: function (session) {
            this.sessionErrors = session;
        },
        resetModal: function() {
            this.sessionErrors = {};
        },
        showScrollInto: function() {
            // refs set with a v-for are returned as an array
            errors.value[this.errorNumber].scrollIntoView({ behavior: "smooth" });
            this.errorNumber += 1;
        },
        scrollToTop: function() {
            this.errorNumber = 0;
            window.scrollTo(0,0);
        },
    },
}
</script>
