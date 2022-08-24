<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Session Interest List</div>
                        <a class="btn btn-primary" href="#"><i class="bi bi-plus-circle"></i> Add new</a>
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
                        <table class="table table-striped table-sm fs-90">
                            <thead>
                            <tr>
                                <th class="ps-2">Name</th>
                                <th>Badge Name</th>
                                <th>Email</th>
                                <th class="m-0 p-0"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="interest in interestedUsers">
                                <td class="ps-2">{{ interest.user.last_name }}, {{ interest.user.first_name }}</td>
                                <td>{{ interest.user_info.badge_name}}</td>
                                <td>{{ interest.user.email }}</td>
                                <td class="m-0 px-0"></td>
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
export default {
    props: ['sessionId'],
    data: function() {
        return {
            interestedUsers: {},
            totalInterestedUsers: '0',
            keyword: '',
            laravelData: {},
        }
    },
    mounted() {
        this.loadSessionInterest();
    },
    methods: {
        loadSessionInterest: function(page = 1) {
            axios.get('/api/admin/session-interest', { params: { session_id: this.sessionId, keyword: this.keyword, page: page }})
                .then((response) => {
                    this.totalInterestedUsers = response.data.meta.total;
                    this.interestedUsers = response.data.data;
                    this.laravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the users interested in the session`);
                });
        },
        addUpdateSessionInterest: function() {

        },
    }

}
</script>
