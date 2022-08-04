<template>
    <div class="container rounded bg-white mt-5 mb-5">
        <div class="row" v-if="allowForm">
            <form class="needs-validation" novalidate  @submit.prevent="addUpdateSession">
            <div class="col-12">
                <div class="py-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="text-right">Session info</h2>
                        <h5 v-if="false">Like this session idea? Vote: <i class="bi bi-hand-thumbs-up-fill text-primary"></i><i class="bi bi-hand-thumbs-down text-secondary"></i> +0(0)</h5>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Session name" v-model="session.name" required>
                            <div class="invalid-feedback">
                                Please provide a name
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="track">Track</label>
                            <select id="track" v-model="session.track_id" class="form-select" aria-label="select category">
                                <partials-track-options />
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="type">Type</label>
                            <select id="type" v-model="session.type_id" class="form-select" aria-label="select category" required>
                                <partials-type-options />
                            </select>
                            <div class="invalid-feedback">
                                Please provide a session type
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="status">Status</label>
                            <select id="status" v-model="session.session_status_id" class="form-select" aria-label="select category" required>
                                <partials-session-status-options />
                            </select>
                            <div class="invalid-feedback">
                                Please provide a session status
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="description">Description</label>
                            <textarea id="description" class="form-control" placeholder="Description for the program" v-model="session.description" rows="4" ></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="description">Participant Notes</label>
                            <textarea id="participant_notes" class="form-control" placeholder="Notes from the presenters" v-model="session.participant_notes" rows="4" ></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="seed_questions">Prompts and Seed Questions</label>
                            <textarea id="seed_questions" class="form-control" placeholder="Seed questions for the moderator"  v-model="session.seed_questions" rows="4" ></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="staff_notes">Staff notes</label>
                            <textarea id="staff_notes" class="form-control" placeholder="General notes about the session (staff view only)"  v-model="session.staff_notes" rows="4" ></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="duration_minutes">Duration (minutes)</label>
                            <input type="text" class="form-control" id="duration_minutes"  v-model="session.duration_minutes" required>
                        </div>
                        <div class="col-md-3">
                            <label for="description">Registration Required</label>
                            <select v-model="session.registration_required" class="form-select" aria-label="select category">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="max_registration">Max Registration</label>
                            <input type="text" class="form-control" id="max_registration"  v-model="session.max_registration" required>
                        </div>
                        <div class="col-md-3">
                            <label for="attendance">Attendance</label>
                            <input type="text" class="form-control" id="attendance"  v-model="session.attendance" required>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <button class="btn btn-outline-secondary profile-button me-2" type="button" @click="loadSession">Reset</button>
                        <button class="btn btn-primary profile-button" type="submit">Save Session</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <div v-else class="p-5 text-center">
            <h1>Error 404</h1>
            Session not found
        </div>
    </div>
</template>


<script>
    export default {
        props: ['sessionId', 'conferenceId', 'sessionListLink', 'defaultSessionDuration'],
        data: function() {
            return {
                session: {
                    'track_id': null,
                    'type_id': null,
                    'registration_required': 0,
                },
                types: [],
                statuses: [],
                allowForm: true
            }
        },
        mounted() {
            this.loadSession();
        },
        methods: {
            loadSession: function() {
                // Don't try to load if a new session
                if(this.sessionId == 0) {
                    this.resetDefaults();
                    return;
                }
                axios.get(`/api/admin/conference-session/${this.sessionId}`)
                    .then((response) => {
                        this.session = response.data.data;
                    })
                    .catch((error) => {
                        this.$toast.show(`Could not find the session. Click here to go back`, {
                            type: 'error',
                            duration: false,
                            onClick: ''
                        });
                        this.allowForm = false
                    });
            },
            addUpdateSession: function() {
                if(this.session.id)
                {
                    axios.put(`/api/admin/conference-session/${this.session.id}`, this.session )
                        .then((response) => {
                            this.$toast.success(`Updated the session successfully`);
                        })
                        .catch((error) => {
                            this.$toast.error(`Could not save the session info`);
                        });
                } else {
                    this.session.conference_id = this.conferenceId;
                    axios.post('/api/admin/conference-session', this.session)
                        .then((response) => {
                            this.session = response.data.data;
                            this.$toast.success(`Created a new session successfully`);
                            window.history.replaceState(null, "", '/admin/conference-session/'+this.session.id );
                        })
                        .catch((error) => {
                            this.$toast.error(`Could not create a new session`);
                        });
                }
            },
            resetDefaults: function() {
                for (var key in this.session ) {
                    this.session[key] = null;
                }
                this.session.registration_required = 0;
                this.session.duration_minutes = this.defaultSessionDuration;
                this.session.session_status_id = 1;
            }
        }

    }
</script>
