<style>
/* You can easily set a different style for each split of your days. */
.vuecal__cell-split.dad {background-color: rgba(221, 238, 255, 0.5);}
.vuecal__cell-split.mom {background-color: rgba(255, 232, 251, 0.5);}
.vuecal__cell-split.kid1 {background-color: rgba(221, 255, 239, 0.5);}
.vuecal__cell-split.kid2 {background-color: rgba(255, 250, 196, 0.5);}
.vuecal__cell-split.kid3 {background-color: rgba(255, 206, 178, 0.5);}
.vuecal__cell-split .split-label {color: rgba(0, 0, 0, 0.1);font-size: 26px;}

/* Different color for different event types. */
.vuecal__event.leisure {background-color: rgba(253, 156, 66, 0.9);border: 1px solid rgb(233, 136, 46);color: #fff;}
.vuecal__event.health {background-color: rgba(164, 230, 210, 0.9);border: 1px solid rgb(144, 210, 190);}
.vuecal__event.sport {background-color: rgba(255, 102, 102, 0.9);border: 1px solid rgb(235, 82, 82);color: #fff;}
.vuecal__event-time {display: none;align-items: center;}
.vuecal__event-content {font-size: .8em;}
</style>
  <template>
  <div class="container">

                        <button @click="minCellWidth = minCellWidth ? 0 : 400">
{{ minCellWidth ? 'min cell width: 400px' : 'Add min cell width' }}
</button>
  <button @click="minSplitWidth = minSplitWidth ? 0 : 200">
{{ minSplitWidth ? 'min split width: 200px' : 'Add min split width' }}
</button>
  <button @click="stickySplitLabels = !stickySplitLabels">
Sticky Split Labels
</button>
<button @click="splitDays[1].hide = !splitDays[1].hide">
Show/Hide Dad
</button>

<vue-cal
selected-date="2025-02-12"
:time-from="8 * 60"
:time-to="20 * 60"
:time-step="30"
:disable-views="['years', 'year', 'month', 'week']"
editable-events
:events="events"
:split-days="splitDays"
:snapToTime=15
:sticky-split-labels="stickySplitLabels"
:min-cell-width="minCellWidth"
:min-split-width="minSplitWidth"
>
</vue-cal>

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
import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'

export default {
  components: { VueCal },
  props: ['conferenceId', 'conferenceName', 'conferenceStartDate', 'conferenceEndDate', 'canEdit'],
  data: function() {
  return {
  stickySplitLabels: false,
  minCellWidth: 0,
  minSplitWidth: 200,
  splitDays: [
// The id property is added automatically if none (starting from 1), but you can set a custom one.
// If you need to toggle the splits, you must set the id explicitly.
   { id: 1, class: 'mom', label: 'Mom' },
{ id: 2, class: 'dad', label: 'Dad', hide: false },
{ id: 3, class: 'kid1', label: 'Kid 1' },
{ id: 4, class: 'kid2', label: 'Kid 2' },
{ id: 5, class: 'kid3', label: 'Kid 3' },
{ id: 6, class: 'kid3', label: 'Kid 3' },
{ id: 7, class: 'kid3', label: 'Kid 3' },
{ id: 8, class: 'kid3', label: 'Kid 3' },
{ id: 9, class: 'kid3', label: 'Kid 3' },
{ id: 10, class: 'kid3', label: 'Kid 3' }
],
  events: [
  {
    start: '2025-02-12 10:00',
    end: '2025-02-12 11:00',
    title: 'Writing',
    content: '<i class="icon material-icons">A really long Title goes here for the name of the panel. Probably longer than this even</i>',
    class: 'health',
    split: 1, // Has to match the id of the split you have set (or integers if none).
  },
{
  start: '2018-11-19 18:30',
  end: '2018-11-19 19:15',
  title: 'Dentist appointment',
  content: '<i class="icon material-icons">local_hospital</i>',
  class: 'health',
  split: 2
},
{
  start: '2018-11-20 18:30',
  end: '2018-11-20 20:30',
  title: 'Crossfit',
  content: '<i class="icon material-icons">fitness_center</i>',
  class: 'sport',
  split: 1
},

],
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