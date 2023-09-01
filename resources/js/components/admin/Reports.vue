<template>
    <div class="container border">
        <div class="row flex-nowrap bg-white">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-light">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <div class="d-flex align-items-center pb-3 mt-2 mb-md-0 me-md-auto text-dark text-decoration-none">
                        <span class="fs-4 d-none d-sm-inline">Reports</span>
                    </div>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                        <li>
                            <a href="/admin/reports" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-speedometer2"></i> <span class="ms-1 d-none d-sm-inline">Dashboard</span> </a>
                        </li>
                        <li>
                            <a href="#submenuSchedule" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-table"></i> <span class="ms-1 d-none d-sm-inline">Schedule reports</span></a>
                            <ul class="collapse nav flex-column ms-2" id="submenuSchedule" data-bs-parent="#menu">
                                <li class="w-100">
                                    <a href="/admin/reports/schedule-list" id="schedule-list-nav" class="nav-link px-0"> <span class="d-none d-sm-inline">Schedule list</span><span class="d-sm-none">SL</span></a>
                                </li>
<!--                                <li>-->
<!--                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Sessions by type</span><span class="d-sm-none">ST</span></a>-->
<!--                                </li>-->
<!--                                <li>-->
<!--                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Session status</span><span class="d-sm-none">SS</span></a>-->
<!--                                </li>-->
<!--                                <li class="w-100">-->
<!--                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">No participants</span><span class="d-sm-none">NP</span></a>-->
<!--                                </li>-->
<!--                                <li class="w-100">-->
<!--                                    <a href="#" class="nav-link px-0"> <span class="d-none d-sm-inline">Panel moderators</span><span class="d-sm-none">PM</span></a>-->
<!--                                </li>-->
                                <li>
                                    <a :href="`/admin/export/schedule-list/${conferenceId}?skip-checks=1`" id="export-schedule-list-nav" class="nav-link px-0" target="_blank"> <span class="d-none d-sm-inline">Export Schedule List</span><span class="d-sm-none">SCx</span></a>
                                </li>
                                <li class="w-100">
                                    <a href="/admin/reports/session-history-list" id="session-history-list-nav" class="nav-link px-0"> <span class="d-none d-sm-inline">Change Log</span><span class="d-sm-none">CL</span></a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#submenuParticipant" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
                                <i class="fs-4 bi-people"></i> <span class="ms-1 d-none d-sm-inline">Participant reports</span> </a>
                            <ul class="collapse nav flex-column ms-2" id="submenuParticipant" data-bs-parent="#menu">
                                <li>
                                    <a href="/admin/reports/potential-panelist-list" id="potential-panelist-list-nav" class="nav-link px-0"> <span class="d-none d-sm-inline">Potential Panelists</span><span class="d-sm-none">PP</span></a>
                                </li>
                                <li>
                                    <a href="/admin/reports/participant-list" id="participant-list-nav" class="nav-link px-0"> <span class="d-none d-sm-inline">Participant schedules</span><span class="d-sm-none">PS</span></a>
                                </li>
                                <li>
                                    <a :href="`/admin/export/participant-list/${conferenceId}?export_emails=1`" id="export-participant-list-nav" class="nav-link px-0" target="_blank"> <span class="d-none d-sm-inline">Export participants</span><span class="d-sm-none">PEx</span></a>
                                </li>
                                <li>
                                    <a :href="`/admin/export/non-participant-list/${conferenceId}`" id="export-non-participant-list-nav" class="nav-link px-0" target="_blank"> <span class="d-none d-sm-inline">Export non-participants</span><span class="d-sm-none">NEx</span></a>
                                </li>

                            </ul>
                        </li>
<!--                        <li>-->
<!--                            <a href="#" class="nav-link px-0 align-middle">-->
<!--                                <i class="fs-4 bi-grid"></i> <span class="ms-1 d-none d-sm-inline">Other</span> </a>-->
<!--                            -->
<!--                        </li>-->
                    </ul>
                </div>
            </div>
            <div class="col py-3">
                <h2 v-if="!reportId">Choose a report</h2>
                <component :is="reportId" :data="data" :returnQueryParams="params" :conference-id="conferenceId" />
            </div>
        </div>
    </div>

</template>

<script>
    import ScheduleList from "./reports/ScheduleList"
    import ParticipantList from "./reports/ParticipantList"
    import PotentialPanelistList from "./reports/PotentialPanelistList"
    import SessionHistoryList from "./reports/SessionHistoryList"

    export default {
        props: ['reportId', 'conferenceId', 'conferenceName'],
        data: function () {
            return {
                data: {},
                params: {},
            }
        },
        mounted() {
            this.expandNav();
        },
        methods: {
            expandNav: function() {
                if(this.reportId) {
                    try {
                        let el = document.getElementById(this.reportId+'-nav');
                        el.closest('.collapse, .nav').classList.add("show");
                    } catch (error) {
                        // nav link does not exist
                    }
                }
            },
        },
        components: {
            ScheduleList,
            ParticipantList,
            PotentialPanelistList,
            SessionHistoryList,
        }
    }
</script>
