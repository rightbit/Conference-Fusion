<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Session interest list</div>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                Found {{ totalInterestedUsers }} Users
                            </div>
                            <div class="w-50" v-if="totalInterestedUsers > 10">
                                <div class="d-flex align-items-end">
                                    <input type="text" class="form-control form-control-sm align-self-center me-2" placeholder="Search name" v-model="keyword" v-on:keyup.enter="loadSessionInterest">
                                    <button class="btn btn-outline-secondary btn-sm"  @click="loadSessionInterest">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-sm table-borderless table-responsive fs-90">
                            <thead class="border-bottom border-dark">
                                <tr>
                                    <th><button class="btn btn-warning btn-sm py-0 me-2" @click="toggleInterests"><i class="bi bi-person-fill-down"></i></button> Badge name</th>
                                    <th class="m-0 py-0 px-2 fit">Interest level</th>
                                    <th class="m-0 py-0 px-2 fit">Experience level</th>
                                    <th class="m-0 py-0 px-2 fit">Staff Score</th>
                                    <th class="m-0 p-0 text-center">Staff notes</th>
                                    <th class="m-0 p-0 text-center">Will moderate</th>
                                    <th class="text-end"><button class="btn btn-light btn-sm py-1 text-primary" @click="loadSessionInterest"><i class="bi bi-arrow-repeat"></i> Reload</button></th>
                                </tr>
                            </thead>
                            <tbody v-for="(interest, index) in interestedUsers" class="border-bottom">
                                <tr>
                                    <td>
                                        <button class="btn btn-sm btn-secondary py-0 me-2" @click="this.interestToggle[interest.id] = !this.interestToggle[interest.id]">
                                            <i class="bi bi-person-lines-fill"></i>
                                        </button>
                                        <a :href="'/admin/user-profile/' + interest.user.id" target="_blank">{{ interest.user_info.badge_name }}</a> <span v-if="!sameName(interest)">({{ interest.user.first_name }} {{ interest.user.last_name }})</span></td>

                                    <td class="text-center fit px-2">
                                        <star-rating
                                            v-model:rating="interest.interest_level"
                                            :read-only="true"
                                            :show-rating="false"
                                            :star-size="18"
                                            :active-color="'#dc7c00'"
                                            :border-color="'#000'"
                                            :border-width="1"
                                        />
                                    </td>
                                    <td class="text-center fit px-2">
                                        <star-rating
                                            v-model:rating="interest.experience_level"
                                            :read-only="true"
                                            :show-rating="false"
                                            :star-size="18"
                                            :active-color="'#feb20a'"
                                            :border-color="'#000'"
                                            :border-width="1"
                                        />
                                    </td>
                                    <td class="text-center fit px-2">
                                        <star-rating
                                            v-model:rating="interest.staff_score"
                                            :show-rating="false"
                                            :star-size="18"
                                            :active-color="'#fedf05'"
                                            :border-color="'#000'"
                                            :border-width="1"
                                            @update:rating="saveStaffScore(interest.staff_score, interest.id)"
                                        />
                                    </td>
                                    <td class="m-0 px-0 text-center">
                                        <button class="btn btn-sm"
                                                :class="{'btn-warning': interest.staff_notes, 'btn-secondary': interest.user_info.staff_notes && !interest.staff_notes, 'btn-outline-secondary': !interest.staff_notes && ! interest.user_info.staff_notes}"
                                                type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#interest-staff-notes"
                                                @click="populateStaffNotes(index, interest)">
                                            <i class="bi bi-journal-bookmark-fill"></i>
                                        </button>
                                    </td>
                                    <td class="m-0 px-0 text-center">{{ interest.will_moderate ? 'Yes' : 'No' }}</td>
                                    <td class="m-0 px-0 text-end"><button class="btn btn-sm btn-secondary p-1" @click="addToPanel(interest.id)"><i class="bi bi-person-check-fill"></i> Include</button></td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="m-0 p-0 border border-top-0">
                                        <div class="d-flex flex-row d-flex justify-content-between ps-2" v-if="this.interestToggle[interest.id]">
                                            <div class="p-2">
                                                <strong>Role:</strong><br />{{ this.role[interest.panel_role] }}
                                            </div>
                                            <div class="p-2 w-30"><strong>Session notes:</strong><br />{{ interest.notes }}</div>
                                            <div class="p-2 w-30"><strong>Participant bio:</strong><br />{{ interest.user_info.biography }}</div>
                                            <div class="p-2 w-30"><strong>Participant info:</strong><br />{{ interest.user_info.notes }}</div>
                                            <div class="p-2">
                                                <strong>Other sessions:</strong>
                                                <br />
                                                <button class="btn btn-sm btn-outline-secondary mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#user-session-data" @click="getModalSessionData(interest.user, 1, 'interests')">
                                                    <i class="bi bi-eye-fill"></i> Interests
                                                </button>
                                                <br />
                                                <button class="btn btn-sm btn-outline-secondary mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#user-session-data" @click="getModalSessionData(interest.user, 1, 'panelist')">
                                                    <i class="bi bi-eye-fill"></i> Panelist
                                                </button>
                                                <br />
                                                <button class="btn  btn-sm btn-outline-secondary mb-2 text-start" type="button" data-bs-toggle="modal" data-bs-target="#user-session-data" @click="getModalSessionData(interest.user, 1, 'presenter')">
                                                    <i class="bi bi-eye-fill"></i> Presenter
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-center">
                        <Pagination :data="laravelData" :limit="3" :show-disabled="false" @pagination-change-page="loadSessionInterest" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="interest-staff-notes" aria-hidden="true">
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
    <div class="modal fade" id="user-session-data" aria-hidden="true">
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
import StarRating from 'vue-star-rating'

