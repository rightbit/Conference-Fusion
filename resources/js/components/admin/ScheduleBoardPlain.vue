<style>
    .swap-buttons {
        display: none; /* Hide button */
        opacity: 75%;
    }

    .card:hover .swap-buttons {
        display: block; /* On :hover of div show button */
    }
</style>


<template>
    <div class="container-fluid">
        <div class="table-scrollable-container">
            <h4 class="position-sticky" style="left:0px;">
                <label for="date-select" class="me-3 mt-2">Plain Schedule Board</label>
                <select id="date-select" class="form-select form-select-sm w-auto d-inline" v-model="boardDate" @change="loadScheduleBoard">
                    <option v-for="day in scheduleDates" :value="day">{{ day }}</option>
                </select>
                <a class="btn btn-sm btn-link" href="/admin/schedule-board"><b><i class="bi bi-arrow-left-circle"></i> Back</b></a>
            </h4>
            <span class=""><small>Copy and paste this table into a spreadsheet to format and print</small></span>
            <table class="border border-1">
                <thead class="">
                    <tr  class="">
                        <td class=""><b>Time</b></td>
                        <td v-for="room in this.schedule.rooms" class="text-center ts-sc">
                            <b>{{ room.name }}</b>
                        </td>
                    </tr>
                </thead>
                <tbody v-for="(rooms, time) in this.schedule.timeslots">
                    <tr  class="">
                        <td class=""><b>{{ time }}</b></td>
                        <td v-for="(room) in rooms" class="">
                                <div v-if="room.session_name" class="">
                                        <small>{{ room.session_name }}</small>
                                </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td  v-for="(room) in rooms" class=""><div v-if="room.track_name" class="">
                                <small><b>{{ room.track_name }}</b></small>
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
    props: ['conferenceId', 'conferenceName', 'conferenceStartDate', 'conferenceEndDate',],
    data: function() {
        return {
            schedule: {},
            room_session: {},
            sessionTime: '',
            boardDate: this.conferenceStartDate,
            scheduleDates: [],
        }
    },
    mounted() {
        this.loadScheduleBoard();
        this.calcScheduleDates(this.conferenceStartDate, this.conferenceEndDate);
    },
    methods: {
        loadScheduleBoard: function () {
            let date = this.boardDate;
            axios.get('/api/admin/conference-schedule', {
                params: {
                    conference_id: this.conferenceId,
                    date: date,
                }
            })
                .then((response) => {
                    this.schedule = response.data.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the schedule`);
                });

        },
        calcScheduleDates: function(start, end) {
            let startDate = new Date(start);
            startDate.setMinutes((startDate.getMinutes()) + startDate.getTimezoneOffset());
            let endDate = new Date(end);
            endDate.setMinutes((endDate.getMinutes()) + endDate.getTimezoneOffset());

            const dates = []
            let currentDate = startDate
            const addDays = function (days) {
                const date = new Date(this.valueOf())
                date.setDate(date.getDate() + days)
                return date
            }
            while (currentDate <= endDate) {
                dates.push(currentDate.toISOString().split('T')[0])
                currentDate = addDays.call(currentDate, 1)
            }

            this.scheduleDates = dates;
        },
    },
}
</script>

