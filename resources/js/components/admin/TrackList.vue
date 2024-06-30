<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Tracks List</div>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm fs-90">
                            <thead>
                            <tr>
                                <th class="w-25 ps-2">Name</th>
                                <th class="">Include in the call?</th>
                                <th class="m-0 p-0">Color Code</th>
                                <th class="">Track Heads ({{ this.conferenceName }})</th>
                                <th class="m-0 p-0"></th>
                                <th class="m-0 p-0"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="track in tracks">
                                <td class=""><input type="text" v-model="track.name" class="form-control" :disabled="!track.editDisabled" /></td>
                                <td>
                                    <select v-model="track.show_on_call" class="form-select" aria-label="select category" :disabled="!track.editDisabled">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </td>
                                <td><input type="color" v-model="track.color_code"></td>
                                <td>
                                    <span v-for="(track_head) in track.track_heads" class="badge bg-secondary me-1">
                                       <button type="button" class="btn btn-sm p-0 text-white" @click="deleteTrackHead(track_head)">
                                           <i class="bi bi-x-circle"></i>
                                       </button>
                                        {{ track_head.first_name }} {{ track_head.last_name }}
                                    </span>
                                    <button class="btn btn-link"
                                            data-bs-toggle="modal"
                                            data-bs-target="#trackHeadModal"
                                            @click="setTrackId(track.id)"
                                    >
                                        <i class="bi bi-plus-square-dotted"></i>
                                    </button>
                                </td>
                                <td class="ps-2 text-end" v-if="!track.editDisabled">
                                    <button class="btn btn-sm btn-outline-secondary me-2" @click="toggleEdit(track)"><i class="bi bi-pencil-fill"></i></button>
                                </td>
                                <td class="ps-2 text-end text-nowrap" v-else>
                                    <button class="btn btn-sm btn-outline-secondary me-2" @click="loadTracks">Cancel</button>
                                    <button class="btn btn-sm btn-outline-primary me-2" @click="updateTrack(track)">Save</button>
                                </td>
                                <td class="">
                                    <button class="btn btn-sm btn-danger" @click="deleteTrack(track)"><i class="bi bi-trash-fill"></i></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body" id="addinfo">
                        <h4 class="ms-2 mb-4">Add new track</h4>
                        <form id="newinfo" class="row g-3 align-items-center" @submit.prevent="addTrack()">
                            <div class="col-md-4">
                                <input type="text" v-model="new_info.name" class="form-control" id="inputName" placeholder="Name" required>
                            </div>
                            <div class="col-md-4">
                                <select id="showOnCall" v-model="new_info.show_on_call" class="form-select" aria-label="select category" required>
                                    <option value="" disabled hidden selected>Show on the Call?</option>
                                    <option value="1">Yes</option>
                                    <option value="0" >No</option>
                                </select>
                            </div>
                            <div class="col-md-1 text-center">
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="trackHeadModal">
        <form id="trackHeadForm" @submit.prevent="">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Select Track Head</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ref="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="input-group flex-nowrap mb-3">
                                <input type="text" class="form-control align-self-center" placeholder="Search name or email" v-model="keyword" v-on:keyup.enter="loadUsers">
                                <button type="button" class="input-group-text" @click="loadUsers"><i class="bi bi-search"></i></button>
                            </div>
                            <div v-for="user in searchUsers" class="mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2" @click="addTrackHead(user.id)"><i class="bi bi-person-plus-fill"></i> Add</button>
                                <a :href="'/admin/user-profile/'+user.id" target="_blank">#{{ user.id }}</a> {{ user.first_name }} {{ user.last_name }} ({{ user.info.badge_name }})
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    </div>
                </div>
            </div>
        </form>
    </div>
</template>


<script>
    export default {
        props: ['conferenceId', 'conferenceName', 'userLink', 'userListLink'],
        data: function() {
            return {
                tracks: [],
                new_info: {
                    name: '',
                    show_on_call: '',
                    color_code: '',
                },
                selectedTrack: 0,
                searchUsers: {},
                totalUsers: 0,
                keyword: '',
            }
        },
        mounted() {
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
            addTrack: function () {
                axios.post('/api/admin/track', this.new_info)
                    .then((response) => {
                        this.$toast.success(`New track added`);
                        this.loadTracks();
                        this.new_info.name = '';
                        this.new_info.show_on_call = '';
                        this.new_info.color_code = '';

                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save the track`);
                    });
            },
            updateTrack: function(track) {
                axios.put(`/api/admin/track/${track.id}`, track)
                    .then((response) => {
                        this.toggleEdit(track)
                        this.$toast.success(`The ${track.name} info was successfully updated`);
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save ${track.name}<br />` + error.response.data.message);
                    });
            },
            deleteTrack: function (track) {
                if(confirm("Do you really want to delete this track? This cannot be undone.")) {
                    axios.delete(`/api/admin/track/${track.id}`)
                        .then((response) => {
                            this.$toast.success(`${track.name} succesfully deleted`);
                            this.loadTracks();
                        })
                        .catch((error) => {
                            this.$toast.error(`Could not delete ${track.name}<br />` + error.response.data.message);
                        });
                }
            },
            toggleEdit: function(track) {
                track.editDisabled = !track.editDisabled;
            },
            loadUsers: function (page = 1) {
                axios.get('/api/profile/user', { params: { keyword: this.keyword, page: page }})
                    .then((response) => {
                        this.totalUsers = response.data.meta.total;
                        this.searchUsers = response.data.data;
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not load the users`);
                    });
            },
            addTrackHead: function(userId) {
                axios.post('/api/admin/track-head', {
                    'conference_id': this.conferenceId,
                    'track_id': this.selectedTrack,
                    'user_id': userId,
                })
                    .then((response) => {
                        this.$toast.success(`New track head added`);
                        this.loadTracks();
                        this.$refs.Close.click();
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save the track head`);
                    });
            },
            deleteTrackHead: function(track_head) {
                axios.delete(`/api/admin/track-head/${track_head.id}`)
                    .then((response) => {
                        this.$toast.success(`Track head removed`);
                        this.loadTracks();
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not remove the track head`);
                    })
            },
            setTrackId: function(trackId) {
                this.selectedTrack = trackId;
            }
        }
    }
</script>
