<template>
    <div class="container">
        <div class="table-scrollable-container">
            <h4 class="position-sticky" style="left:0px;">Schedule Board</h4>
            <table class="table table-scrollable table-striped bg-white">
                <thead class="bg-secondary text-white">
                    <tr  class="d-flex align-content-stretch">
                        <td class="bg-secondary text-white">Time</td>
                        <td v-for="room in this.schedule.rooms" class="text-center ts-sc">
                            {{ room.name }}
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(rooms, time) in this.schedule.timeslots" class="d-flex align-content-stretch">
                        <td class="bg-secondary text-white">{{ time }}</td>
                        <td v-for="room in rooms" class="p-1">
                            <div class="card h-100">
                                <div v-if="room.session_name" class="card-body lh-1 p-2">
                                    <a href="#" class="btn stretched-link lh-1 text-start p-0">
                                        <small>{{ room.session_name }}</small>
                                    </a>
                                </div>
                                <div v-else class="card-body text-center">
                                    <button class="btn btn-sm btn-outline-secondary text-nowrap disabled"><i class="bi bi-plus-circle "></i> Add</button>
                                </div>
                                <div v-if="room.track_name" class="card-footer lh-1 text-center">
                                    <small>{{ room.track_name }}</small>
                                </div>

                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>



<script>
export default {
    props: ['conferenceId', 'conferenceName', 'conferenceStartDate', 'conferenceEndDate', 'canEdit'],
    data: function() {
        return {
            schedule: {},
            rooms: {},

        }
    },
    mounted() {
        this.loadScheduleBoard(this.conferenceStartDate);
    },
    methods: {
        loadScheduleBoard: function (date) {
            axios.get('/api/admin/conference-schedule', {
                params: {
                    conference_id: this.conferenceId,
                    date: date,
                }
            })
            .then((response) => {
                this.schedule = response.data.data
            })
            .catch((error) => {
                this.$toast.error(`Could not find the schedule`);
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
