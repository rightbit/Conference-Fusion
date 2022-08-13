<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Sessions List</div>
                        <a class="btn btn-primary" :href="this.sessionLink +'/0'"><i class="bi bi-plus-circle"></i> Add new</a>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                Found {{ totalSessions }} Sessions
                            </div>
                            <div class="w-50">
                                <div class="d-flex align-items-end">
                                    <input type="text" class="form-control form-control-sm align-self-center me-2" placeholder="Search session name" v-model="keyword" v-on:keyup.enter="loadSessions">
                                    <button class="btn btn-outline-secondary btn-sm"  @click="loadSessions">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-sm fs-90">
                            <thead>
                            <tr>
                                <th class="ps-2 w-30">Name</th>
                                <th  class="w-30">Description</th>
                                <th>Track</th>
                                <th>Type</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="session in conferenceSessions">
                                <td class="ps-2">{{ session.name }}</td>
                                <td>{{ truncate(session.description , 50, '...') }}</td>
                                <td>{{ session.track }}</td>
                                <td>{{ session.session_type.name }}</td>
                                <td><a class="btn btn-sm btn-outline-secondary" v-bind:href="this.sessionLink +'/'+ session.id"><i class="bi bi-pencil-square"></i></a></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-center">
                        <Pagination :data="laravelData" :limit="3" :show-disabled="false" @pagination-change-page="loadSessions" />
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body" id="addinfo">
                        <h4 class="ms-2 mb-4"> Quick-add new session</h4>
                        <form id="newsession" class="row g-3 align-items-center" @submit.prevent="addSession">
                            <div class="col-md-2">
                                <select id="inputCategory" v-model="new_session.track_id" class="form-select" aria-label="select category">
                                    <option value="" disabled hidden selected>Track (optional)</option>
                                    <option v-for="track in tracks" v-bind:value="track.id">{{ track.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" v-model="new_session.name" class="form-control" id="inputName" placeholder="Name" required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" v-model="new_session.description" class="form-control" id="inputOptions" placeholder="Description (optional)">
                            </div>
                            <div class="col-md-1 text-center">
                                <button type="submit" class="btn btn-primary btn-sm" ><i class="bi bi-arrow-up-circle me-2"></i>Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>



<script>
export default {
    props: ['conferenceId', 'sessionLink'],
    data: function() {
        return {
            conferenceSessions: [],
            totalSessions: '0',
            keyword: '',
            laravelData: {},
            new_session: {
                conference_id: '',
                track_id: '',
                name: '',
                description: '',
            },
            tracks: [],
        }
    },
    mounted() {
        this.loadSessions();
        this.loadTracks();
    },
    methods: {
        loadTracks: function () {
            axios.get('/api/admin/track')
                .then((response) => {
                    this.tracks = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the tracks`);
                });
        },
        loadSessions: function (page = 1) {
            axios.get('/api/admin/conference-session', {
                    params: {
                        conference_id: this.conferenceId,
                        keyword: this.keyword,
                        page: page
                    }
                })
                .then((response) => {
                    this.totalSessions = response.data.meta.total;
                    this.conferenceSessions = response.data.data;
                    this.laravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the sessions`);
                });
        },
        addSession: function() {
            this.new_session.conference_id = this.conferenceId;
            this.new_session.session_status_id = 1;
            axios.post('/api/admin/conference-session', this.new_session)
                .then((response) =>{
                    this.$toast.success(`New session added`);
                    this.loadSessions();
                    this.new_session.track_id = '';
                    this.new_session.name = '';
                    this.new_session.description = '';
                })
                .catch((error) => {
                    this.$toast.error(`Could not save the session`);
                });
        },
        truncate: function (text, length, suffix) {
            if (text.length > length) {
                return text.substring(0, length) + suffix;
            } else {
                return text;
            }
        },
    },
}
</script>
