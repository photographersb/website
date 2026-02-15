import './bootstrap'
import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import lazyload from './directives/lazyload'
import api from './api'

// Components
import App from './App.vue'
const PhotographerSearch = () => import('./components/PhotographerSearch.vue')
const PhotographerProfile = () => import('./components/PhotographerProfile.vue')
const BookingForm = () => import('./components/BookingForm.vue')
const Auth = () => import('./components/Auth.vue')
const CookieConsentBanner = () => import('./components/CookieConsentBanner.vue')
const AdminDashboard = () => import('./Pages/Admin/Dashboard.vue')
const AdminNotificationCenter = () => import('./Pages/Admin/NotificationCenter.vue')
const AdminAnalytics = () => import('./Pages/Admin/Analytics.vue')
const AdminRolesPermissions = () => import('./Pages/Admin/RolesPermissions.vue')
const AdminApprovals = () => import('./Pages/Admin/Approvals.vue')
const AdminSystemHealth = () => import('./Pages/Admin/SystemHealth.vue')
const AdminSettingsGeneral = () => import('./Pages/Admin/SettingsGeneral.vue')
const AdminProfile = () => import('./Pages/Admin/Profile.vue')
const AdminSettingsAccount = () => import('./Pages/Admin/Settings/Account.vue')
const AdminVerificationDocuments = () => import('./Pages/Admin/VerificationDocuments.vue')
const AdminEventAttendance = () => import('./Pages/Admin/EventAttendance.vue')
const AdminSubmissions = () => import('./Pages/Admin/Submissions.vue')
const AdminSponsorships = () => import('./Pages/Admin/Sponsorships.vue')
const AdminFeedback = () => import('./Pages/Admin/Feedback.vue')
const AdminComplaints = () => import('./Pages/Admin/Complaints.vue')
const AdminPayouts = () => import('./Pages/Admin/Payouts.vue')
const AdminBackups = () => import('./Pages/Admin/Backups.vue')
const AdminSEOSettings = () => import('./Pages/Admin/SEOSettings.vue')
const AdminEmailTemplates = () => import('./Pages/Admin/EmailTemplates.vue')
const AdminScoringSystem = () => import('./Pages/Admin/ScoringSystem.vue')
const AdminEventAlbums = () => import('./Pages/Admin/EventAlbums.vue')
const AdminSystemHealthSitemap = () => import('./Pages/Admin/SystemHealthSitemap.vue')
const AdminSponsorsPage = () => import('./Pages/Admin/Sponsors.vue')
const AdminHashtagsPage = () => import('./Pages/Admin/Hashtags.vue')
const AdminPhotoCategoriesPage = () => import('./Pages/Admin/PhotoCategories.vue')
const AdminFeaturedPhotographersIndex = () => import('./Pages/Admin/FeaturedPhotographers/Index.vue')
const AdminDataHubPage = () => import('./Pages/Admin/DataHub.vue')
const AdminFeaturedHashtagsPage = () => import('./Pages/Admin/FeaturedHashtags.vue')
const AdminLocationsPage = () => import('./Pages/Admin/Cities/Index.vue')
const AdminDataHub = () => import('./components/AdminDataHub.vue')
const AdminSponsorManagement = () => import('./components/AdminSponsorManagement.vue')
const AdminPhotoCategoryManagement = () => import('./components/AdminPhotoCategoryManagement.vue')
const AdminHashtagManagement = () => import('./components/AdminHashtagManagement.vue')
const EventsList = () => import('./components/EventsList.vue')
const CompetitionsList = () => import('./components/CompetitionsList.vue')
const Competitions = () => import('./Pages/Competitions.vue')
const CompetitionDetail = () => import('./Pages/CompetitionDetail.vue')
const CompetitionSubmit = () => import('./Pages/CompetitionSubmit.vue')
const CompetitionGallery = () => import('./Pages/CompetitionGallery.vue')
const SubmissionDetail = () => import('./Pages/SubmissionDetail.vue')
const Events = () => import('./Pages/Events.vue')
const EventDetail = () => import('./Pages/EventDetail.vue')
const EventTickets = () => import('./Pages/EventTickets.vue')
const PhotographerDashboard = () => import('./components/PhotographerDashboard.vue')
const PhotographerAchievements = () => import('./Pages/PhotographerAchievements.vue')
const ReviewForm = () => import('./components/ReviewForm.vue')
const PaymentCheckout = () => import('./components/PaymentCheckout.vue')
const PaymentSuccess = () => import('./components/PaymentSuccess.vue')
const PaymentFailed = () => import('./components/PaymentFailed.vue')
const PaymentCancelled = () => import('./components/PaymentCancelled.vue')
const TransactionHistory = () => import('./components/TransactionHistory.vue')
const NotificationsInbox = () => import('./components/NotificationsInbox.vue')
const AdminCompetitionsIndex = () => import('./Pages/Admin/Competitions/Dashboard.vue')
const AdminCompetitionsShow = () => import('./Pages/Admin/Competitions/Show.vue')
const AdminCompetitionsCreate = () => import('./Pages/Admin/Competitions/Create.vue')
const AdminCompetitionsEdit = () => import('./Pages/Admin/Competitions/Edit.vue')
const AdminEventsIndex = () => import('./Pages/Admin/Events/Index.vue')
const AdminEventsCreate = () => import('./Pages/Admin/Events/Create.vue')
const AdminEventsEdit = () => import('./Pages/Admin/Events/Edit.vue')
const AdminEventCheckIn = () => import('./Pages/Admin/Events/CheckIn.vue')
const SubmissionModeration = () => import('./Pages/Admin/SubmissionModeration.vue')
const JudgeScoring = () => import('./Pages/JudgeScoring.vue')
const JudgeDashboard = () => import('./Pages/JudgeDashboard.vue')
const WinnerAnnouncement = () => import('./Pages/WinnerAnnouncement.vue')
const AdminUsersIndex = () => import('./Pages/Admin/Users/Index.vue')
const AdminPhotographersIndex = () => import('./Pages/Admin/Photographers/Index.vue')
const AdminVerificationsIndex = () => import('./Pages/Admin/Verifications/Index.vue')
const AdminBookingsIndex = () => import('./Pages/Admin/Bookings/Index.vue')
const AdminReviewsIndex = () => import('./Pages/Admin/Reviews/Index.vue')
const AdminTransactionsIndex = () => import('./Pages/Admin/Transactions/Index.vue')
const AdminPaymentsIndex = () => import('./Pages/Admin/Payments/Index.vue')
const AdminSettingsIndex = () => import('./Pages/Admin/Settings/Index.vue')
const AdminAuditLogsIndex = () => import('./Pages/Admin/AuditLogs/Index.vue')
const AdminContactMessagesIndex = () => import('./Pages/Admin/ContactMessages/Index.vue')
const AdminNoticesIndex = () => import('./Pages/Admin/Notices/Index.vue')
const AdminCertificatesIndex = () => import('./Pages/Admin/Certificates/Index.vue')
const AdminCertificatesManualIssuance = () => import('./Pages/Admin/Certificates/ManualIssuance.vue')
const AdminCertificatesTemplates = () => import('./Pages/Admin/Certificates/Templates.vue')
const AdminShareFrameGenerator = () => import('./Pages/Admin/ShareFrameGenerator.vue')
const AdminSettingsChangeTracking = () => import('./Pages/Admin/Settings/ChangeTracking.vue')
const AdminMentorsIndex = () => import('./Pages/Admin/Mentors/Index.vue')
const AdminMentorsCreate = () => import('./Pages/Admin/Mentors/Create.vue')
const AdminMentorsEdit = () => import('./Pages/Admin/Mentors/Edit.vue')
const AdminMentorsShow = () => import('./Pages/Admin/Mentors/Show.vue')
const AdminJudgesIndex = () => import('./Pages/Admin/Judges/Index.vue')
const AdminJudgesCreate = () => import('./Pages/Admin/Judges/Create.vue')
const AdminJudgesEdit = () => import('./Pages/Admin/Judges/Edit.vue')
const AdminJudgesShow = () => import('./Pages/Admin/Judges/Show.vue')
const AdminActivityLogsIndex = () => import('./Pages/Admin/ActivityLogs/Index.vue')
const AdminErrorCenter = () => import('./Pages/Admin/ErrorCenter.vue')
const AdminCategoriesIndex = () => import('./Pages/Admin/Categories/Index.vue')
const AdminSponsors = () => import('./components/AdminSponsors.vue')
const About = () => import('./Pages/About.vue')
const HowItWorks = () => import('./Pages/HowItWorks.vue')
const Pricing = () => import('./Pages/Pricing.vue')
const Contact = () => import('./Pages/Contact.vue')
const HelpCenter = () => import('./Pages/HelpCenter.vue')
const Privacy = () => import('./Pages/Privacy.vue')
const Terms = () => import('./Pages/Terms.vue')
const Cookies = () => import('./Pages/Cookies.vue')
const Settings = () => import('./Pages/Settings.vue')
const PhotographerSettings = () => import('./Pages/PhotographerSettings.vue')
const BecomeSponsor = () => import('./Pages/BecomeSponsor.vue')
const BeFeautured = () => import('./Pages/BeFeautured.vue')
const FeaturedPhotographerPayment = () => import('./Pages/FeaturedPhotographerPayment.vue')
const FeaturedPhotographerAnalytics = () => import('./Pages/FeaturedPhotographerAnalytics.vue')
const FeaturedPhotographerUpgrade = () => import('./Pages/FeaturedPhotographerUpgrade.vue')
const Bookings = () => import('./Pages/Bookings.vue')
const ClientDashboard = () => import('./Pages/Client/Dashboard.vue')
const ClientGalleries = () => import('./Pages/Client/Galleries.vue')
const ClientGalleryShow = () => import('./Pages/Client/GalleryShow.vue')
const ClientFavorites = () => import('./Pages/Client/Favorites.vue')
const ClientPayments = () => import('./Pages/Client/Payments.vue')
const ClientNotifications = () => import('./Pages/Client/Notifications.vue')
const ClientPaymentDetail = () => import('./Pages/Client/PaymentDetail.vue')
const ForgotPassword = () => import('./Pages/ForgotPassword.vue')
const BookingMessages = () => import('./Pages/BookingMessages.vue')
const VerificationCenter = () => import('./Pages/VerificationCenter.vue')
const PublicVerification = () => import('./Pages/PublicVerification.vue')
const LocationPhotographers = () => import('./Pages/LocationPhotographers.vue')
const CategoryPhotographers = () => import('./Pages/CategoryPhotographers.vue')
const CategoriesLanding = () => import('./Pages/CategoriesLanding.vue')
const LocationsLanding = () => import('./Pages/LocationsLanding.vue')
const PhotographerTips = () => import('./Pages/PhotographerTips.vue')
const JudgeDashboardComponent = () => import('./components/Judge/JudgeDashboard.vue')
const JudgeCompetitionsComponent = () => import('./components/Judge/JudgeCompetitions.vue')
const JudgeScoringFormComponent = () => import('./components/Judge/JudgeScoringForm.vue')
const JudgesManagement = () => import('./components/admin/pages/JudgesManagement.vue')
const SponsorsManagement = () => import('./components/admin/pages/SponsorsManagement.vue')
const ReviewsManagement = () => import('./components/admin/pages/ReviewsManagement.vue')
const BookingsManagement = () => import('./components/admin/pages/BookingsManagement.vue')
const TransactionsManagement = () => import('./components/admin/pages/TransactionsManagement.vue')
const HashtagsManagement = () => import('./components/admin/pages/HashtagsManagement.vue')
const NotificationsManagement = () => import('./components/admin/pages/NotificationsManagement.vue')
const ErrorCenterManagement = () => import('./components/admin/pages/ErrorCenterManagement.vue')
const ShareFramesManagement = () => import('./components/admin/pages/ShareFramesManagement.vue')
const ActivityLogsManagement = () => import('./components/admin/pages/ActivityLogsManagement.vue')

