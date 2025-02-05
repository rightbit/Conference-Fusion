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
  <div class="container">
    <div class="table-scrollable-container">
      <h4 class="position-sticky" style="left:0px;">
        <label for="date-select" class="me-3 mt-2">Schedule Board</label>
        <select id="date-select" class="form-select form-select-sm w-auto d-inline" v-model="boardDate" @change="loadScheduleBoard">
          <option v-for="day in scheduleDates" :value="day">{{ day }}</option>
        </select>
        <button class="btn btn-sm btn-link" @click="loadScheduleBoard"><b><i class="bi bi-arrow-repeat"></i> Reload</b></button>
      </h4>
      <table class="table table-scrollable table-striped bg-white">
        <thead class="bg-secondary text-white">
        <tr  class="d-flex align-content-stretch">
          <td class="bg-secondary text-white">Time</td>
          <td v-for="room in this.schedule.rooms" class="text-center ts-sc">
            <div class="mb-0 p-0">
              {{ room.name }}
            </div>
            <div class="mt-0 p-0">
              <small>Capacity: {{ room.capacity }} <i v-if="room.has_av" class="bi bi-person-video3"></i></small>
            </div>
          </td>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(rooms, time) in this.schedule.timeslots" class="d-flex align-content-stretch">
          <td class="bg-secondary text-white">{{ time }}</td>
          <td v-for="(room, room_name) in rooms" class="p-1">
            <div class="card h-100">
              <div v-if="room.session_name" class="swap-buttons h-100 w-100 text-center" style="position:absolute; top:33%; z-index: 100">
                <button class="btn btn-sm btn-secondary me-2"
                        data-bs-toggle="modal"
                        data-bs-target="#session-schedule-modal"
                        @click="populateSessionModal(time, room_name)">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <a :href="'/admin/conference-session/'+room.session_id" class="btn btn-sm btn-primary" target="_blank">
                  <i class="bi bi-box-arrow-up-right"></i>
                </a>
              </div>
              <div v-if="room.session_name" class="card-body lh-1 p-2">
                <small>{{ room.session_name }}</small>
              </div>
              <div v-else class="card-body text-center">
                <button class="btn btn-sm btn-outline-secondary text-nowrap"
                        data-bs-toggle="modal"
                        data-bs-target="#session-schedule-modal"
                        @click="populateSessionModal(time, room_name)">
                  <i class="bi bi-plus-circle "></i> Add
                </button>
              </div>
              <div v-if="room.track_name" class="card-footer lh-1 text-center bg-gradient" :style="[room.track_color ? 'background-color:'+room.track_color : 'background-color:#0dcaf0']">
                <div class="">
                  <small><b>{{ room.track_name }}</b></small>
                </div>

              </div>

            </div>

          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="text-end"><a href="/admin/schedule-board-plain">Printable plain schedule</a></div>
  </div>
  <div class="modal" id="session-schedule-modal" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Schedule a session</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetModal"></button>
        </div>
        <div class="modal-body">
          <h5>{{ boardDate }} {{ sessionTime }} in {{ roomName }}</h5>
          <label for="choose-type">Choose a type</label>
          <select id="choose-type" v-model="this.typeId" class="form-select mb-2" aria-label="select category" @change="loadSessions">
            <option value="">All Types</option>
            <option v-for="type in types" v-bind:value="type.id">{{ type.name }}</option>
          </select>
          <label for="choose-track">Choose a track</label>
          <select v-model="this.trackId" class="form-select mb-2" id="choose-track" @change="loadSessions">
            <option v-for="track in tracks" :value="track.id">{{ track.name }}</option>
            <option value="0" class="">No Track</option>
          </select>
          <label for="choose-track">Choose a session</label>
          <select v-model="this.sessionId" class="form-select mb-2" id="choose-track" @change="loadSessions">
            <option value="0" class="">No Session</option>
            <option v-for="session in sessions" :value="session.id">{{ session.name }}</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="resetTrack">Cancel</button>
          <button v-if="editSession" type="button" class="btn btn-primary" @click="updateSession" data-bs-dismiss="modal">Edit schedule</button>
          <button v-else type="button" class="btn btn-primary" @click="addSession" data-bs-dismiss="modal">Save to schedule</button>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