export default {
    components: {
        StarRating
    },
    props: ['sessionId'],
    data: function() {
        return {
            interestedUsers: {},
            totalInterestedUsers: '0',
            keyword: '',
            laravelData: {},
            interestToggle: {},
            staffNotesIndex: null,
            staffNotesInterestId: null,
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
            }
        }
    },
    mounted() {
        this.loadSessionInterest();
    },
    emits: ['reloadParticipants'],
    methods: {
        loadSessionInterest: function(page = 1) {
            axios.get('/api/admin/session-interest', { params: { session_id: this.sessionId, non_partipants: 1, keyword: this.keyword, page: page }})
                .then((response) => {
                    this.totalInterestedUsers = response.data.meta.total;
                    this.interestedUsers = response.data.data;
                    this.laravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the users interested in the session`);
                });
        },
        addToPanel: function(sessionInterestId) {
            axios.put(`/api/admin/session-interest/${sessionInterestId}`,  { action: 'make_participant', setting: 'is_participant', value: '1' } )
                .then((response) => {
                    this.$toast.success(`Added to participant list`);
                    this.loadSessionInterest();
                    this.$emit("reloadParticipants");
                })
                .catch((error) => {
                    this.$toast.error(`Could not include the user as a participant`);
                })

        },
        populateStaffNotes: function(listIndex, interest) {
            this.staffNotesIndex = listIndex;
            this.staffNotesInterestId = interest.id;
            this.userInfoStaffNotes = interest.user_info.staff_notes;
            this.userSessionStaffNotes =  interest.staff_notes;
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
        saveStaffScore: function(staffScore, interestId) {
            axios.put(`/api/admin/session-interest/${interestId}`,  { action: 'save_staff_score', staff_score: staffScore })
                .then((response) => {
                    this.$toast.success(`Staff score saved`);
                })
                .catch((error) => {
                    this.$toast.error(`Could not save the staff score`);
                })
        },
        saveStaffNotes: function() {
            axios.put(`/api/admin/session-interest/${this.staffNotesInterestId}`,  { action: 'save_staff_notes', staff_notes: this.userSessionStaffNotes })
                .then((response) => {
                    this.interestedUsers[this.staffNotesIndex].staff_notes = this.userSessionStaffNotes;
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
        toggleInterests: function() {
            for(const interest of this.interestedUsers) {
                this.interestToggle[interest.id] = !this.interestToggle[interest.id];
            }

        },
        sameName: function(interest) {
            if (interest.user_info.badge_name == (interest.user.first_name + ' ' + interest.user.last_name)) {
                return true;
            }
            return false;
        },
    }

}
</script>
