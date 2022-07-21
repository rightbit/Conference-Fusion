<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Conferences List</div>
                        <a class="btn btn-primary" :href="this.conferenceLink +'/0'"><i class="bi bi-plus-circle"></i> Add new</a>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                Found {{ totalCons }} Conferences
                            </div>
                            <div class="w-50">
                                <div class="d-flex align-items-end">
                                <input type="text" class="form-control form-control-sm align-self-center me-2" placeholder="Search name or year" v-model="keyword" v-on:keyup.enter="loadConferences">
                                <button class="btn btn-outline-secondary btn-sm"  @click="loadConferences">
                                    <i class="bi bi-search"></i>
                                </button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-sm fs-90">
                            <thead>
                            <tr>
                                <th class="ps-2">Name</th>
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th class="m-0 p-0"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="conference in conferences">
                                <td class="ps-2">{{ conference.name }}</td>
                                <td>{{ conference.location }}</td>
                                <td>{{ conference.start_date }}</td>
                                <td>{{ conference.end_date }}</td>
                                <td class="m-0 px-0"><a class="btn btn-sm btn-outline-secondary" v-bind:href="this.conferenceLink +'/'+ conference.id"><i class="bi bi-pencil-square"></i></a></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-center">
                        <Pagination :data="laravelData" :limit="3" :show-disabled="false" @pagination-change-page="loadConferences" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>



<script>
    export default {
        props: ['conferenceLink'],
        data: function() {
            return {
                conferences: [],
                totalCons: '0',
                keyword: '',
                laravelData: {},
            }
        },
        mounted() {
            this.loadConferences();
        },
        methods: {
            loadConferences: function (page = 1) {
                axios.get('/api/admin/conference', { params: { keyword: this.keyword, page: page }})
                    .then((response) => {
                        this.totalCons = response.data.meta.total;
                        this.conferences = response.data.data;
                        this.laravelData = response.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the conferences`);
                    });
            },
        }
    }
</script>
