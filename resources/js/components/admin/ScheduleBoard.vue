<template>
    <div class="container">
        <div class="table-scrollable-container">
            <h4 class="position-sticky" style="left:0px;">Schedule Board</h4>
            <table class="table table-scrollable table-striped bg-white">
                <thead class="bg-secondary text-white">
                    <tr  class="d-flex align-content-stretch">
                        <td class="bg-secondary text-white">Time</td>
                        <td v-for="room in this.schedule.rooms" class="text-center ts-sc">
                            {{ room.name }}
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(rooms, time) in this.schedule.timeslots" class="d-flex align-content-stretch">
                        <td class="bg-secondary text-white">{{ time }}</td>
                        <td v-for="(room, room_name) in rooms" class="p-1">
                            <div class="card h-100">
                                <div v-if="room.session_name" class="card-body lh-1 p-2">
                                    <a href="#" class="btn stretched-link lh-1 text-start p-0">
                                        <small>{{ room.session_name }}</small>
                                    </a>
                                </div>
                                <div v-else class="card-body text-center">
                                    <button class="btn btn-sm btn-outline-secondary text-nowrap"
                                            data-bs-toggle="modal"
                                            data-bs-target="#session-schedule-modal"
                                            @click="populateSessionModal(room, time, room_name)">
                                        <i class="bi bi-plus-circle "></i> Add
                                    </button>
                                </div>
                                <div v-if="room.track_name" class="card-footer lh-1 text-center">
                                    <small><b>{{ room.track_name }}</b></small>
                                </div>

                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal" id="session-schedule-modal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule a session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" v-on:click="addSession" data-bs-dismiss="modal">Assign session</button>
                </div>
            </div>
        </div>
    </div>
</template>



<script>
export default {
    props: ['conferenceId', 'conferenceName', 'conferenceStartDate', 'conferenceEndDate', 'canEdit'],
    data: function() {
        return {
            schedule: {},
            room_session: {},
        }
    },
    mounted() {
        this.loadScheduleBoard(this.conferenceStartDate);
    },
    methods: {
        loadScheduleBoard: function (date) {
            axios.get('/api/admin/conference-schedule', {
                params: {
                    conference_id: this.conferenceId,
                    date: date,
                }
            })
            .then((response) => {
                this.schedule = response.data.data;
                console.log(this.schedule);
            })
            .catch((error) => {
                this.$toast.error(`Could not find the schedule`);
            });

        },
        populateSessionModal: function (room, time, room_name) {

        },
        addSession: function () {
            this.schedule.timeslots[time][room_name] = {
                conference_id: 1,
                date: "2023-02-16",
                id: 2,
                room_id: 1,
                session_id: 71,
                session_name: "Gamification for Creators - What Video Games Teach Us about Motivation",
                time: "09:00",
                track_id: 1,
                track_name: "Gaming",
            }
        },
        truncate: function (text, length, suffix) {
            if (text && text.length > length) {
                return text.substring(0, length) + suffix;
            } else {
                return text;
            }
        },

    },
}
</script>
