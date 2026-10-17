<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="h4 align-self-center mb-lg-0">Conference {{ conference.short_name }}</div>
                    </div>
                    <div class="card-body">
                        <form class="needs-validation" novalidate  @submit.prevent="addUpdateConference()">
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="required">Name</label>
                                    <input type="text" class="form-control" id="name" placeholder="Conference name" v-model="conference.name" required>
                                    <div class="invalid-feedback">
                                        Please provide a name
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="shortName">Short name</label>
                                    <input type="text" class="form-control" id="shortName" placeholder="Abbreviated name" v-model="conference.short_name" required>
                                    <div class="invalid-feedback">
                                        Please provide a short name
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-12">
                                    <label for="description">Description</label>
                                    <input type="text" class="form-control" id="description" placeholder="Brief Description" v-model="conference.description">
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <label for="url">URL</label> <small>(optional)</small>
                                    <input type="text" class="form-control" id="url" placeholder="Link to main page" v-model="conference.url">
                                </div>
                                <div class="col-md-4">
                                    <label for="location">Location</label> <small>(optional)</small>
                                    <input type="text" class="form-control" id="location" placeholder="Location" v-model="conference.location">
                                </div>
                                <div class="col-md-4">
                                    <label for="timeZone">Time zone</label>
                                    <div class="input-group">
                                        <div class="input-group-text">UTC</div>
                                        <select id="timeZone" class="w-auto"  v-model="conference.time_zone">
                                            <option value="-12">-12</option>
                                            <option value="-11">-11</option>
                                            <option value="-10">-10</option>
                                            <option value="-9">-9</option>
                                            <option value="-8">-8</option>
                                            <option value="-7">-7</option>
                                            <option value="-6">-6</option>
                                            <option value="-5">-5</option>
                                            <option value="-4">-4</option>
                                            <option value="-3">-3</option>
                                            <option value="-2">-2</option>
                                            <option value="-1">-1</option>
                                            <option value="+0">+0</option>
                                            <option value="+1">+1</option>
                                            <option value="+2">+2</option>
                                            <option value="+3">+3</option>
                                            <option value="+4">+4</option>
                                            <option value="+5">+5</option>
                                            <option value="+6">+6</option>
                                            <option value="+7">+7</option>
                                            <option value="+8">+8</option>
                                            <option value="+9">+9</option>
                                            <option value="+10">+10</option>
                                            <option value="+11">+11</option>
                                            <option value="+12">+12</option>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">
                                        Please provide a time zone
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <label for="startDate">Start date</label>
                                    <input type="date" class="form-control" id="startDate" v-model="conference.start_date" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="endDate">End date</label>
                                    <input type="date" class="form-control" id="endDate" v-model="conference.end_date" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="startTime">Session daily start time</label>
                                    <input type="time" class="form-control" id="startTime" v-model="conference.session_start_time " required>
                                </div>
                                <div class="col-md-3">
                                    <label for="endTime">Session daily end time</label>
                                    <input type="time" class="form-control" id="endTime" v-model="conference.session_end_time " required>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <label for="scheduleStartDate">Schedule Calendar Default Date</label>
                                    <input type="date" class="form-control" id="callStartDate" v-model="conference.calendar_default_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="callStartDate">Call for papers / panelists start date</label>
                                    <input type="date" class="form-control" id="callStartDate" v-model="conference.call_start_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="callEndDate">Call for papers / panelists end date</label>
                                    <input type="date" class="form-control" id="callEndDate" v-model="conference.call_end_date " required>
                                </div>
                            </div>

                                <button type="submit" class="btn btn-primary float-end">Save Conference Info</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-10 mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="h4 align-self-center mb-lg-0">Tools</div>
                            </div>
                            <div class="card-body d-flex">
                                <a class="btn btn-primary me-2" href="/admin/import-session" ><i class="bi bi-upload"></i> Import sessions</a>
                                <button class="btn btn-primary me-2" @click="syncProposedStartTimes" ><i class="bi bi-calendar-week"></i> Sync call start times</button>
                                <div v-if="conference.id">
                                    <p class="mb-2">Public schedule: <strong>{{ conference.schedule_published ? 'Published' : 'Unpublished' }}</strong></p>
                                    <button type="button" class="btn btn-primary" @click="updateSchedulePublication" :disabled="publishingSchedule">
                                        <i class="bi bi-calendar-check"></i>
                                        {{ conference.schedule_published ? 'Unpublish session schedule' : 'Publish session schedule' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        props: ['pageId'],
        data: function() {
            return {
                conference: {},
                publishingSchedule: false,
            }
        },
        mounted() {
                this.getConference()
        },
        methods: {
            getConference: function() {
                // Don't try to load if a new user
                if(this.pageId == 0) {
                    let new_info = {
                        name: '',
                        short_name: '',
                        description: '',
                        time_zone: '',
                        start_date: '',
                        end_date: '',
                        calendar_default_date: '',
                        session_start_time: '',
                        session_end_time: '',
                        call_start_date: '',
                        call_end_date: '',
                    }

                    this.conference = new_info;

                    return ;
                }
                axios.get(`/api/admin/conference/${this.pageId}`)
                    .then((response) => {
                        this.conference = response.data.data;
                    })
                    .catch((error) => {
                        window.location.href = "/404";
                        //this.$toast.error(`Could not find the user`);
                    });
            },
            addUpdateConference: function() {
                if(this.conference.id)
                {
                    axios.put(`/api/admin/conference/${this.conference.id}`, this.conference )
                        .then((response) => {

                            this.$toast.success(`Updated ${this.conference.short_name} successfully`);
                        })
                        .catch((error) => {
                            this.$toast.error(`Could not save the conference info<br />` + error.response.data.message);
                        });
                } else {
                    axios.post(`/api/admin/conference`, this.conference)
                        .then((response) => {
                            this.$toast.success(`Added ${this.conference.short_name} successfully`);
                            window.location.href = "/admin/conference-info/"+response.data.data.id;
                        })
                        .catch((error) => {
                            this.$toast.error(`Could not save the conference info<br />` + error.response.data.message);
                        });
                }
            },
            syncProposedStartTimes: function() {
                if(confirm("Do you really want to update all session times for the call?")) {
                    axios.get(`/api/admin/tools/proposed-session-times/${this.conference.id}`)
                        .then((response) => {

                            this.$toast.success(`Updated proposed session times successfully`);
                        })
                        .catch((error) => {
                            this.$toast.error(`Could not sync call schedule<br />` + error.response.data.message);
                        });
                }
            },
            updateSchedulePublication: function() {
                const published = !this.conference.schedule_published;
                const message = published
                    ? 'Publish scheduled sessions on the public schedule? Future edits will also be public.'
                    : 'Hide the session schedule from the public page?';
                if(confirm(message)) {
                    this.publishingSchedule = true;
                    axios.patch(`/api/admin/conference/${this.conference.id}/schedule-publication`, {
                        schedule_published: published,
                    })
                        .then((response) => {
                            this.conference.schedule_published = response.data.data.schedule_published;
                            this.$toast.success(published ? 'Session schedule published' : 'Session schedule unpublished');
                        })
                        .catch((error) => {
                            this.$toast.error('Could not update schedule publication<br />' + (error.response?.data?.message || error.message));
                        })
                        .finally(() => {
                            this.publishingSchedule = false;
                        });
                }
            }
        }

    }
</script>
