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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetTrack"></button>
                </div>
                <div class="modal-body">
                    <label for="choose-type">Choose a type</label>
                    <select id="choose-type" v-model="typeId" class="form-select mb-2" aria-label="select category" @change="loadSessions">
                        <option value="">All Types</option>
                        <option v-for="type in types" v-bind:value="type.id">{{ type.name }}</option>
                    </select>
                    <label for="choose-track">Choose a track</label>
                    <select v-model="trackId" class="form-select mb-2" id="choose-track" @change="loadSessions">
                        <option v-for="track in tracks" :value="track.id">{{ track.name }}</option>
                        <option value="0" class="">No Track</option>
                    </select>
                    <label for="choose-track">Choose a session</label>
                    <select v-model="sessionId" class="form-select mb-2" id="choose-track" @change="loadSessions">
                        <option value="0" class="">No Session</option>
                        <option v-for="session in sessions" :value="session.id">{{ session.name }}</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="resetTrack">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="addSession" data-bs-dismiss="modal">Save to schedule</button>
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
            tracks: {},
            sessions: {},
            types: {},
            trackId: 0,
            sessionId: 0,
            typeId: '',

        }
    },
    mounted() {
        this.loadScheduleBoard(this.conferenceStartDate);
        this.loadTypes();
        this.loadTracks();

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
        loadTypes: function () {
            axios.get('/api/admin/session-type')
                .then((response) => {
                    this.types = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the session types`);
                });
        },
        loadTracks: function () {
            axios.get('/api/admin/track')
                .then((response) => {
                    console.log(response.data.data);
                    this.tracks = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the tracks`);
                });
        },
        loadSessions: function() {
            if(!this.trackId) {
                this.sessions = {};
                return;
            }
            axios.get('/api/admin/conference-session', {
                params: {
                    conference_id: this.conferenceId,
                    track_id: this.trackId,
                    type_id: this.typeId,
                    all_statuses: '',
                }
            })
                .then((response) => {
                    console.log( response.data.data);
                    this.sessions = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the sessions`);
                });
        },
        populateSessionModal: function (room, time, room_name) {

        },
        resetTrack: function() {
            this.trackId = 0;
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
