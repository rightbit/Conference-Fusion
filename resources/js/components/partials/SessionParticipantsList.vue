<template>
    <div className="container">
        <div className="row justify-content-center">
            <div className="col-md-12">
                <div className="card">
                    <div className="card-header d-flex justify-content-between">
                        <div className="h4 align-self-center mb-lg-0">Session Participant List</div>
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
                                           v-on:keyup.enter="loadSessionInterest">
                                    <button className="btn btn-outline-secondary btn-sm" @click="loadSessionInterest">
                                        <i className="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table className="table table-striped table-sm fs-90">
                            <thead>
                            <tr>
                                <th>Badge Name</th>
                                <th className="ps-2">Name</th>
                                <th>Email</th>
                                <th className="m-0 p-0"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="participant in participatingUsers">
                                <td>{{ participant.user_info.badge_name}}</td>
                                <td className="ps-2">{{ participant.user.first_name }} {{ participant.user.last_name }}</td>
                                <td>{{ participant.user.email }}</td>
                                <td className="m-0 px-0"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div className="card-footer d-flex justify-content-center">
                        <Pagination :data="laravelData" :limit="3" :show-disabled="false"
                                    @pagination-change-page="loadSessionInterest"/>
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
        }
    },
    mounted() {
        this.loadSessionParticipants();
    },
    methods: {
        loadSessionParticipants: function (page = 1) {
            axios.get('/api/admin/session-interest', { params: { session_id: this.sessionId, partipants_only: 1, keyword: this.keyword, page: page }})
                .then((response) => {

                    console.log(response);
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
