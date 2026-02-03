import './bootstrap'
import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import lazyload from './directives/lazyload'

// Components
import App from './App.vue'
const PhotographerSearch = () => import('./components/PhotographerSearch.vue')
const PhotographerProfile = () => import('./components/PhotographerProfile.vue')
const BookingForm = () => import('./components/BookingForm.vue')
const Auth = () => import('./components/Auth.vue')
const AdminDashboard = () => import('./components/AdminDashboardEnhanced.vue')
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
const PhotographerDashboard = () => import('./components/PhotographerDashboard.vue')
const PhotographerAchievements = () => import('./pages/PhotographerAchievements.vue')
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
const AdminUserApprovalIndex = () => import('./Pages/Admin/UserApproval/Index.vue')
const AdminErrorCenter = () => import('./Pages/Admin/ErrorCenter.vue')
const AdminSeoIndex = () => import('./Pages/Admin/SEO/Index.vue')
const AdminCitiesIndex = () => import('./Pages/Admin/Cities/Index.vue')
const AdminCategoriesIndex = () => import('./Pages/Admin/Categories/Index.vue')
const AdminSponsors = () => import('./components/AdminSponsors.vue')
const AdminContactMessages = () => import('./components/AdminContactMessages.vue')
const About = () => import('./Pages/About.vue')
const HowItWorks = () => import('./Pages/HowItWorks.vue')
const Contact = () => import('./Pages/Contact.vue')
const HelpCenter = () => import('./Pages/HelpCenter.vue')
const Privacy = () => import('./Pages/Privacy.vue')
const Terms = () => import('./Pages/Terms.vue')
const Settings = () => import('./Pages/Settings.vue')
const BecomeSponsor = () => import('./Pages/BecomeSponsor.vue')
const Bookings = () => import('./Pages/Bookings.vue')
const ForgotPassword = () => import('./Pages/ForgotPassword.vue')
const BookingMessages = () => import('./Pages/BookingMessages.vue')
const VerificationCenter = () => import('./Pages/VerificationCenter.vue')
const PublicVerification = () => import('./Pages/PublicVerification.vue')
const LocationPhotographers = () => import('./Pages/LocationPhotographers.vue')
const CategoryPhotographers = () => import('./Pages/CategoryPhotographers.vue')
const CategoriesLanding = () => import('./Pages/CategoriesLanding.vue')
const LocationsLanding = () => import('./Pages/LocationsLanding.vue')
const JudgeDashboardComponent = () => import('./components/Judge/JudgeDashboard.vue')
const JudgeCompetitionsComponent = () => import('./components/Judge/JudgeCompetitions.vue')
const JudgeScoringFormComponent = () => import('./components/Judge/JudgeScoringForm.vue')

// Routes
const routes = [
    {
        path: '/',
        component: PhotographerSearch,
        name: 'home',
    },
    {
        path: '/photographer',
        component: PhotographerSearch,
        name: 'photographer-list',
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
        redirect: '/photographer',
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
        path: '/dashboard',
        component: PhotographerDashboard,
        name: 'photographer-dashboard',
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
        path: '/admin/data-hub',
        component: AdminDataHub,
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
        component: AdminUserApprovalIndex,
        name: 'admin-pending-users',
        meta: { requiresAuth: true, requiresAdmin: true },
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
        path: '/admin/notifications',
        component: NotificationsInbox,
        name: 'admin-notifications',
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
        component: AdminSponsors,
        name: 'admin-sponsors',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/contact-messages',
        component: AdminContactMessages,
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
        path: '/admin/error-center',
        component: AdminErrorCenter,
        name: 'admin-error-center',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/seo',
        component: AdminSeoIndex,
        name: 'admin-seo',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/cities',
        component: AdminCitiesIndex,
        name: 'admin-cities',
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
        component: AdminPhotoCategoryManagement,
        name: 'admin-photo-categories',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/hashtags',
        component: AdminHashtagManagement,
        name: 'admin-hashtags',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/user-approval',
        component: () => import('./Pages/Admin/UserApproval/Index.vue'),
        name: 'admin-user-approval',
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
        path: '/become-sponsor',
        component: BecomeSponsor,
        name: 'become-sponsor',
    },
    {
        path: '/settings',
        component: Settings,
        name: 'settings',
        meta: { requiresAuth: true },
    },
    // Catch-all for @username or direct username access (must be last)
    {
        path: '/:username',
        redirect: to => {
            const username = to.params.username;
            // Skip if it looks like a system route
            const systemRoutes = ['admin', 'api', 'auth', 'payment', 'sitemap', 'storage', 'dashboard', 'photographer', 'competitions', 'events', 'bookings', 'reviews', 'help', 'privacy', 'terms', 'become-sponsor', 'settings'];
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

// Navigation guard
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token')
    const user = JSON.parse(localStorage.getItem('user') || '{}')

    // Redirect authenticated users from auth page to their dashboard
    if (to.path === '/auth' && token && user.role) {
        if (['admin', 'super_admin'].includes(user.role)) {
            return next('/admin/dashboard')
        } else if (user.role === 'photographer') {
            return next('/dashboard')
        } else {
            return next('/')
        }
    }

    // Redirect root path for authenticated users to their dashboard
    if (to.path === '/' && token && user.role) {
        if (['admin', 'super_admin'].includes(user.role)) {
            return next('/admin/dashboard')
        } else if (user.role === 'photographer') {
            return next('/dashboard')
        } else {
            return next()
        }
    }

    if (to.meta.requiresAuth && !token) {
        return next('/auth')
    } else if (to.meta.requiresAdmin && !['admin', 'super_admin'].includes(user.role)) {
        return next('/')
    } else {
        return next()
    }
})

const app = createApp(App)

// Register lazy loading directive
app.directive('lazy', lazyload)

app.use(router)

app.mount('#app')
