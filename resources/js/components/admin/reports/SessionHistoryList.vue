<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Session History List</div>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                Found {{ totalHistories }} Records
                            </div>
                            <div class="w-50">
                                <div class="d-flex align-items-end">
                                    <input type="text" class="form-control form-control-sm align-self-center me-2" placeholder="Search name or email" v-model="keyword" v-on:keyup.enter="loadUsers">
                                    <button class="btn btn-outline-secondary btn-sm"  @click="loadHistory">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-sm fs-90">
                            <thead>
                            <tr>
                                <th class="m-0 p-0"></th>
                                <th class="ps-2">Action</th>
                                <th>User</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="history in histories">
                                <td></td>
                                <td class="ps-2">{{ history.action_short_code }}</td>
                                <td>{{ history.user_id }}</td>
                                <td>{{ history.created_at }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-center">
                        <Pagination :data="laravelData" :limit="3" :show-disabled="false" @pagination-change-page="loadUsers" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['data', 'returnQueryParams'],
    data: function() {
        return {
            histories: {},
            totalHistories: '0',
            keyword: '',
            laravelData: {},
        }
    },
    mounted() {
        console.log(this.data);
    },
    methods: {
        loadUsers: function (page = 1) {
            axios.get('/api/profile/user', { params: { keyword: this.keyword, page: page }})
                .then((response) => {
                    console.log(response.data.data)
                    this.totalHistories = response.data.meta.total;
                    this.histories = response.data.data;
                    this.laravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the users`);
                });
        },
    }
}
</script>
