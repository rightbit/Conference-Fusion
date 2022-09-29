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
                                    <th>Badge name</th>
                                    <th class="ps-2">Name</th>
                                    <th class="m-0 p-0 text-center fit">Interest level</th>
                                    <th class="m-0 p-0 text-center fit">Experience level</th>
                                    <th class="m-0 p-0 text-center">Staff notes</th>
                                    <th class="m-0 p-0 text-center">Will moderate</th>
                                    <th class="m-0 p-0"></th>
                                </tr>
                            </thead>
                            <tbody v-for="(interest, index) in interestedUsers" class="border-bottom">
                                <tr>
                                    <td>
                                        <button class="btn btn-sm btn-secondary py-0 me-2" @click="this.interestToggle[interest.id] = !this.interestToggle[interest.id]">
                                            <i class="bi bi-person-lines-fill"></i>
                                        </button>
                                        <a :href="'/admin/user-profile/' + interest.user.id" target="_blank">{{ interest.user_info.badge_name }}</a></td>
                                    <td class="ps-2">{{ interest.user.first_name }} {{ interest.user.last_name }}</td>
                                    <td class="text-center fit px-2">
                                        <star-rating
                                            v-model:rating="interest.interest_level"
                                            :read-only="true"
                                            :show-rating="false"
                                            :star-size="18"
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
                                            :border-color="'#000'"
                                            :border-width="1"
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
                                            <div class="p-2"><strong>Role:</strong><br />{{ this.role[interest.panel_role] }}</div>
                                            <div class="p-2 w-30"><strong>Session notes:</strong><br />{{ interest.notes }}</div>
                                            <div class="p-2 w-30"><strong>Participant bio:</strong><br />{{ interest.user_info.biography }}</div>
                                            <div class="p-2 w-30"><strong>Participant info:</strong><br />{{ interest.user_info.notes }}</div>
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
    }

}
</script>
