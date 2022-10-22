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
        <select v-model="trackId" class="form-select w-25 mb-2 me-2 float-end" @change="changeTrack">
            <option value="0">All Tracks</option>
            <option v-for="track in tracks" :value="track.id">{{ track.name }}</option>
        </select>
        <table class="table table-scrollable table-free-width table-sm table-striped bg-white">
            <thead style="z-index: 500">
            <tr class="bg-secondary text-white">
                <td class=""></td>
                <td class="ts-sc w-100">Session Name</td>
                <td class="ts-sc text-center">Track</td>
                <td class="ts-sc text-center">Type</td>
                <td class="ts-sc text-center">Date & Time </td>
                <td class="ts-sc text-center">Room</td>
                <td class="ts-sc w-auto pe-2">Status</td>
            </tr>
            </thead>
            <tbody v-for="d in data">
            <tr>
                <td><button v-if="d.errors"
                            class="btn btn-sm btn-danger"
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
                <td class="text-nowrap">{{ formattedDate(d.date) }} {{ d.time }}</td>
                <td class="text-nowrap  text-center">{{ d.room_name }} {{ d.capacity }} <i v-if="d.has_av" class="bi bi-person-video3"></i></td>
                <td class="text-center"><i :class="[goodStatuses.includes(d.status_id) ? 'bi-check-square text-success':'bi-exclamation-triangle-fill text-warning']" ></i></td>
            </tr>
            <tr>
                <td colspan="7">
                    <table class="table table-sm table-borderless table-striped w-100">
                        <tr v-for="p in d.sublist">
                            <td class="ps-4"><i v-if="p.is_moderator" class="bi bi-person-circle"></i></td>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="resetModal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import moment from 'moment';

export default {
    props: ['data', 'returnQueryParams'],
    data: function () {
        return {
            tracks: {},
            trackId: 0,
            goodStatuses: [4,5],
            sessionErrors: {},
        }
    },
    emits: ['queryParams'],
    mounted() {
        this.loadTracks();
        this.handleReturnParams();

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
        handleReturnParams: function() {
            if(this.returnQueryParams.track_id) {
                this.trackId = this.returnQueryParams.track_id;
            }

        },
        formattedDate: function (date) {
            return moment(date).format('MM-DD');
        },
        populateErrorModal: function (session) {
            this.sessionErrors = session;
        },
        resetModal: function() {
            this.sessionErrors = {};
        },
        changeTrack: function() {
            this.$emit("queryParams", {0:{name: 'track_id', value: this.trackId}});
        }
    },
}
</script>
