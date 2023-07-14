<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Submit a new presentation</div>

                    </div>
                    <div class="card-body" id="addinfo">
                        <form id="newsession" class="row g-3 align-items-center" @submit.prevent="addSession">
                            <div class="col-md-3">
                                <select id="inputCategory" v-model="new_session.track_id" class="form-select" aria-label="select category">
                                    <option value="" disabled hidden selected>Track (optional)</option>
                                    <option v-for="track in tracks" v-bind:value="track.id">{{ track.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-9">
                                <input type="text" v-model="new_session.name" class="form-control" id="name" placeholder="Title" required>
                            </div>
                            <div class="col-md-6">
                                <textarea  v-model="new_session.description" class="form-control" id="description" placeholder="Description (required for the conference program)"></textarea>
                            </div>
                            <div class="col-md-6">
                                <textarea  v-model="new_session.participant_notes" class="form-control" id="participant_notes" placeholder="Other notes about you or the presentation to give to the staff (optional, but recommended)"></textarea>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary " ><i class="bi bi-arrow-up-circle me-2"></i>Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between bg-warning">
                        <div class="h4 align-self-center mb-lg-0">Your presentation submissions</div>

                    </div>

                    <div class="card-body">
                        <table class="table table-striped table-sm fs-90">
                            <thead>
                            <tr>
                                <th class="ps-2">Name</th>
                                <th>Description</th>
                                <th class="m-0 p-0"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="presentation in conferenceSessions">
                                <td class="ps-2">{{ presentation.conference_session.name }}</td>
                                <td>{{ presentation.conference_session.description }}</td>
                                <td class="m-0 pe-1"><button class="btn btn-danger btn-sm" @click="deleteSession(presentation)"><i class="bi bi-trash-fill"></i></button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>


<script>
export default {
    props: ['conferenceId'],
    data: function() {
        return {
            conferenceSessions: [],
            new_session: {
                conference_id: '',
                track_id: '',
                name: '',
                description: '',
                participant_notes: '',
            },
            tracks: [],
            equipment: [],
        }
    },
    mounted() {
        this.loadSessions();
        this.loadTracks();
        this.loadEquipment();
    },
    methods: {
        loadTracks: function () {
            axios.get('/api/track-list')
                .then((response) => {
                    this.tracks = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the tracks`);
                });
        },
        loadEquipment: function () {
            axios.get('/api/session-equipment-list')
                .then((response) => {
                    this.equipment = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the special equipment`);
                });
        },
        loadSessions: function () {
            axios.get(`/api/user-presentation-list/${this.conferenceId}`)
                .then((response) => {
                    // this.totalSessions = response.data.meta.total;
                    this.conferenceSessions = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find your submissions`);
                });
        },
        addSession: function() {
            this.new_session.conference_id = this.conferenceId;
            this.new_session.session_status_id = 3; //"User Suggested"
            axios.post('/api/presentations/submit', this.new_session)
                .then((response) => {
                    this.$toast.success(`New presentation added`);
                    this.loadSessions();
                    this.new_session.track_id = '';
                    this.new_session.name = '';
                    this.new_session.description = '';
                    this.new_session.participant_notes = '';
                })
                .catch((error) => {
                    this.$toast.error(`Could not save the session`);
                });
        },
        deleteSession: function(presentation) {
            if(confirm("Do you really want to delete this presentation?")) {
                axios.delete(`/api/presentations/${presentation.conference_session.id}`)
                    .then((response) => {
                        this.$toast.success(`Presentation succesfully deleted`);
                        this.loadSessions();
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not delete presentation`);
                    });
            }
        }
    }
}
</script>

