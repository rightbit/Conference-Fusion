<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col">
        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between">
            <div class="h4 align-self-center mb-lg-0">Announcement list for {{ this.conferenceName }}</div>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
              <div>
                Found {{ totalAnnouncements }} announcements
              </div>
            </div>
            <table class="table table-sm fs-90">
              <thead>
                <tr>
                  <th class="ps-2">Title</th>
                  <th class="w-33">Message</th>
                  <th>Display Date</th>
                  <th>Pinned</th>
                  <th v-if="this.canEdit"></th>
                </tr>
              </thead>
              <tbody>
                <tr scope="row" v-for="announcement in announcements">
                  <td class="ps-2"><input type="text" class="form-control form-control-sm" v-model="announcement.title" :disabled="!this.canEdit" required /></td>
                  <td><input type="text" class="form-control form-control-sm" v-model="announcement.message" :disabled="!this.canEdit" /></td>
                  <td><input type="text" class="form-control form-control-sm" v-model="announcement.display_date" :disabled="!this.canEdit" /></td>
                  <td>
                    <select id="inputCategory" v-model="announcement.pinned" class="form-select form-select-sm" aria-label="select active" :disabled="!this.canEdit">
                      <option value="1">Yes</option>
                      <option value="0">No</option>
                    </select>
                  </td>
                  <td v-if="this.canEdit">
                    <button @click="updateAnnouncement(announcement)" type="button" class="btn btn-primary btn-sm me-1 my-1"><i class="bi bi-pencil-square"></i></button>
                    <button @click="deleteAnnouncement(announcement)" type="button" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer d-flex justify-content-center">
            <Pagination :data="laravelData" :limit="3" :show-disabled="false" @pagination-change-page="loadAnnouncements" />
          </div>
        </div>
        <div class="card mb-3" v-if="this.canEdit">
          <div class="card-body" id="addinfo">
            <h5 class="card-title">Add New Announcement</h5>
            <form @submit.prevent="addAnnouncement">
              <div class="p-1">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" v-model="new_announcement.title" required>
              </div>
              <div class="p-1">
                <label for="content" class="form-label">Message</label>
                <span class="form-text small ms-2">(Leave blank if title is enough.)</span>
                <textarea class="form-control" id="content" v-model="new_announcement.message"></textarea>
              </div>
              <div class="p-1">
                <label for="content" class="form-label">Pinned to top?</label>
                <select id="inputCategory" v-model="new_announcement.pinned" class="form-select form-select-sm" required>
                  <option value="0">No</option>
                  <option value="1">Yes</option>
                </select>
              </div>
              <div class="p-1">
                <label for="content" class="form-label">Display After Date</label>
                <span class="form-text small ms-2">(Leave blank to display immediately.)</span>
                <input type="datetime-local" class="form-control" id="content" v-model="new_announcement.display_date">
              </div>
              <div class="p-1 text-end">
                <button type="submit" class="btn btn-primary">Add Announcement</button>
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
            announcements: [],
            totalAnnouncements: '0',
            laravelData: {},
            new_announcement: {
                title: '',
                message: '',
                display_date: '',
                pinned: 0,
            },
        }
    },
    mounted() {
        this.loadAnnouncements();
    },
    methods: {
        loadAnnouncements: function (page = 1) {
            axios.get('/api/admin/announcement', {
                    params: {
                        conference_id: this.conferenceId,
                        page: page
                    }
                })
                .then((response) => {
                    this.totalAnnouncements = response.data.meta.total;
                    this.announcements = response.data.data;
                    this.laravelData = response.data;
                })
                .catch((error) => {
                    this.$toast.error(`Could not find the announcements`);
                });
        },
        addAnnouncement: function() {
            this.new_announcement.conference_id = this.conferenceId;
            axios.post('/api/admin/announcement', this.new_announcement)
                .then((response) => {
                    this.$toast.success(`New announcement added`);
                    this.loadAnnouncements();
                    this.new_announcement.title = '';
                    this.new_announcement.message = '';
                    this.new_announcement.display_date = '';
                    this.new_announcement.pinned = 0;
                })
                .catch((error) => {
                    this.$toast.error(`Could not add the announcement`);
                });
        },
        deleteAnnouncement: function(announcement) {
          if(confirm("Do you really want to delete this announcement? This cannot be undone.")) {
            axios.delete(`/api/admin/announcement/${announcement.id}`)
                .then((response) => {
                  this.$toast.success(`Announcement deleted`);
                  this.loadAnnouncements();
                })
                .catch((error) => {
                  this.$toast.error(`Could not delete the announcement`);
                });
          }
        },
        updateAnnouncement: function(announcement) {
            axios.put(`/api/admin/announcement/${announcement.id}`, announcement)
                .then((response) => {
                    this.$toast.success(`Announcement updated`);
                })
                .catch((error) => {
                    this.$toast.error(`Could not update the announcement`);
                });
        },
    }
}
</script>