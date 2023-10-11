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
                        <div class="d-flex justify-content-between mb-0">
                            <div>
                                Found {{ totalSessions }} Sessions
                            </div>
                            <div class="w-75">
                                <div class="d-flex align-items-end">
                                    <select id="inputCategory" v-model="searchTypeId" class="form-select form-select-sm me-2 w-33" aria-label="select category" @change="loadSessions">
                                        <option value="" disabled hidden selected>Type</option>
                                        <option value="">All Types</option>
                                        <option v-for="type in types" v-bind:value="type.id">{{ type.name }}</option>
                                    </select>
                                    <select id="inputCategory" v-model="searchTrackId" class="form-select form-select-sm me-2 w-33" aria-label="select category" @change="loadSessions">
                                        <option value="" disabled hidden selected>Track</option>
                                        <option value="">All Tracks</option>
                                        <option v-for="track in tracks" v-bind:value="track.id">{{ track.name }}</option>
                                        <option value="empty">No Track</option>
                                    </select>
                                    <input type="text" class="form-control form-control-sm align-self-center me-2" placeholder="Search session name" v-model="keyword" v-on:keyup.enter="loadSessions">
                                    <button class="btn btn-outline-secondary btn-sm"  @click="loadSessions">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-if="false"> <!-- Use this as a key in in the future, if needed -->
                            <i class='bi bi-lightbulb-fill text-warning me-1'></i> Planning<br/>
                            <i class='bi bi-c-square-fill text-success me-1'></i> Ready for Call<br/>
                            <i class='bi bi-chat-left-dots text-success me-1'></i> User Submitted (Presentations only)<br/>
                            <i class='bi bi-person-fill-check text-success me-1'></i> Participants Assigned<br/>
                            <i class='bi bi-check-circle text-success me-1'></i> Scheduled<br/>
                            <i class='bi bi-sim-slash-fill text-danger me-1'></i> Not used<br/>
                            <i class='bi bi-slash-circle text-danger me-1'></i> Canceled (after being on the schedule)<br/>
                            <i class='bi bi-calendar-check-fill text-success'></i> On the calendar<br/>
                            <i class='bi bi-calendar2-x-fill text-danger'></i> Scheduled, but not on the calendar!<br/>
                        </div>
                        <div class="text-end">
                            <input type="checkbox" v-model="searchAllStatuses" class="form-check-input" id="statuses" v-on:change="loadSessions">
                            <label class="form-check-label ms-2" for="statuses">Include not used/canceled sessions </label>
                        </div>
                        <div class="text-end mb-3">
                            <input type="checkbox" v-model="searchNotScheduled" class="form-check-input" id="statuses" v-on:change="loadSessions">
                            <label class="form-check-label ms-2" for="statuses">Exclude scheduled sessions </label>
                        </div>
                        <table class="table table-striped table-sm fs-90">
                            <thead>
                            <tr>
                                <th class="ps-2 w-30">Name</th>
                                <th  class="w-30">Description</th>
                                <th>Track</th>
                                <th>Type</th>
                                <th class="text-center">Status <i class="bi bi-info-circle-fill text-info"
                                                                  data-bs-toggle="tooltip"
                                                                  data-bs-placement="top"
                                                                  title="Mouseover icon for definition"></i>
                                </th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr scope="row" v-for="session in conferenceSessions">
                                <td class="ps-2">{{ session.name }}</td>
                                <td>{{ truncate(session.description , 50, '...') }}</td>
                                <td>{{ session.track }}</td>
                                <td>{{ session.session_type.name }}</td>
                                <td class="text-center">
                                    <i v-if="session.status"
                                       :class="this.statusIcons[session.status.id]"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       :title="session.status.status"
                                    ></i>
                                    <i v-if="session.conference_schedule && session.conference_schedule.length > 0"
                                       class='bi bi-calendar-check-fill text-success'
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       :title="formatDate(session.conference_schedule[0].date + ' ' + session.conference_schedule[0].time)"
                                    ></i>
                                    <i v-if="session.conference_schedule && session.conference_schedule.length == 0 && session.status.id == 5"
                                       class='bi bi-calendar2-x-fill text-danger'
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Scheduled but not on calendar"
                                    ></i>

                                </td>
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
                                <select id="inputCategory" v-model="new_session.track_id" class="form-select" aria-label="select category" required>
                                    <option value="" disabled hidden selected>Track</option>
                                    <option v-for="track in tracks" v-bind:value="track.id">{{ track.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" v-model="new_session.name" class="form-control" id="inputName" placeholder="Name" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" v-model="new_session.description" class="form-control" id="inputOptions" placeholder="Description" required>
                            </div>
                            <div class="col-md-2">
                                <select id="inputCategory" v-model="new_session.type_id" class="form-select" aria-label="select category" required>
                                    <option value="" disabled hidden selected>Type</option>
                                    <option v-for="type in types" v-bind:value="type.id">{{ type.name }}</option>
                                </select>
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
import dayjs from "dayjs";
import {Tooltip} from "bootstrap";

export default {
    props: ['conferenceId', 'sessionLink'],
    data: function() {
        return {
            conferenceSessions: [],
            totalSessions: '0',
            searchTrackId: '',
            searchTypeId: '',
            searchAllStatuses: false,
            searchNotScheduled: false,
            keyword: '',
            laravelData: {},
            new_session: {
                conference_id: '',
                track_id: '',
                type_id: '',
                name: '',
                description: '',
            },
            tracks: [],
            types: [],
            statusIcons: [],
        }
    },
    mounted() {
        new Tooltip(document.body, {
            selector: "[data-bs-toggle='tooltip']",
        }),
        this.populateStatusIcons();
        this.loadSessions();
        this.loadTracks();
        this.loadTypes();
    },
    methods: {
        populateStatusIcons() {
            this.statusIcons[1] = 'bi bi-lightbulb-fill text-warning me-1';
            this.statusIcons[2] = 'bi bi-c-square-fill text-success me-1';
            this.statusIcons[3] = 'bi bi-chat-left-dots text-success me-1';
            this.statusIcons[4] = 'bi bi-person-fill-check text-success me-1';
            this.statusIcons[5] = 'bi bi-check-circle text-success me-1';
            this.statusIcons[6] = 'bi bi-sim-slash-fill text-danger me-1';
            this.statusIcons[7] = 'bi bi-slash-circle text-danger me-1';
        },
        formatDate(dateString) {
            if(!dateString) { return null }
            const date = dayjs(dateString);
            return date.format('ddd MMM DD, h A');
        },
        loadTracks: function () {
            axios.get('/api/admin/track')
                .then((response) => {
                    this.tracks = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the tracks`);
                });
        },
        loadTypes: function () {
            axios.get('/api/admin/session-type')
                .then((response) => {
                    this.types = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not load the session types`);
                });
        },
        loadSessions: function (page = 1) {
            axios.get('/api/admin/conference-session', {
                    params: {
                        conference_id: this.conferenceId,
                        track_id: this.searchTrackId,
                        type_id: this.searchTypeId,
                        all_statuses: this.searchAllStatuses ? '1' : '',
                        not_scheduled: this.searchNotScheduled ? '1' : '',
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
                    this.new_session.name = '';
                    this.new_session.description = '';
                })
                .catch((error) => {
                    this.$toast.error(`Could not save the session`);
                });
        },
        truncate: function (text, length, suffix) {
            if (text && text.length > length) {
                return text.substring(0, length) + suffix;
            } else {
                return text;
            }
        },
    },
}
</script>
