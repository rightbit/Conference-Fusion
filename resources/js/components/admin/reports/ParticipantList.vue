<style>
.table-free-width td, th {
    min-width: 10px;
}
</style>

<template>
    <div class="">
        <h2>Participant Schedule List</h2>
        <h6>Alphabetical list of participants on the schedule including sessions and errors</h6>
        {{ data.length }} participants found

        <table class="table table-scrollable table-free-width table-sm table-striped bg-white">
            <thead style="z-index: 500">
            <tr class="bg-secondary text-white">
                <td class=""></td>
                <td class="ts-sc w-50">Name</td>
                <td class="ts-sc">Email</td>
                <td class="ts-sc text-center">Share Email</td>
                <td class="ts-sc text-center">Can Record </td>
                <td class="ts-sc text-center">Staff Notes</td>
            </tr>
            </thead>
            <tbody v-for="d in data">
            <tr>
                <td><button v-if="d.errors"
                            class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#user-errors-modal"
                            @click="populateErrorModal(d)"
                >
                <i  class="bi bi-exclamation-octagon-fill "></i></button></td>
                <td class="w-50">
                    <a :href="`/admin/user-profile/${d.user_id}`" target="_blank" class="text-decoration-none text-body fw-bold">
                        {{ d.last_name }}, {{ d.first_name }} ({{d.badge_name}}) <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </td>
                <td class="text-nowrap">{{ d.email }}</td>
                <td :class="[d.share_email_permission ? 'text-center':'text-center bg-danger']"><span :class="[d.share_email_permission ? '':'text-white fw-bold']">{{ d.share_email_permission ? "Y":"N" }}</span></td>
                <td :class="[d.recording_permission ? 'text-center':'text-center bg-danger']"><span :class="[d.recording_permission ? '':'text-white fw-bold']">{{ d.recording_permission ? "Y":"N" }}</span></td>
                <td :class="[d.staff_notes ? 'text-center bg-warning':'']"><i v-if="d.staff_notes" class="bi-journal-bookmark-fill" ></i></td>
            </tr>
            <tr>
                <td colspan="6">
                    <table class="table table-sm table-borderless table-striped w-100">
                        <tr v-for="session in d.sessions">
                            <td class="text-end p-0"><i v-if="session.session_user_staff_notes" class="bi-journal-bookmark-fill text-warning" ></i><i v-if="session.is_moderator" class="bi bi-person-circle"></i></td>
                            <td class="w-50">
                                <a :href="`/admin/conference-session/${session.session_id}`" target="_blank">
                                    {{ session.session_name }}
                                </a>
                            </td>
                            <td class="text-nowrap w-auto">{{ formattedDate(session.date) }} {{ session.time}}</td>
                            <td class="text-nowrap w-auto">{{ session.room_name }} ({{ session.capacity }}<i v-if="session.has_av" class="bi bi-person-video3"></i>)</td>
                            <td class="text-nowrap w-auto">{{ session.session_type}}</td>
                            <td class="text-nowrap w-auto text-end">{{ session.track_name}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="modal" id="user-errors-modal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <a :href="`/admin/user-profile/${userErrors.user_id}`" target="_blank" class="text-decoration-none text-body fw-bold">
                            {{ userErrors.badge_name }} <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetModal"></button>
                </div>
                <div class="modal-body">
                    Session Errors:
                    <ul>
                        <li v-for="error in userErrors.errors">{{ error }}</li>
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
            userErrors: {},
        }
    },
    emits: ['queryParams'],
    mounted() {
        this.handleReturnParams();

    },
    methods: {
        handleReturnParams: function() {
            //Placeholder for future filters
        },
        formattedDate: function (date) {
            return moment(date).format('MM-DD');
        },
        populateErrorModal: function (user) {
            this.userErrors = user;
        },
        resetModal: function() {
            this.userErrors = {};
        },
    },
}
</script>
