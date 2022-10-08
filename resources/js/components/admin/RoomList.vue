<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Rooms list for {{ this.conferenceName }}</div>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                Found {{ totalRooms }} rooms
                            </div>
                            <div class="w-25">
                                <div class="d-flex align-items-end">
                                    <input type="text" class="form-control form-control-sm align-self-center me-2" placeholder="Search room name" v-model="keyword" v-on:keyup.enter="loadRooms">
                                    <button class="btn btn-outline-secondary btn-sm"  @click="loadRooms">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-sm fs-90">
                            <thead>
                                <tr>
                                    <th class="ps-2 w-33">Name</th>
                                    <th>Room Number</th>
                                    <th style="width: 30px;">Capacity</th>
                                    <th>A/V</th>
                                    <th style="width: 30px;">Display Order</th>
                                    <th>Notes</th>
                                    <th v-if="this.canEdit"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr scope="row" v-for="room in rooms">
                                    <td class="ps-2"><input type="text" class="form-control form-control-sm" v-model="room.name" :disabled="!this.canEdit" required /></td>
                                    <td><input type="text" class="form-control form-control-sm" v-model="room.room_number" :disabled="!this.canEdit" /></td>
                                    <td><input type="text" class="form-control form-control-sm" v-model="room.capacity" :disabled="!this.canEdit" /></td>
                                    <td>
                                        <select id="inputCategory" v-model="room.has_av" class="form-select form-select-sm" aria-label="select a/v" :disabled="!this.canEdit">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm w-100" v-model="room.display_order" /></td>
                                    <td><input type="text" class="form-control form-control-sm" v-model="room.notes" /></td>
                                    <td v-if="this.canEdit">
                                        <button @click="updateRoom(room)" class="btn btn-sm btn-primary me-1 my-1" ><i class="bi bi-pencil-square"></i></button>
                                        <button @click="deleteRoom(room)" class="btn btn-sm btn-danger" ><i class="bi bi-trash-fill"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-center">
                        <Pagination :data="laravelData" :limit="3" :show-disabled="false" @pagination-change-page="loadRooms" />
                    </div>
                </div>
                <div class="card mb-3" v-if="this.canEdit">
                    <div class="card-body" id="addinfo">
                        <h4 class="ms-2 mb-4">Add new room</h4>
                        <form id="newroom" class="d-flex flex-row" @submit.prevent="addRoom">
                            <div class="p-1">
                                <input type="text" v-model="new_room.name" class="form-control form-control-sm" id="inputName" placeholder="Name" required>
                            </div>
                            <div class="p-1">
                                <input type="text" v-model="new_room.room_number" class="form-control form-control-sm" id="inputName" placeholder="Room Number (optional)">
                            </div>
                            <div class="p-1">
                                <input type="text" v-model="new_room.capacity" class="form-control form-control-sm" id="inputName" placeholder="Capacity" required>
                            </div>
                            <div class="p-1">
                                <select id="inputCategory" v-model="new_room.has_av" class="form-select form-select-sm" aria-label="select a/v" required>
                                    <option value="" disabled hidden selected>Has A/V?</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="p-1">
                                <input type="text" v-model="new_room.display_order" class="form-control form-control-sm" id="inputName" placeholder="Display order">
                            </div>
                            <div class="p-1">
                                <input type="text" v-model="new_room.notes" class="form-control form-control-sm" id="inputOptions" placeholder="Notes (optional)">
                            </div>
                            <div class="p-1 text-center">
                                <button type="submit" class="btn btn-primary btn-sm" >Save</button>
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
    props: ['conferenceId', 'conferenceName', 'canEdit'],
    data: function() {
        return {
            rooms: [],
            totalRooms: '0',
            keyword: '',
            laravelData: {},
            new_room: {
                name: '',
                room_number: '',
                has_av: '',
                capacity: '',
                display_order: '',
                notes: '',
            },
        }
    },
    mounted() {
        this.loadRooms();
    },
    methods: {
        loadRooms: function (page = 1) {
            axios.get('/api/admin/room', {
                    params: {
                        keyword: this.keyword,
                        conference_id: this.conferenceId,
                        page: page
                    }
                })
                .then((response) => {
                    this.totalRooms = response.data.meta.total;
                    this.rooms = response.data.data;
                    this.laravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the rooms`);
                });
        },
        addRoom: function() {
            this.new_room.conference_id = this.conferenceId;
            axios.post('/api/admin/room', this.new_room)
                .then((response) => {
                    this.$toast.success(`New room added`);
                    this.loadRooms();
                    this.new_room.name = '';
                    this.new_room.room_number = '';
                    this.new_room.notes = '';
                    this.new_room.has_av = '';
                    this.new_room.capacity = '';
                })
                .catch((error) => {
                    this.$toast.error(`Could not save the room`);
                });
        },
        updateRoom: function(room) {
            if(confirm("Do you really want to update this room?")) {
                axios.put(`/api/admin/room/${room.id}`, room)
                    .then((response) => {
                        this.$toast.success(`The ${room.name} room was successfully updated`);
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not save ${room.name}<br />` + error.response.data.message);
                    });
            } else {
                this.loadRooms();
            }
        },
        deleteRoom: function (room) {
            if(confirm("Do you really want to delete this room? This cannot be undone.")) {
                axios.delete(`/api/admin/room/${room.id}`)
                    .then((response) => {
                        this.$toast.success(`${room.name} succesfully deleted`);
                        this.loadRooms();
                    })
                    .catch((error) => {
                        this.$toast.error(`Could not delete ${room.name}<br />` + error.response.data.message);
                    });
            }
        },
    },
}
</script>