export default {
  props: ['conferenceId', 'conferenceName', 'conferenceStartDate', 'conferenceEndDate', 'canEdit'],
  data: function() {
    return {
      schedule: {},
      room_session: {},
      tracks: {},
      sessions: {},
      types: {},
      scheduleId: null,
      trackId: 0,
      sessionId: 0,
      roomId: 0,
      roomName: '',
      typeId: '',
      sessionTime: '',
      boardDate: this.conferenceStartDate,
      scheduleDates: [],
      editSession: 0,

    }
  },
  mounted() {
    this.loadScheduleBoard();
    this.loadTracks();
    this.loadTypes();
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
    loadTypes: function () {
      axios.get('/api/admin/session-type')
          .then((response) => {
            this.types = response.data.data;
          })
          .catch((error) => {
            this.$toast.error(`Could not load the session types`);
          });
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
    loadSessions: function() {
      if(!this.trackId) {
        this.sessions = {0:0};
        return;
      }
      axios.get('/api/admin/conference-session', {
        params: {
          conference_id: this.conferenceId,
          track_id: this.trackId,
          type_id: this.typeId,
          all_statuses: '',
          no_paginate: 1,
        }
      })
          .then((response) => {
            this.sessions = response.data.data;
          })
          .catch((error) => {
            this.$toast.error(`Could not find the sessions`);
          });
    },
    populateSessionModal: function (time, room_name) {
      this.trackId = this.schedule.timeslots[time][room_name].track_id ?? 0;
      this.loadSessions();
      this.editSession = 0;
      if(this.schedule.timeslots[time][room_name].id) {
        this.editSession = 1;
      }
      this.scheduleId = this.schedule.timeslots[time][room_name].id ?? 0;
      this.sessionId = this.schedule.timeslots[time][room_name].session_id ?? 0;
      this.roomId = this.schedule.rooms[room_name].id;
      this.sessionTime = time;
      this.roomName = room_name;
    },
    resetModal: function() {
      this.scheduleId = null;
      this.trackId = 0;
      this.sessionId = 0;
      this.roomId = 0;
      this.roomName = '';
      this.sessionTime = '';
      this.sessions = {};
    },
    addSession: function () {
      axios.post('/api/admin/conference-schedule', {
        'conference_id': this.conferenceId,
        'conference_session_id': this.sessionId ? this.sessionId : null,
        'track_id': this.trackId ? this.trackId : null,
        'room_id': this.roomId,
        'date': this.boardDate,
        'time': this.sessionTime,

      })
          .then((response) => {
            let newSchedule = response.data.data;
            this.populateCard(newSchedule.id);
            this.$toast.success(`The schedule was successfully saved`);
            this.resetModal();
          })
          .catch((error) => {
            this.$toast.error(`Could not schedule the session`);
            this.resetModal();
          })

    },
    updateSession: function() {
      if (this.trackId == "0") this.trackId = 0;
      if (this.trackId == "0" || this.sessionId == "0") this.sessionId = 0;
      axios.put(`/api/admin/conference-schedule/${this.scheduleId}`, {
        'conference_id': this.conferenceId,
        'conference_session_id': this.sessionId ? this.sessionId : null,
        'track_id': this.trackId ? this.trackId : null,
        'room_id': this.roomId,
        'date': this.boardDate,
        'time': this.sessionTime,

      })
          .then((response) => {
            let updatedSchedule = response.data.data;
            this.populateCard(updatedSchedule.id);
            this.$toast.success(`The schedule was successfully updated`);
            this.resetModal();
          })
          .catch((error) => {
            this.$toast.error(`Could not update the schedule`);
            this.resetModal();
          })
    },
    populateCard: function(id) {
      this.schedule.timeslots[this.sessionTime][this.roomName] = {
        conference_id: this.conferenceId,
        date: this.boardDate,
        id: id,
        room_id: this.roomId,
        session_id: this.sessionId ? this.sessionId : null,
        session_name: this.sessionId ? this.getItemNameById(this.sessions, this.sessionId) : 0,
        time: this.sessionTime,
        track_id: this.trackId ? this.trackId : null,
        track_name: this.trackId ? this.getItemNameById(this.tracks, this.trackId) : 0,
        track_color: this.trackId ? this.getItemColorById(this.tracks, this.trackId) : null,
      }
    },
    getItemNameById: function(data, id) {
      let item = data.filter(
          function(data) { if( data.id == id) return data.name }
      );
      return item[0].name;
    },
    getItemColorById: function(data, id) {
      let item = data.filter(
          function(data) { if( data.id == id) return data.color_code }
      );
      return item[0].color_code;
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