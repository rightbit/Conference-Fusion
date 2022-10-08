<template>
    <div class="container">
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <td></td>
                    <td v-for="room in this.schedule.rooms" class="text-center">
                        {{ room.name }}
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(rooms, time) in this.schedule.timeslots">
                    <td>{{ time }}</td>
                    <td v-for="room in rooms">
                        {{ room.session_name }}<br />
                        <small>{{ room.track_name }}</small>
                    </td>
                </tr>
            </tbody>
        </table>
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
                console.log(response.data)
            })
            .catch((error) => {

                console.log(error)
                this.$toast.error(`Could not find the schedule`);
            });

        },

    },
}
</script>
