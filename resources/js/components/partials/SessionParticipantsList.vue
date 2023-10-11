<template>
    <div className="container mb-2">
        <div className="row justify-content-center">
            <div className="col-md-12">
                <div className="card">
                    <div className="card-header d-flex justify-content-between">
                        <div className="h4 align-self-center mb-lg-0">Session participant list</div>
                    </div>

                    <div className="card-body">
                        <div className="d-flex justify-content-between mb-3">
                            <div>
                                Found {{ totalParticipatingUsers }} Users
                            </div>
                            <div className="w-50" v-if="totalParticipatingUsers > 10">
                                <div className="d-flex align-items-end">
                                    <input type="text" className="form-control form-control-sm align-self-center me-2"
                                           placeholder="Search name" v-model="keyword"
                                           v-on:keyup.enter="loadSessionParticipants">
                                    <button className="btn btn-outline-secondary btn-sm" @click="loadSessionParticipants">
                                        <i className="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table className="table table-borderless table-responsive table-sm fs-90">
                            <thead class="border-bottom border-dark">
                            <tr>
                                <th>Badge name</th>
                                <th className="ps-2">Name</th>
                                <th>Email</th>
                                <th  class="m-0 p-0 text-center">Staff notes</th>
                                <th className="m-0 p-0"></th>
                            </tr>
                            </thead>
                            <tbody v-for="(participant, index) in participatingUsers">
                            <tr :class="participant.is_moderator ? 'bg-light' : ''">
                                <td><button class="btn btn-sm btn-secondary py-0 me-2" @click="this.participantToggle[participant.id] = !this.participantToggle[participant.id]">
                                    <i class="bi bi-person-lines-fill"></i>
                                </button>
                                    <a :href="'/admin/user-profile/' + participant.user.id" target="_blank">{{ participant.user_info.badge_name }}</a>
                                </td>
                                <td className="ps-2">{{ participant.user.first_name }} {{ participant.user.last_name }}</td>
                                <td>{{ participant.user.email }}</td>
                                <td class="m-0 px-0 text-center">
                                    <button class="btn btn-sm"
                                            :class="{'btn-warning': participant.staff_notes, 'btn-secondary': participant.user_info.staff_notes && !participant.staff_notes, 'btn-outline-secondary': !participant.staff_notes && ! participant.user_info.staff_notes}"
                                            type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#participant-staff-notes"
                                            @click="populateStaffNotes(index, participant)">
                                        <i class="bi bi-journal-bookmark-fill"></i>
                                    </button>
                                </td>
                                <td className="m-0 px-0 text-end">
                                    <span v-if="participant.will_moderate">
                                        <button v-if="!participant.is_moderator" class="btn btn-sm btn-outline-primary p-1 me-2" @click="makeModerator(participant.id)">
                                            <i class="bi bi-square"></i> Moderator
                                        </button>
                                        <button v-else class="btn btn-sm btn-primary p-1 me-2 disabled"><i class="bi  bi-check-square"></i> Moderator</button>
                                    </span>
                                    <span v-else>
                                        <button class="btn btn-sm btn-light p-1 me-2 disabled"><i class="bi  bi-x-circle"></i> Moderator</button>
                                    </span>
                                    <button class="btn btn-sm btn-danger p-1" @click="removeFromSession(participant.id)"><i class="bi bi-person-x-fill"></i> Remove</button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="m-0 p-0 border border-top-0">
                                    <div class="d-flex flex-row d-flex justify-content-between ps-2" v-if="this.participantToggle[participant.id]">
                                        <div class="p-2" v-if="participant.panel_role"><strong>Role:</strong><br />{{ this.role[participant.panel_role] }}</div>
                                        <div class="p-2 w-30"><strong>Session notes:</strong><br />{{ participant.notes }}</div>
                                        <div class="p-2 w-30"><strong>Participant bio:</strong><br />{{ participant.user_info.biography }}</div>
                                        <div class="p-2 w-30"><strong>Participant info:</strong><br />{{ participant.user_info.notes }}</div>
                                        <div class="p-2">
                                            <strong>Other sessions:</strong>
                                            <br />
                                            <button class="btn btn-sm btn-outline-secondary mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#participant-session-data" @click="getModalSessionData(participant.user, 1, 'interests')">
                                                <i class="bi bi-eye-fill"></i> Interests
                                            </button>
                                            <br />
                                            <button class="btn btn-sm btn-outline-secondary mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#participant-session-data" @click="getModalSessionData(participant.user, 1, 'panelist')">
                                                <i class="bi bi-eye-fill"></i> Panelist
                                            </button>
                                            <br />
                                            <button class="btn  btn-sm btn-outline-secondary mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#participant-session-data" @click="getModalSessionData(participant.user, 1, 'presenter')">
                                                <i class="bi bi-eye-fill"></i> Presenter
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div className="card-footer d-flex justify-content-center">
                        <Pagination :data="laravelData" :limit="3" :show-disabled="false"
                                    @pagination-change-page="loadSessionParticipants"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="participant-staff-notes" aria-hidden="true">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Staff Notes </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="Close" @click="clearStaffNotes"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h5>Session user staff notes</h5>
                        <textarea v-model="this.userSessionStaffNotes" class="form-control"></textarea>
                    </div>
                    <div v-if="this.userInfoStaffNotes">
                        <h5>Staff notes from profile</h5>
                        {{ this.userInfoStaffNotes }}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" @click="saveStaffNotes()">Save notes</button>
                </div>
            </div>
        </div>
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
export default {
    props: ['sessionId'],
    data: function () {
        return {
            participatingUsers: {},
            totalParticipatingUsers: '0',
            keyword: '',
            laravelData: {},
            participantToggle: {},
            staffNotesIndex: null,
            staffNotesParticipantId: null,
            userInfoStaffNotes: null,
            userSessionStaffNotes: null,
            modalSessionData: {},
            modalSessionTitle: '',
            modalSessionlaravelData: {},
            role: {
                '1': 'Creator',
                '2': 'Critic',
                '3': 'Educator',
                '4': 'Expert',
                '5': 'Fan',
                '6': 'Other'
            }
        }
    },
    mounted() {
        this.loadSessionParticipants();
    },
    emits: ['reloadInterests'],
    methods: {
        loadSessionParticipants: function (page = 1) {
            axios.get('/api/admin/session-interest', { params: { session_id: this.sessionId, partipants_only: 1, keyword: this.keyword, page: page }})
                .then((response) => {
                    this.totalParticipatingUsers = response.data.meta.total;
                    this.participatingUsers = response.data.data;
                    this.laravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the users participating in the session`);
                });
        },
        removeFromSession: function(sessionInterestId) {
            axios.put(`/api/admin/session-interest/${sessionInterestId}`,  { action: 'remove_participant', setting: 'is_participant', value: '0' } )
                .then((response) => {
                    this.$toast.success(`Removed from participant list`);
                    this.$emit("reloadInterests");
                    this.loadSessionParticipants();
                })
                .catch((error) => {
                    this.$toast.error(`Could not remove the user as a participant`);
                })

        },
        makeModerator: function(sessionInterestId) {
            axios.put(`/api/admin/session-interest/${sessionInterestId}`,  { action: 'make_moderator', setting: 'is_moderator', value: '1' } )
                .then((response) => {
                    this.$toast.success(`Saved participant as moderator`);
                    this.loadSessionParticipants();
                })
                .catch((error) => {
                    this.$toast.error(`Could not remove the user as a participant`);
                })

        },
        getModalSessionData: function(user, page = 1, cat = null) {
            this.modalSessionTitle = cat ? cat : this.modalSessionTitle;
            this.modalSessionData = {};
            axios.get(`/api/user/${user.id}/sessions/${this.modalSessionTitle}`, { params: { page: page }})
                .then((response) => {
                    this.modalSessionData = response.data.data;
                    this.modalSessionlaravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the user's session info`);
                });
        },
        populateStaffNotes: function(listIndex, participant) {
            this.staffNotesIndex = listIndex;
            this.staffNotesParticipantId = participant.id;
            this.userInfoStaffNotes = participant.user_info.staff_notes;
            this.userSessionStaffNotes =  participant.staff_notes;
        },
        saveStaffNotes: function() {
            axios.put(`/api/admin/session-interest/${this.staffNotesParticipantId}`,  { action: 'save_staff_notes', staff_notes: this.userSessionStaffNotes })
                .then((response) => {
                    this.participatingUsers[this.staffNotesIndex].staff_notes = this.userSessionStaffNotes;
                    this.$toast.success(`Staff notes saved`);
                    this.$refs.Close.click();

                    this.clearStaffNotes();
                })
                .catch((error) => {
                    this.$toast.error(`Could not save the staff notes`);
                })
        },
        clearStaffNotes: function() {
            this.staffNotesIndex = null;
            this.staffNotesInterestId = null;
            this.userInfoStaffNotes = null;
            this.userSessionStaffNotes = null;
        },
    }

}
</script>
