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
                                <th class="col-5 ps-2">Name</th>
                                <th class="col-1" >Include in the call?</th>
                                <th class="col-4">Track Heads ({{ this.conferenceName }})</th>
                                <th class="col-2 m-0 p-0"></th>
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
                                <td>
                                    <span v-for="(track_head, index) in track.track_heads">
                                        <a v-bind:href="this.userLink +'/'+ track_head.id">{{ track_head.first_name }} {{ track_head.last_name }}</a>,
                                    </span>
                                    <a v-bind:href="this.userListLink"><i class="bi bi-plus-square-dotted"></i></a>
                                </td>
                                <td class="px-2" v-if="!track.editDisabled">
                                    <button class="btn btn-sm btn-outline-secondary me-2" @click="toggleEdit(track)"><i class="bi bi-pencil-fill"></i></button>
                                </td>
                                <td class="px-2" v-else>
                                    <button class="btn btn-sm btn-outline-secondary me-2" @click="loadTracks">Cancel</button>
                                    <button class="btn btn-sm btn-outline-primary me-2" @click="updateTrack(track)">Save</button>
                                </td>
                                <td class="px-2 text-end">
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
                },
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
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save the track<br />` + error.data.message);
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
            }
        }
    }
</script>
