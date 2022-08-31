<template>
    <div className="container">
        <div className="row justify-content-center">
            <div className="col-md-12">
                <div className="card">
                    <div className="card-header d-flex justify-content-between">
                        <div className="h4 align-self-center mb-lg-0">Session participant list</div>
                        <a className="btn btn-primary" href="#"><i className="bi bi-plus-circle"></i> Add new</a>
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
                                <th>Badge Name</th>
                                <th className="ps-2">Name</th>
                                <th>Email</th>
                                <th className="m-0 p-0"></th>
                            </tr>
                            </thead>
                            <tbody  v-for="participant in participatingUsers">
                            <tr>
                                <td><button class="btn btn-sm btn-secondary py-0 me-2" @click="this.participantToggle[participant.id] = !this.participantToggle[participant.id]">
                                    <i class="bi bi-person-lines-fill"></i>
                                </button>
                                    <a :href="'/admin/user-profile/' + participant.user.id" target="_blank">{{ participant.user_info.badge_name }}</a>
                                </td>
                                <td className="ps-2">{{ participant.user.first_name }} {{ participant.user.last_name }}</td>
                                <td>{{ participant.user.email }}</td>
                                <td className="m-0 px-0"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="m-0 p-0 border border-top-0">
                                    <div class="d-flex flex-row d-flex justify-content-between ps-2" v-if="this.participantToggle[participant.id]">
                                        <div class="p-2" v-if="participant.panel_role"><strong>Role:</strong><br />{{ this.role[participant.panel_role] }}</div>
                                        <div class="p-2 w-30"><strong>Session notes:</strong><br />{{ participant.notes }}</div>
                                        <div class="p-2 w-30"><strong>Participant bio:</strong><br />{{ participant.user_info.biography }}</div>
                                        <div class="p-2 w-30"><strong>Participant info:</strong><br />{{ participant.user_info.notes }}</div>
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
        this.loadSessionParticipants();
    },
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
        addUpdateSessionparticipants: function () {

        },
    }

}
</script>
