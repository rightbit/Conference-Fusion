<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Volunteers List</div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVolunteerModal"><i class="bi bi-plus-circle"></i> Add new</button>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                Found {{ totalVolunteers }} Volunteers
                            </div>
                            <div class="w-50">
                                <div class="d-flex align-items-end">
                                <input type="text" class="form-control form-control-sm align-self-center me-2" placeholder="Search name or email" v-model="keyword" v-on:keyup.enter="loadVolunteers">
                                <button class="btn btn-outline-secondary btn-sm"  @click="loadVolunteers">
                                    <i class="bi bi-search"></i>
                                </button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-sm fs-90">
                            <thead>
                            <tr>
                                <th class="ps-2">Name</th>
                                <th>Type</th>
                                <th>Email</th>
                                <th class="m-0 p-0"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="volunteer in volunteers">
                                <td class="ps-2">{{ volunteer.user.last_name }}, {{ volunteer.user.first_name }}</td>
                                <td>{{ volunteer.type }}</td>
                                <td>{{ volunteer.user.email }}</td>
                                <td class="m-0 px-0"><a class="btn btn-sm btn-outline-secondary" v-bind:href="this.userLink +'/'+ volunteer.user_id"><i class="bi bi-pencil-square"></i></a></td>
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
    <div class="modal" id="addVolunteerModal" >
      <form id="interestForm" class="needs-validation row" novalidate @submit.prevent="">
        <div class="modal-dialog modal-lg" style="min-width: 25%;">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Add Volunteer Form</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="Close"></button>
            </div>
            <div class="modal-body">
              <div class="container">
                <div class="input-group flex-nowrap mb-3">
                  <input type="text" class="form-control align-self-center" placeholder="Search name or email" v-model="addVolunteerKeyword" v-on:keyup.enter="loadUsers">
                  <button type="button" class="input-group-text" @click="loadUsers"><i class="bi bi-search"></i></button>
                </div>
                <div v-for="user in searchUsers" class="mb-2">
                  <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="selectUser(user)"><i class="bi bi-person-plus-fill"></i></button>
                  <a :href="'/admin/user-profile/'+user.id" target="_blank">#{{ user.id }}</a> {{ user.first_name }} {{ user.last_name }} ({{ user.info.badge_name }})
                </div>
              </div>


            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button v-if="true" type="submit" class="btn btn-primary">Add User</button>
            </div>
          </div>
        </div>
      </form>
    </div>
</template>

<script>
    export default {
        props: ['userLink'],
        data: function() {
            return {
                volunteers: [],
                totalVolunteers: '0',
                keyword: '',
                addVolunteerKeyword: '',
                laravelData: {},
                searchUsers: [],
                totalUsers: '0',
                newVolunteer: {},
            }
        },
        mounted() {
            this.loadVolunteers();
        },
        methods: {
            loadVolunteers: function (page = 1) {
                axios.get('/api/admin/volunteer', { params: { keyword: this.keyword, page: page }})
                    .then((response) => {
                        this.totalVolunteers = response.data.meta.total;
                        this.volunteers = response.data.data;
                        this.laravelData = response.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the volunteers`);
                    });
            },
            loadUsers: function (page = 1) {
              axios.get('/api/profile/user', { params: { keyword: this.addVolunteerKeyword, page: page }})
                  .then((response) => {
                    this.totalUsers = response.data.meta.total;
                    this.searchUsers = response.data.data;
                  })
                  .catch((error) => {
                    this.$toast.error(`Could not load the users`);
                  });
            },
            selectUser: function(user) {
              this.newVolunteer.user_id = user.id;
              this.newVolunteer.type = '';
            },
        }
    }
</script>
