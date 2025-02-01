// Require
require('./bootstrap')

// Import
import { createApp } from 'vue'
import Toaster from "@meforma/vue-toaster";
import LaravelVuePagination from 'laravel-vue-pagination';

import AdminAnnouncementList from "./components/admin/AnnouncementList";
import AdminConferenceList from "./components/admin/ConferenceList";
import AdminConferenceInfo from "./components/admin/ConferenceInfo";
import AdminConfiguration from './components/admin/Configuration'
import AdminReports from "./components/admin/Reports";
import AdminRoomList from "./components/admin/RoomList";
import AdminScheduleBoard from "./components/admin/ScheduleBoard";
import AdminScheduleBoardPlain from "./components/admin/ScheduleBoardPlain";
import AdminSponsorList from "./components/admin/SponsorList";
import AdminTrackList from "./components/admin/TrackList";
import AdminUserList from "./components/admin/UserList";
import AdminVolunteerList from "./components/admin/VolunteerList";
import AdminConferenceSessionList from "./components/admin/ConferenceSessionList";
import AdminConferenceSession from "./components/admin/ConferenceSession";

import CallForPanels from "./components/CallForPanels";
import CallForPresentations from "./components/CallForPresentations";
import UserProfile from "./components/UserProfile";
import UserSessionView from "./components/UserSessionView";

import PartialsTrackOptions from "./components/partials/TrackOptions";
import PartialsTypeOptions from "./components/partials/TypeOptions";
import PartialsSessionInterestList from "./components/partials/SessionInterestList";
import PartialsSessionParticipantsList from "./components/partials/SessionParticipantsList";
import PartialsSessionStatusOptions from "./components/partials/SessionStatusOptions";
import PartialsPresenterPromo from "./components/partials/PresenterPromo";
import PartialsUserInfoData from "./components/partials/UserInfoData";
import PartialsUserProfileImageModal from "./components/partials/UserProfileImageModal";
import PartialsUserUpdatePasswordModal from "./components/partials/UserUpdatePasswordModal";


//Optional Modules
import CallForSigning from "./components/CallForSigning";

// Create and Use
const app = createApp({})
app.use(Toaster, {
    position: "top",
});


// Components
app.component('Pagination', LaravelVuePagination)
app.component('admin-announcement-list', AdminAnnouncementList)
app.component('admin-conference-session-list', AdminConferenceSessionList)
app.component('admin-conference-session', AdminConferenceSession)
app.component('admin-conference-info', AdminConferenceInfo)
app.component('admin-conference-list', AdminConferenceList)
app.component('admin-configuration', AdminConfiguration)
app.component('admin-room-list', AdminRoomList)
app.component('admin-reports', AdminReports)
app.component('admin-schedule-board', AdminScheduleBoard)
app.component('admin-schedule-board-plain', AdminScheduleBoardPlain)
app.component('admin-sponsor-list', AdminSponsorList)
app.component('admin-track-list', AdminTrackList)
app.component('admin-user-list', AdminUserList)
app.component('admin-volunteer-list', AdminVolunteerList)

app.component('call-for-panels', CallForPanels)
app.component('call-for-presentations', CallForPresentations)
app.component('user-profile', UserProfile)
app.component('user-session-view', UserSessionView)

app.component('partials-track-options', PartialsTrackOptions)
app.component('partials-type-options', PartialsTypeOptions)
app.component('partials-session-interest-list', PartialsSessionInterestList)
app.component('partials-session-participants-list', PartialsSessionParticipantsList)
app.component('partials-session-status-options', PartialsSessionStatusOptions)
app.component('partials-presenter-promo', PartialsPresenterPromo)
app.component('partials-user-info-data', PartialsUserInfoData)
app.component('partials-user-profile-image-modal', PartialsUserProfileImageModal)
app.component('partials-user-update-password-modal', PartialsUserUpdatePasswordModal)


//Optional Modules
app.component('call-for-signing', CallForSigning)



// Mounts
app.mount('#app')

