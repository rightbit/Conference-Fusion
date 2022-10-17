<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Users List</div>
                        <a class="btn btn-primary" href="/admin/user-create"><i class="bi bi-plus-circle"></i> Add new</a>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                Found {{ totalUsers }} Users
                            </div>
                            <div class="w-50">
                                <div class="d-flex align-items-end">
                                <input type="text" class="form-control form-control-sm align-self-center me-2" placeholder="Search name or email" v-model="keyword" v-on:keyup.enter="loadUsers">
                                <button class="btn btn-outline-secondary btn-sm"  @click="loadUsers">
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
                            <tr scope="row" v-for="user in users">
                                <td class="ps-2">{{ user.last_name }}, {{ user.first_name }}</td>
                                <td>{{ user.info.badge_name}}</td>
                                <td>{{ user.email }}</td>
                                <td class="m-0 px-0"><a class="btn btn-sm btn-outline-secondary" v-bind:href="this.userLink +'/'+ user.id"><i class="bi bi-pencil-square"></i></a></td>
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
        props: ['userLink'],
        data: function() {
            return {
                users: [],
                totalUsers: '0',
                keyword: '',
                laravelData: {},
            }
        },
        mounted() {
            this.loadUsers();
        },
        methods: {
            loadUsers: function (page = 1) {
                axios.get('/api/profile/user', { params: { keyword: this.keyword, page: page }})
                    .then((response) => {
                        this.totalUsers = response.data.meta.total;
                        this.users = response.data.data;
                        this.laravelData = response.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the users`);
                    });
            },
        }
    }
</script>
