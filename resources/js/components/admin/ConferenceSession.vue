<template>
    <div class="container rounded bg-white mt-5 mb-5 py-3">
        <div class="row" v-if="foundSession">
            <form class="needs-validation mb-5" novalidate  @submit.prevent="addUpdateSession">
            <div class="col-12">
                <div class="py-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="text-right">Session info</h2>
                            <div v-if="session.conference_schedule.length" class="d-flex justify-content-start p-2">
                                <div class="h4 align-self-center mb-lg-0">Scheduled Times:</div>
                                <a v-for="schedule in session.conference_schedule" class="btn btn-outline-dark ms-2" href="/admin/schedule-board" target="_blank"><i class="bi bi-calendar-check"></i> {{ schedule.date }} {{ schedule.time }}</a>
                            </div>
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
                                <option value="null" disabled hidden selected>Select a track</option>
                                <option v-for="track in tracks" v-bind:value="track.id">{{ track.name }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="type">Type</label>
                            <select id="type" v-model="session.type_id" class="form-select" aria-label="select category" required>
                                <option value="null" disabled hidden selected>Select a type</option>
                                <option v-for="type in types" v-bind:value="type.id">{{ type.name }}</option>
                            </select>
                            <div class="invalid-feedback">
                                Please provide a session type
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="status">Status</label>
                            <select id="status" v-model="session.session_status_id" class="form-select" aria-label="select category" required>
                                <option value="null" disabled hidden selected>Select a status</option>
                                <option v-for="status in statuses" v-bind:value="status.id">{{ status.status }}</option>
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
                            <label for="staff_notes">Staff notes <span class="text-secondary">(private)</span></label>
                            <textarea id="staff_notes" class="form-control" placeholder="General notes about the session (staff view only)"  v-model="session.staff_notes" rows="4" ></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="duration_minutes">Duration <span class="text-secondary">(minutes)</span></label>
                            <input type="text" class="form-control" id="duration_minutes"  v-model="session.duration_minutes" required>
                        </div>
                        <div class="col-md-2">
                            <label for="special_equipment">Special Equipment Request</label>
                            <input type="text" class="form-control" id="special_equipment"  v-model="session.special_equipment">
                        </div>
                        <div class="col-md-2">
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
                    <div class="mt-3 text-center">
                        <button class="btn btn-outline-secondary profile-button me-2" type="button" @click="loadSession">Reset</button>
                        <button class="btn btn-primary profile-button" type="submit">Save Session</button>
                    </div>
                </div>
            </div>
            </form>
            <div class="container mb-2" v-if="session.id">
                <div class="d-flex justify-content-start p-2">
                    <div class="h4 align-self-center mb-lg-0">Scheduled Times:</div>
                    <a v-for="schedule in session.conference_schedule" class="btn btn-outline-dark ms-2" href="/admin/schedule-board" target="_blank"><i class="bi bi-calendar-check"></i> {{ schedule.date }} {{ schedule.time }}</a>
                </div>
            </div>
            <div class="container mb-2" v-if="session.id">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="h4 align-self-center mb-lg-0">Add user to this session</div>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sessionInterestModal"><i class="bi bi-plus-circle"></i> Add new</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <partials-session-participants-list v-if="session.id" :session-id="session.id" :key="participantListKey" @reloadInterests="reloadInterestList" class="mb-2" />
            <partials-session-interest-list v-if="session.id" :session-id="session.id" :key="interestListKey"  @reloadParticipants="reloadParticipantList" />
        </div>
        <div v-else class="p-5 text-center">
            <h1>Error 404</h1>
            Session not found
        </div>
    </div>
    <div class="modal" id="sessionInterestModal" v-if="session.id" >
        <form id="interestForm" class="needs-validation row" novalidate @submit.prevent="">
            <div class="modal-dialog modal-lg" style="min-width: 25%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Panel Interest Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="!interest.user_id" class="container">
                            <div class="input-group flex-nowrap mb-3">
                                <input type="text" class="form-control align-self-center" placeholder="Search name or email" v-model="keyword" v-on:keyup.enter="loadUsers">
                                <button type="button" class="input-group-text" @click="loadUsers"><i class="bi bi-search"></i></button>
                            </div>
                            <div v-for="user in searchUsers" class="mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="selectUser(user)"><i class="bi bi-person-plus-fill"></i></button>
                                <a :href="'/admin/user-profile/'+user.id" target="_blank">#{{ user.id }}</a> {{ user.first_name }} {{ user.last_name }} ({{ user.info.badge_name }})
                            </div>
                        </div>
                        <div v-else class="container">
                            <div class="mb-3">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-secondary me-2" @click="clearUser()"><i class="bi bi-person-x-fill"></i></button>
                                    {{ interest.user.first_name }} {{ interest.user.last_name }} ({{ interest.user.info.badge_name }})
                                </div>

                                <label for="interest_level">Level of interest</label>
                                <select class="form-select" v-model="interest.interest_level" id="interest_level" required>
                                    <option value="5" selected>Extremely Interested</option>
                                    <option value="4">Very Interested</option>
                                    <option value="3">Interested</option>
                                    <option value="2">Somewhat Interested</option>
                                    <option value="1">Mildly Interested</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose an interest level.
                                </div>
                                <small class="text-muted">How interested are they in being on this panel?</small>
                            </div>
                            <div class="mb-3">
                                <label for="experience_level">Level of experience</label>
                                <select class="form-select" v-model="interest.experience_level" id="experience_level" required>
                                    <option value="5" selected>Expert Knowledge</option>
                                    <option value="4">Extremely Knowledgeable</option>
                                    <option value="3">Very Knowledgeable</option>
                                    <option value="2">Somewhat Knowledgeable</option>
                                    <option value="1">Mildly Knowledgeable</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose an experience level.
                                </div>
                                <small class="text-muted">How familiar are they with this topic?</small>
                            </div>
                            <div class="mb-3">
                                <label for="panel_role">Role on this panel</label>
                                <select class="form-select" v-model="interest.panel_role" id="panel_role" required>
                                    <option value="5" selected>Creator</option>
                                    <option value="4">Critic</option>
                                    <option value="3">Educator</option>
                                    <option value="2">Expert</option>
                                    <option value="1">Fan</option>
                                    <option value="6">Other</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please indicate the potential role.
                                </div>
                                <small class="text-muted">In what type of role would they contribute?</small>
                            </div>
                            <div class="mb-3">
                                <label for="will_moderate">Interest in moderating</label>
                                <select class="form-select" v-model="interest.will_moderate" id="will_moderate" required>
                                    <option value="1" selected>Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please indicate their interest in moderating.
                                </div>
                                <small class="text-muted">Are they willing to moderate this panel?</small>
                            </div>
                            <div class="mb-3">
                                <label for="will_moderate">Add as a participant?</label>
                                <select class="form-select" v-model="interest.is_participant" id="is_participant" required>
                                    <option value="1" selected>Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please indicate their status.
                                </div>
                                <small class="text-muted">Are they being added directly to the panel?</small>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button v-if="interest.user_id" type="submit" class="btn btn-primary" @click="addInterest">Add User</button>
                    </div>
                </div>
            </div>
        </form>
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
                    'conference_schedule': [],
                },
                types: [],
                statuses: [],
                foundSession: true,
                participantListKey: 0,
                interestListKey: 0,
                interest: {
                  interest_level: 5,
                  experience_level: 5,
                  is_participant: 1,
                  will_moderate: 1,
                  panel_role: 5,
                },
                searchUsers: {},
                totalUsers: 0,
                keyword: '',
            }
        },
        mounted() {
            this.loadTracks();
            this.loadStatuses();
            this.loadTypes();
            this.loadSession();
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
            loadStatuses: function () {
                axios.get('/api/admin/session-status')
                    .then((response) => {
                        this.statuses = response.data.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the session statuses`);
                    });
            },
            loadTypes: function () {
                axios.get('/api/type-list')
                    .then((response) => {
                        this.types = response.data.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the session types`);
                    });
            },
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
                        this.foundSession = false
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
            loadUsers: function (page = 1) {
                axios.get('/api/profile/user', { params: { keyword: this.keyword, page: page }})
                    .then((response) => {
                        this.totalUsers = response.data.meta.total;
                        this.searchUsers = response.data.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the users`);
                    });
            },
            selectUser: function(user) {
                this.interest.user_id = user.id;
                this.interest.user = user;
            },
            clearUser: function() {
                this.interest.user_id = null;
                this.interest.user = null;
            },
            addInterest: function() {
                let form = document.querySelector("#interestForm");
                if (!form.checkValidity()) {
                    form.classList.add('was-validated')
                    return;
                }
                this.interest.conference_session_id = this.session.id;

                axios.post('/api/admin/session-interest', this.interest)
                    .then((response) => {
                        if(this.interest.is_participant == 1) {
                            this.reloadParticipantList();
                        } else {
                            this.reloadInterestList();
                        }

                        this.$toast.success(`Saved a new panel interest`);
                        this.interest = {};
                        this.$refs.Close.click();

                    })
                    .catch((error) => {
                        if(error.response.data.message) {
                            this.$toast.error(error.response.data.message);
                        } else {
                            this.$toast.error(`Could not save your participant`);
                        }
                    });
            },
            resetDefaults: function() {
                for (var key in this.session ) {
                    this.session[key] = null;
                }
                this.session.registration_required = 0;
                this.session.duration_minutes = this.defaultSessionDuration;
                this.session.session_status_id = 1;
                this.session.conference_schedule = [];
            },
            reloadInterestList: function() {
                this.interestListKey += 1;
            },
            reloadParticipantList: function() {
                this.participantListKey += 1;
            },
        }

    }
</script>
