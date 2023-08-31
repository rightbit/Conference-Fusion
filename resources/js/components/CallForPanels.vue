<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="text-end"><a href="/home"><i class="bi bi-arrow-left-circle"></i> Back home</a></div>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Call for Panelists <span v-if="singleSession">- single session</span></div>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                Found {{ totalSessions }} Panels
                            </div>
                            <div v-if="!singleSession">
                                <div class="d-flex align-items-end input-group input-group-sm ">
                                    <div class="input-group-text bg-warning"><i class="bi bi-filter-right">&nbsp;</i> Filter Panels</div>
                                    <select class="form-select form-select-sm me-2" v-model="filter" v-on:change="loadSessions"  aria-label="select track filter">
                                        <optgroup label="General">
                                            <option value="" selected>Show all</option>
                                            <option value="own">Only my interests</option>
                                        </optgroup>
                                        <optgroup label="Tracks">
                                            <option v-for="track in tracks" v-bind:value="track.id">{{ track.name }}</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning d-flex" role="alert" v-if="sessionCounts.interest > 15">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <div>Please limit your presentation submissions and panel interest to 30 or less. You are currently have {{ sessionCounts.interest }} submissions.</div>
                        </div>
                        <table class="table table-striped table-sm fs-90">
                            <thead>
                            <tr>
                                <th class="ps-2">Interested</th>
                                <th class="ps-2 text-center">Track</th>
                                <th class="ps-2 text-center"> Day/Time </th>
                                <th class="ps-2">Name</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="(panel, key) in conferenceSessions">
                                <td class="ps-2">
                                    <button v-if="panel.user_session_interest" class="btn btn-warning btn-sm" v-on:click="setPanelInfo(panel, key)" data-bs-toggle="modal" data-bs-target="#panelInterestModal"><i class="bi bi-star-fill"></i> Edit</button>
                                    <button v-else class="btn btn-outline-success btn-sm" v-on:click="setPanelInfo(panel, key)" data-bs-toggle="modal" data-bs-target="#panelInterestModal"><i class="bi bi-bookmark-plus-fill"></i> Add</button>
                                </td>
                                <td class="ps-2 text-center"><span class="badge bg-secondary">{{ panel.track }}</span></td>
                                <td class="ps-2 text-center" v-html="formatDate(panel.proposed_date)"></td>
                                <td class="ps-2">{{ panel.name }}</td>
                                <td>{{ truncate(panel.description , 300, '...') }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="singleSession" class="card-footer d-flex justify-content-center">
                            <a :href="'/call-for-panelists/' + conferenceId" class="btn btn-primary"><i class="bi bi-arrow-counterclockwise"></i> View complete panel list</a>
                    </div>
                    <div v-else class="card-footer d-flex justify-content-center">
                        <Pagination :data="laravelData" :limit="3" :show-disabled="false" @pagination-change-page="loadSessions" />
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal" id="panelInterestModal" >
        <form id="interestForm" class="needs-validation row" novalidate @submit.prevent="addUpdateInterest">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Panel Interest Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="alert alert-warning d-flex" role="alert" v-if="sessionCounts.interest > 15">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <div>Please limit your presentation submissions and panel interest to 30 or less. You are currently have {{ sessionCounts.interest }} submissions.</div>
                        </div>
                        <h5>Title:</h5>
                        <p>{{ this.panelInfo.name }}</p>

                        <h5>Description:</h5>
                        <p style="white-space: pre-line">{{ this.panelInfo.description }}</p>
                        <div class="mb-3">
                            <label for="interest_level">My level of interest</label>
                            <select class="form-select" v-model="interest.interest_level" id="interest_level" required>
                                <option value=""></option>
                                <option value="5">Extremely Interested</option>
                                <option value="4">Very Interested</option>
                                <option value="3">Interested</option>
                                <option value="2">Somewhat Interested</option>
                                <option value="1">Mildly Interested</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose an interest level.
                            </div>
                            <small class="text-muted">How interested are you in being on this panel?</small>
                        </div>
                        <div class="mb-3">
                            <label for="experience_level">My level of experience</label>
                            <select class="form-select" v-model="interest.experience_level" id="experience_level" required>
                                <option value=""></option>
                                <option value="5">Expert Knowledge</option>
                                <option value="4">Extremely Knowledgeable</option>
                                <option value="3">Very Knowledgeable</option>
                                <option value="2">Somewhat Knowledgeable</option>
                                <option value="1">Mildly Knowledgeable</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose an experience level.
                            </div>
                            <small class="text-muted">How familiar are you with this topic?</small>
                        </div>
                        <div class="mb-3">
                            <label for="panel_role">My role on this panel</label>
                            <select class="form-select" v-model="interest.panel_role" id="panel_role" required>
                                <option value=""></option>
                                <option value="5">Creator</option>
                                <option value="4">Critic</option>
                                <option value="3">Educator</option>
                                <option value="2">Expert</option>
                                <option value="1">Fan</option>
                                <option value="6">Other</option>
                            </select>
                            <div class="invalid-feedback">
                                Please indicate your potential role.
                            </div>
                            <small class="text-muted">In what type of role would you contribute?</small>
                        </div>
                        <div class="mb-3">
                            <label for="will_moderate">Interest in moderating</label>
                            <select class="form-select" v-model="interest.will_moderate" id="will_moderate" required>
                                <option value=""></option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <div class="invalid-feedback">
                                Please indicate your interest in moderating.
                            </div>
                            <small class="text-muted">Are you willing to moderate this panel?</small>
                        </div>
                        <div class="mb-3">
                            <label for="comments" class="form-label">Comments and notes about this panel</label>

                            <textarea class="form-control" v-model="interest.notes" id="comments" rows="3" required></textarea>
                            <div class="invalid-feedback">
                                Please fill out the comments field.
                            </div>
                            <small class="text-muted">Tell us why you would make a good contributor to this panel. Please be as detailed as possible! </small>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" v-if="interest.id" @click="deleteSession(interest)" class="btn btn-danger" style="margin-right:auto"><i class="bi bi-trash-fill"></i> Remove Interest</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button v-if="interest.id" type="submit" class="btn btn-primary">Update my interest</button>
                    <button v-else-if="sessionCounts.interest < 30" type="submit" class="btn btn-primary">Submit my interest</button>
                    <button v-else type="submit" class="btn btn-secondary" disabled><i class="bi bi-exclamation-circle-fill"></i> Submit my interest</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</template>


<script>
import dayjs from 'dayjs';

export default {
    props: ['conferenceId', 'userId', 'singleSession'],
    data: function() {
        return {
            totalSessions: 0,
            conferenceSessions: [],
            interest: {},
            panelInfo: {},
            tracks: {},
            filter: '',
            laravelData: {},
            sessionCounts: {
                interest: 0,
                panelist: 0,
                presenter: 0,
            },
        }
    },
    mounted() {
        this.loadTracks();
        this.loadSessions();
        this.getUserSessionTotals();
    },
    methods: {
        formatDate(dateString) {
            if(!dateString) { return null }
            const date = dayjs(dateString);
            return date.format('MMM DD, <br /> h A');
        },
        loadTracks: function () {
            axios.get('/api/track-list', { params: {call_included: 1}})
                .then((response) => {
                    this.tracks = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the tracks`);
                });
        },
        loadSessions: function (page = 1, filter = this.filter) {
            if (this.singleSession) {
                // Find a single session, add it to the array and pop up the modal
                axios.get(`/api/user-panel-list/${this.conferenceId}`, {
                    params: {
                        page: page,
                        single_session: this.singleSession,
                        call_included: 1
                    }
                })
                    .then((response) => {
                        this.conferenceSessions = response.data.data;
                        this.totalSessions = response.data.meta.total;
                        this.laravelData = response.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the single session you requested`);
                    });

            } else {
                axios.get(`/api/user-panel-list/${this.conferenceId}`, {
                    params: {
                        page: page,
                        filter: filter,
                        call_included: 1
                    }
                })
                    .then((response) => {
                        this.conferenceSessions = response.data.data;
                        this.totalSessions = response.data.meta.total;
                        this.laravelData = response.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the panels for this conference`);
                    });
            }



        },
        getUserSessionTotals: function() {
            axios.get(`/api/user-session-totals/${this.userId}`)
                .then((response) => {
                    this.sessionCounts = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Error loading the session counts`);
                });
        },
        setPanelInfo: function (panel, key) {
            this.panelInfo = panel;
            this.panelInfo.array_key = key;
            if(panel.user_session_interest) {
                this.interest = panel.user_session_interest;
            } else {
                this.interest = {};
            }
        },
        addUpdateInterest: function() {
            let form = document.querySelector("#interestForm");
            if (!form.checkValidity()) {
                form.classList.add('was-validated')
                return;
            }
            this.interest.conference_session_id = this.panelInfo.id;
            if(this.interest.id) {
                axios.post(`/api/panels/interest/update/${this.interest.id}`, this.interest)
                    .then((response) => {
                        this.conferenceSessions[this.panelInfo.array_key].user_session_interest = this.interest;
                        this.$toast.success(`Updated your panel interest`);
                        this.interest = {};
                        this.$refs.Close.click();

                    })
                    .catch((error) => {
                        this.$toast.error(`Could not update your panel request`);
                    });
            } else {
                axios.post('/api/panels/interest/submit', this.interest)
                    .then((response) => {
                        this.interest.id = response.data.data.id;
                        this.conferenceSessions[this.panelInfo.array_key].user_session_interest = this.interest;
                        this.$toast.success(`Saved a new panel interest`);
                        this.interest = {};
                        this.$refs.Close.click();
                        this.getUserSessionTotals();

                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save your panel request`);
                    });
            }

        },
        deleteSession: function(interest) {
            if(confirm("Do you really want to remove your interest in this panel?")) {
                axios.delete(`/api/panels/interest/${interest.id}`)
                    .then((response) => {
                        this.$toast.success(`Panel interest deleted`);
                        this.interest = {};
                        this.conferenceSessions[this.panelInfo.array_key].user_session_interest = null;
                        this.$refs.Close.click();
                        this.getUserSessionTotals();
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not delete panel interest`);
                    });
            }
        },
        truncate: function (text, length, suffix) {
            if (text && text.length > length) {
                return text.substring(0, length) + suffix;
            } else {
                return text;
            }
        },
    }
}
</script>