// Routes
const routes = [
    {
        path: '/',
        component: PhotographerSearch,
        name: 'home',
    },
    {
        path: '/photographers/by-location',
        component: LocationPhotographers,
        name: 'photographers-by-location',
    },
    {
        path: '/locations',
        component: LocationsLanding,
        name: 'locations-landing',
    },
    {
        path: '/photographers/by-category',
        component: CategoryPhotographers,
        name: 'photographers-by-category',
    },
    {
        path: '/categories',
        component: CategoriesLanding,
        name: 'categories-landing',
    },
    {
        path: '/photographers',
        component: PhotographerSearch,
        name: 'photographers',
    },
    {
        path: '/photographer/:slug/tips',
        component: PhotographerTips,
        name: 'photographer-tips',
    },
    {
        path: '/photographer/:slug',
        component: PhotographerProfile,
        name: 'photographer-profile',
    },
    {
        path: '/photographers/:slug',
        redirect: to => `/photographer/${to.params.slug}`,
    },
    {
        path: '/booking/:id',
        component: BookingForm,
        name: 'booking',
        meta: { requiresAuth: true },
    },
    {
        path: '/review/:photographerId',
        component: ReviewForm,
        name: 'review',
        meta: { requiresAuth: true },
    },
    {
        path: '/payment/:bookingId',
        component: PaymentCheckout,
        name: 'payment',
        meta: { requiresAuth: true },
    },
    {
        path: '/payment/success',
        component: PaymentSuccess,
        name: 'payment-success',
        meta: { requiresAuth: true },
    },
    {
        path: '/payment/failed',
        component: PaymentFailed,
        name: 'payment-failed',
        meta: { requiresAuth: true },
    },
    {
        path: '/payment/cancelled',
        component: PaymentCancelled,
        name: 'payment-cancelled',
        meta: { requiresAuth: true },
    },
    {
        path: '/transactions',
        component: TransactionHistory,
        name: 'transactions',
        meta: { requiresAuth: true },
    },
    {
        path: '/notifications',
        component: NotificationsInbox,
        name: 'notifications',
        meta: { requiresAuth: true },
    },
    {
        path: '/bookings',
        component: Bookings,
        name: 'bookings',
        meta: { requiresAuth: true },
    },
    {
        path: '/bookings/:bookingId/messages',
        component: BookingMessages,
        name: 'booking-messages',
        meta: { requiresAuth: true },
    },
    {
        path: '/verification',
        component: VerificationCenter,
        name: 'verification-center',
        meta: { requiresAuth: true },
    },
    {
        path: '/verify/:slug',
        component: PublicVerification,
        name: 'public-verification',
    },
    {
        path: '/judge/dashboard',
        component: JudgeDashboardComponent,
        name: 'judge-dashboard',
        meta: { requiresAuth: true },
    },
    {
        path: '/judge/competition/:competitionId',
        component: JudgeCompetitionsComponent,
        name: 'judge.competition',
        meta: { requiresAuth: true },
    },
    {
        path: '/judge/submission/:competitionId/:submissionId/score',
        component: JudgeScoringFormComponent,
        name: 'judge.score-submission',
        meta: { requiresAuth: true },
    },
    {
        path: '/auth',
        component: Auth,
        name: 'auth',
    },
    {
        path: '/login',
        component: Auth,
        name: 'login',
    },
    {
        path: '/admin/login',
        component: Auth,
        name: 'admin-login',
    },
    {
        path: '/forgot-password',
        component: ForgotPassword,
        name: 'forgot-password',
    },
    {
        path: '/events',
        component: Events,
        name: 'events',
    },
    {
        path: '/events/:slug',
        component: EventDetail,
        name: 'event-detail',
    },
    {
        path: '/events/:slug/tickets',
        component: EventTickets,
        name: 'event-tickets',
        meta: { requiresAuth: true },
    },
    {
        path: '/competitions',
        component: Competitions,
        name: 'competitions',
    },
    {
        path: '/competitions/:slug',
        component: CompetitionDetail,
        name: 'competition-detail',
    },
    {
        path: '/competitions/:slug/gallery',
        component: CompetitionGallery,
        name: 'competition-gallery',
    },
    {
        path: '/competitions/:slug/leaderboard',
        component: CompetitionGallery,
        name: 'competition-leaderboard',
    },
    {
        path: '/competitions/:slug/submissions/:submissionId',
        component: SubmissionDetail,
        name: 'submission-detail',
    },
    {
        path: '/competitions/:slug/submit',
        component: CompetitionSubmit,
        name: 'competition-submit',
        meta: { requiresAuth: true },
    },
    {
        path: '/competitions/:slug/judge',
        component: JudgeScoring,
        name: 'judge-scoring',
        meta: { requiresAuth: true },
    },
    {
        path: '/competitions/:slug/winners',
        component: WinnerAnnouncement,
        name: 'winner-announcement',
    },
    {
        path: '/client/dashboard',
        component: ClientDashboard,
        name: 'client-dashboard',
        meta: { requiresAuth: true, requiresClient: true },
    },
    {
        path: '/client/galleries',
        component: ClientGalleries,
        name: 'client-galleries',
        meta: { requiresAuth: true, requiresClient: true },
    },
    {
        path: '/client/galleries/:albumId',
        component: ClientGalleryShow,
        name: 'client-gallery-show',
        meta: { requiresAuth: true, requiresClient: true },
    },
    {
        path: '/client/favorites',
        component: ClientFavorites,
        name: 'client-favorites',
        meta: { requiresAuth: true, requiresClient: true },
    },
    {
        path: '/client/payments',
        component: ClientPayments,
        name: 'client-payments',
        meta: { requiresAuth: true, requiresClient: true },
    },
    {
        path: '/client/payments/:transactionId',
        component: ClientPaymentDetail,
        name: 'client-payment-detail',
        meta: { requiresAuth: true, requiresClient: true },
    },
    {
        path: '/transaction/:transactionId',
        component: ClientPaymentDetail,
        name: 'payment-detail',
        meta: { requiresAuth: true },
    },
    {
        path: '/client/notifications',
        component: ClientNotifications,
        name: 'client-notifications',
        meta: { requiresAuth: true, requiresClient: true },
    },
    {
        path: '/admin/competitions/:slug/winners',
        component: WinnerAnnouncement,
        name: 'admin-competition-winners',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/dashboard',
        component: PhotographerDashboard,
        name: 'photographer-dashboard',
        meta: { requiresAuth: true },
    },
    {
        path: '/photographer/dashboard',
        component: PhotographerDashboard,
        name: 'photographer-dashboard-alias',
        meta: { requiresAuth: true },
    },
    {
        path: '/photographer/onboarding',
        component: PhotographerSettings,
        name: 'photographer-onboarding',
        meta: { requiresAuth: true },
    },
    {
        path: '/photographer/achievements',
        component: PhotographerAchievements,
        name: 'photographer-achievements',
        meta: { requiresAuth: true },
    },
    {
        path: '/admin/dashboard',
        component: AdminDashboard,
        name: 'admin-dashboard',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/analytics',
        component: AdminAnalytics,
        name: 'admin-analytics',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/roles',
        component: AdminRolesPermissions,
        name: 'admin-roles',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/approvals',
        component: AdminApprovals,
        name: 'admin-approvals',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/system-health',
        component: AdminSystemHealth,
        name: 'admin-system-health',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/system-health/sitemap',
        component: AdminSystemHealthSitemap,
        name: 'admin-system-health-sitemap',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/settings/general',
        component: AdminSettingsGeneral,
        name: 'admin-settings-general',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/profile',
        component: AdminProfile,
        name: 'admin-profile',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/settings/account',
        component: AdminSettingsAccount,
        name: 'admin-settings-account',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/verification-documents',
        component: AdminVerificationDocuments,
        name: 'admin-verification-documents',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/events/attendance',
        component: AdminEventAttendance,
        name: 'admin-event-attendance',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/event-albums',
        component: AdminEventAlbums,
        name: 'admin-event-albums',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/submissions',
        component: AdminSubmissions,
        name: 'admin-submissions',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/sponsorships',
        component: AdminSponsorships,
        name: 'admin-sponsorships',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/feedback',
        component: AdminFeedback,
        name: 'admin-feedback',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/complaints',
        component: AdminComplaints,
        name: 'admin-complaints',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/payouts',
        component: AdminPayouts,
        name: 'admin-payouts',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/backups',
        component: AdminBackups,
        name: 'admin-backups',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/settings/seo',
        component: AdminSEOSettings,
        name: 'admin-seo-settings',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/settings/email-templates',
        component: AdminEmailTemplates,
        name: 'admin-email-templates',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/scoring',
        component: AdminScoringSystem,
        name: 'admin-scoring-system',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/notifications',
        component: AdminNotificationCenter,
        name: 'admin-notifications',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/data-hub',
        component: AdminDataHubPage,
        name: 'admin-data-hub',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/competitions',
        component: AdminCompetitionsIndex,
        name: 'admin-competitions-index',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/competitions/submissions',
        component: SubmissionModeration,
        name: 'admin-all-submissions',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/competitions/create',
        component: AdminCompetitionsCreate,
        name: 'admin-competitions-create',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/competitions/:id',
        component: AdminCompetitionsShow,
        name: 'admin-competitions-show',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/competitions/:id/edit',
        component: AdminCompetitionsEdit,
        name: 'admin-competitions-edit',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/competitions/:id/submissions',
        component: SubmissionModeration,
        name: 'admin-submissions-moderation',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/events',
        component: AdminEventsIndex,
        name: 'admin-events-index',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/events/create',
        component: AdminEventsCreate,
        name: 'admin-events-create',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/events/edit/:id',
        component: AdminEventsEdit,
        name: 'admin-events-edit',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/events/:id/check-in',
        component: AdminEventCheckIn,
        name: 'admin-events-check-in',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    // Placeholder routes for admin navigation (to be implemented)
    {
        path: '/admin/users',
        component: AdminUsersIndex,
        name: 'admin-users',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/pending-users',
        redirect: '/admin/approvals',
    },
    {
        path: '/admin/bookings',
        component: AdminBookingsIndex,
        name: 'admin-bookings',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/reviews',
        component: AdminReviewsIndex,
        name: 'admin-reviews',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/transactions',
        component: AdminTransactionsIndex,
        name: 'admin-transactions',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/verifications',
        component: AdminVerificationsIndex,
        name: 'admin-verifications',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/photographers',
        component: AdminPhotographersIndex,
        name: 'admin-photographers',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/featured-photographers',
        component: AdminFeaturedPhotographersIndex,
        name: 'admin-featured-photographers',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/payments',
        component: AdminPaymentsIndex,
        name: 'admin-payments',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/settings',
        component: AdminSettingsIndex,
        name: 'admin-settings',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/certificates',
        component: AdminCertificatesIndex,
        name: 'admin-certificates',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/certificates/manual-issuance',
        component: AdminCertificatesManualIssuance,
        name: 'admin-certificates-manual-issuance',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/certificates/templates',
        component: AdminCertificatesTemplates,
        name: 'admin-certificates-templates',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/settings/changes',
        component: AdminSettingsChangeTracking,
        name: 'admin-settings-changes',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/share-frames',
        component: AdminShareFrameGenerator,
        name: 'admin-share-frames',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/audit-logs',
        component: AdminAuditLogsIndex,
        name: 'admin-audit-logs',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/sponsors',
        component: AdminSponsorsPage,
        name: 'admin-sponsors',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/contact-messages',
        component: AdminContactMessagesIndex,
        name: 'admin-contact-messages',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/notices',
        component: AdminNoticesIndex,
        name: 'admin-notices',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/mentors',
        component: AdminMentorsIndex,
        name: 'admin-mentors',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/mentors/create',
        component: AdminMentorsCreate,
        name: 'admin-mentors-create',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/mentors/:id/edit',
        component: AdminMentorsEdit,
        name: 'admin-mentors-edit',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/mentors/:id',
        component: AdminMentorsShow,
        name: 'admin-mentors-show',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/judges',
        component: AdminJudgesIndex,
        name: 'admin-judges',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/judges/create',
        component: AdminJudgesCreate,
        name: 'admin-judges-create',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/judges/:id/edit',
        component: AdminJudgesEdit,
        name: 'admin-judges-edit',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/judges/:id',
        component: AdminJudgesShow,
        name: 'admin-judges-show',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/activity-logs',
        component: AdminActivityLogsIndex,
        name: 'admin-activity-logs',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/error-center',
        component: AdminErrorCenter,
        name: 'admin-error-center',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/seo',
        redirect: '/admin/settings/seo',
    },
    {
        path: '/admin/cities',
        redirect: '/admin/locations',
    },
    {
        path: '/admin/locations',
        component: AdminLocationsPage,
        name: 'admin-locations',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/categories',
        component: AdminCategoriesIndex,
        name: 'admin-categories',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/photo-categories',
        component: AdminPhotoCategoriesPage,
        name: 'admin-photo-categories',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/hashtags',
        component: AdminHashtagsPage,
        name: 'admin-hashtags',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/user-approval',
        redirect: '/admin/approvals',
    },
    {
        path: '/admin/sitemap',
        redirect: '/admin/system-health/sitemap',
    },
    {
        path: '/admin/hashtags/featured',
        component: AdminFeaturedHashtagsPage,
        name: 'admin-hashtags-featured',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    // Footer Pages
    {
        path: '/about',
        component: About,
        name: 'about',
    },
    {
        path: '/how-it-works',
        component: HowItWorks,
        name: 'how-it-works',
    },
    {
        path: '/pricing',
        component: Pricing,
        name: 'pricing',
    },
    {
        path: '/contact',
        component: Contact,
        name: 'contact',
    },
    {
        path: '/help',
        component: HelpCenter,
        name: 'help',
    },
    {
        path: '/privacy',
        component: Privacy,
        name: 'privacy',
    },
    {
        path: '/terms',
        component: Terms,
        name: 'terms',
    },
    {
        path: '/cookies',
        component: Cookies,
        name: 'cookies',
    },
    {
        path: '/become-sponsor',
        component: BecomeSponsor,
        name: 'become-sponsor',
    },
    {
        path: '/be-featured',
        component: BeFeautured,
        name: 'be-featured',
    },
    {
        path: '/featured-photographer/payment/:id',
        component: FeaturedPhotographerPayment,
        name: 'featured-photographer-payment',
        meta: { requiresAuth: true },
    },
    {
        path: '/featured-photographer/analytics/:id',
        component: FeaturedPhotographerAnalytics,
        name: 'featured-photographer-analytics',
        meta: { requiresAuth: true },
    },
    {
        path: '/featured-photographer/upgrade/:id',
        component: FeaturedPhotographerUpgrade,
        name: 'featured-photographer-upgrade',
        meta: { requiresAuth: true },
    },
    {
        path: '/settings',
        component: Settings,
        name: 'settings',
        meta: { requiresAuth: true },
    },
    {
        path: '/photographer/settings',
        component: PhotographerSettings,
        name: 'photographer-settings',
        meta: { requiresAuth: true },
    },
    // Catch-all for @username or direct username access (must be last)
    {
        path: '/:username',
        redirect: to => {
            const username = to.params.username;
            // Skip if it looks like a system route
            const systemRoutes = ['admin', 'api', 'auth', 'payment', 'sitemap', 'storage', 'dashboard', 'photographer', 'competitions', 'events', 'bookings', 'reviews', 'help', 'privacy', 'terms', 'become-sponsor', 'be-featured', 'settings'];
            if (systemRoutes.includes(username)) {
                return to.path;
            }
            // Redirect to photographer profile
            return `/photographer/${username}`;
        }
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

const CLICK_ENDPOINT = '/api/v1/clicks/batch'
const CLICK_SESSION_KEY = 'click_session_id'
const CLICK_BATCH_SIZE = 20
const CLICK_FLUSH_INTERVAL_MS = 2000
const clickQueue = []
let clickFlushTimer = null

const getClickSessionId = () => {
    let sessionId = localStorage.getItem(CLICK_SESSION_KEY)
    if (!sessionId) {
        if (window.crypto && typeof window.crypto.randomUUID === 'function') {
            sessionId = window.crypto.randomUUID()
        } else {
            sessionId = `${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 10)}`
        }
        localStorage.setItem(CLICK_SESSION_KEY, sessionId)
    }
    return sessionId
}

const normalizeText = (value, maxLength = 200) => {
    if (!value) return ''
    const text = String(value).replace(/\s+/g, ' ').trim()
    return text.length > maxLength ? text.slice(0, maxLength) : text
}

const getElementInfo = (target) => {
    const element = target?.closest
        ? target.closest('a,button,input,textarea,select,label,[role="button"]') || target
        : target

    if (!element) return {}

    const tag = element.tagName ? element.tagName.toLowerCase() : null
    const elementText = normalizeText(
        element.getAttribute?.('aria-label') || element.innerText || element.textContent,
        200
    )
    const elementClasses = typeof element.className === 'string'
        ? normalizeText(element.className, 500)
        : ''
    const elementId = element.id || null
    const elementName = element.getAttribute?.('name') || null
    const elementType = element.getAttribute?.('type') || null

    let inputValue = null
    if (tag === 'input' || tag === 'textarea' || tag === 'select') {
        const inputType = (elementType || '').toLowerCase()
        if (inputType === 'password') {
            inputValue = '[REDACTED]'
        } else if (inputType === 'checkbox' || inputType === 'radio') {
            inputValue = String(element.checked)
        } else {
            inputValue = String(element.value ?? '')
        }
    }

    return {
        element_tag: tag,
        element_id: elementId || null,
        element_classes: elementClasses || null,
        element_text: elementText || null,
        element_name: elementName || null,
        element_type: elementType || null,
        input_value: inputValue,
    }
}

const buildClickHeaders = () => {
    const headers = { 'Content-Type': 'application/json' }
    
    // Extract CSRF token from cookie for stateful API requests
    if (typeof document !== 'undefined' && document.cookie) {
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
        if (match && match[1]) {
            headers['X-XSRF-TOKEN'] = decodeURIComponent(match[1])
        }
    }
    
    return headers
}

const sendClickBatch = async (events, useBeacon = false) => {
    if (!events.length) return

    const body = JSON.stringify({ events })

    if (useBeacon && navigator.sendBeacon) {
        try {
            const blob = new Blob([body], { type: 'application/json' })
            navigator.sendBeacon(CLICK_ENDPOINT, blob)
            return
        } catch (error) {
            // Fallback to fetch
        }
    }

    try {
        await fetch(CLICK_ENDPOINT, {
            method: 'POST',
            headers: buildClickHeaders(),
            body,
            credentials: 'include',
            keepalive: true,
        })
    } catch (error) {
        // Ignore tracking errors
    }
}

const flushClickQueue = async (useBeacon = false) => {
    if (!clickQueue.length) return
    const batch = clickQueue.splice(0, CLICK_BATCH_SIZE)
    await sendClickBatch(batch, useBeacon)

    if (clickQueue.length) {
        scheduleClickFlush()
    }
}

const scheduleClickFlush = () => {
    if (clickFlushTimer) return
    clickFlushTimer = window.setTimeout(async () => {
        clickFlushTimer = null
        await flushClickQueue()
    }, CLICK_FLUSH_INTERVAL_MS)
}

const setupClickTracking = (routerInstance) => {
    const sessionId = getClickSessionId()

    document.addEventListener('click', (event) => {
        const info = getElementInfo(event.target)
        const payload = {
            session_id: sessionId,
            page_url: window.location.href,
            route_name: routerInstance?.currentRoute?.value?.name || null,
            click_x: typeof event.clientX === 'number' ? event.clientX : null,
            click_y: typeof event.clientY === 'number' ? event.clientY : null,
            occurred_at: new Date().toISOString(),
            ...info,
        }

        clickQueue.push(payload)

        if (clickQueue.length >= CLICK_BATCH_SIZE) {
            flushClickQueue()
        } else {
            scheduleClickFlush()
        }
    }, true)

    window.addEventListener('beforeunload', () => {
        flushClickQueue(true)
    })
}

setupClickTracking(router)

// Navigation guard
const normalizeRole = (role) => String(role || '').toLowerCase().replace(/[\s-]+/g, '_')

const hydrateUserFromApi = async () => {
    try {
        const { data } = await api.get('/auth/me')
        const user = data?.data || data?.user || data
        if (user?.role) {
            localStorage.setItem('user', JSON.stringify(user))
            localStorage.setItem('user_role', normalizeRole(user.role))
            return normalizeRole(user.role)
        }
    } catch (error) {
        // If lookup fails, treat as unauthenticated and continue.
    }

    return ''
}

router.beforeEach(async (to, from, next) => {
    const user = JSON.parse(localStorage.getItem('user') || '{}')
    let userRole = normalizeRole(localStorage.getItem('user_role') || user.role)
    const isAdminRoute = to.path.startsWith('/admin')
    const shouldHydrate = Boolean(
        userRole ||
        to.meta?.requiresAuth ||
        to.meta?.requiresAdmin ||
        isAdminRoute ||
        to.path.startsWith('/dashboard') ||
        to.path.startsWith('/judge') ||
        to.path.startsWith('/client')
    )

    if (!userRole && shouldHydrate) {
        userRole = await hydrateUserFromApi()
    }

    const isAuthenticated = Boolean(userRole)

    if (to.path === '/bookings' && ['photographer', 'judge', 'admin', 'super_admin', 'moderator'].includes(userRole)) {
        if (userRole === 'judge') {
            return next('/judge/dashboard')
        }
        if (userRole === 'photographer') {
            return next('/dashboard')
        }
        return next('/admin/dashboard')
    }

    if (to.path === '/settings' && userRole === 'photographer') {
        return next('/photographer/settings')
    }

    // Redirect authenticated users from auth/login pages to their dashboard
    if (['/auth', '/login', '/admin/login'].includes(to.path) && userRole) {
        if (userRole && ['admin', 'super_admin', 'moderator'].includes(userRole)) {
            return next('/admin/dashboard')
        } else if (userRole === 'photographer') {
            return next('/dashboard')
        } else if (userRole === 'client') {
            return next('/client/dashboard')
        } else if (userRole) {
            return next('/')
        } else if (isAdminRoute) {
            window.location.href = '/403'
            return
        }
    }

    if (to.meta.requiresAuth && !isAuthenticated) {
        return next(isAdminRoute ? '/admin/login' : '/auth')
    }

    const hasAdminRole = ['admin', 'super_admin', 'moderator'].includes(userRole)
    if (to.meta.requiresAdmin && !hasAdminRole) {
        if (!userRole) {
            return next('/admin/login')
        }
        window.location.href = '/403'
        return
    }

    if (to.meta.requiresClient && userRole !== 'client') {
        if (!userRole) {
            return next('/auth')
        }
        if (userRole === 'photographer') {
            return next('/dashboard')
        }
        if (userRole === 'judge') {
            return next('/judge/dashboard')
        }
        if (hasAdminRole) {
            return next('/admin/dashboard')
        }
        return next('/')
    }

    return next()
})

const app = createApp(App)

// Register lazy loading directive
app.directive('lazy', lazyload)

// Register global components
app.component('CookieConsentBanner', CookieConsentBanner)

app.use(router)

app.mount('#app')
