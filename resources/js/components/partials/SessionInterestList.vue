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
                                    <th>Badge Name</th>
                                    <th class="ps-2">Name</th>
                                    <th class="m-0 p-0">Interest Level</th>
                                    <th class="m-0 p-0">Experience Level</th>
                                    <th class="m-0 p-0">Will Moderate</th>
                                    <th class="m-0 p-0"></th>
                                </tr>
                            </thead>
                            <tbody v-for="interest in interestedUsers" class="border-bottom">
                                <tr>
                                    <td>
                                        <button class="btn btn-sm btn-secondary py-0 me-2" @click="this.interestToggle[interest.id] = !this.interestToggle[interest.id]">
                                            <i class="bi bi-person-lines-fill"></i>
                                        </button>
                                        <a :href="'/admin/user-profile/' + interest.user.id" target="_blank">{{ interest.user_info.badge_name }}</a></td>
                                    <td class="ps-2">{{ interest.user.first_name }} {{ interest.user.last_name }}</td>
                                    <td>
                                        <star-rating
                                            v-model:rating="interest.interest_level"
                                            :read-only="true"
                                            :show-rating="false"
                                            :star-size="18"
                                            :border-color="'#000'"
                                            :border-width="1"
                                        />
                                    </td>
                                    <td>
                                        <star-rating
                                            v-model:rating="interest.experience_level"
                                            :read-only="true"
                                            :show-rating="false"
                                            :star-size="18"
                                            :border-color="'#000'"
                                            :border-width="1"
                                        />
                                    </td>
                                    <td class="m-0 px-0">{{ interest.will_moderate ? 'Yes' : 'No' }}</td>
                                    <td class="m-0 px-0 text-end"><button class="btn btn-sm btn-secondary p-1" @click="addToPanel(interest.id)"><i class="bi bi-person-check-fill"></i> Include</button></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="m-0 p-0 border border-top-0">
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
    }

}
</script>
