import './bootstrap'
import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import lazyload from './directives/lazyload'

// Components
import App from './App.vue'
import PhotographerSearch from './components/PhotographerSearch.vue'
import PhotographerProfile from './components/PhotographerProfile.vue'
import BookingForm from './components/BookingForm.vue'
import Auth from './components/Auth.vue'
import AdminDashboard from './components/AdminDashboard.vue'
import AdminSponsorManagement from './components/AdminSponsorManagement.vue'
import AdminPhotoCategoryManagement from './components/AdminPhotoCategoryManagement.vue'
import AdminHashtagManagement from './components/AdminHashtagManagement.vue'
import EventsList from './components/EventsList.vue'
import CompetitionsList from './components/CompetitionsList.vue'
import Competitions from './Pages/Competitions.vue'
import CompetitionDetail from './Pages/CompetitionDetail.vue'
import CompetitionSubmit from './Pages/CompetitionSubmit.vue'
import CompetitionGallery from './Pages/CompetitionGallery.vue'
import SubmissionDetail from './Pages/SubmissionDetail.vue'
import Events from './Pages/Events.vue'
import EventDetail from './Pages/EventDetail.vue'
import PhotographerDashboard from './components/PhotographerDashboard.vue'
import ReviewForm from './components/ReviewForm.vue'
import PaymentCheckout from './components/PaymentCheckout.vue'
import PaymentSuccess from './components/PaymentSuccess.vue'
import PaymentFailed from './components/PaymentFailed.vue'
import PaymentCancelled from './components/PaymentCancelled.vue'
import TransactionHistory from './components/TransactionHistory.vue'
import NotificationsInbox from './components/NotificationsInbox.vue'
import AdminCompetitionsIndex from './Pages/Admin/Competitions/Dashboard.vue'
import AdminCompetitionsCreate from './Pages/Admin/Competitions/Create.vue'
import AdminCompetitionsEdit from './Pages/Admin/Competitions/Edit.vue'
import AdminEventsIndex from './Pages/Admin/Events/Index.vue'
import AdminEventsCreate from './Pages/Admin/Events/Create.vue'
import AdminEventsEdit from './Pages/Admin/Events/Edit.vue'
import SubmissionModeration from './Pages/Admin/SubmissionModeration.vue'
import JudgeScoring from './Pages/JudgeScoring.vue'
import JudgeDashboard from './Pages/JudgeDashboard.vue'
import WinnerAnnouncement from './Pages/WinnerAnnouncement.vue'
import AdminUsersIndex from './Pages/Admin/Users/Index.vue'
import AdminPhotographersIndex from './Pages/Admin/Photographers/Index.vue'
import AdminVerificationsIndex from './Pages/Admin/Verifications/Index.vue'
import AdminBookingsIndex from './Pages/Admin/Bookings/Index.vue'
import AdminReviewsIndex from './Pages/Admin/Reviews/Index.vue'
import AdminTransactionsIndex from './Pages/Admin/Transactions/Index.vue'
import AdminPaymentsIndex from './Pages/Admin/Payments/Index.vue'
import AdminSettingsIndex from './Pages/Admin/Settings/Index.vue'
import AdminAuditLogsIndex from './Pages/Admin/AuditLogs/Index.vue'
import About from './Pages/About.vue'
import HowItWorks from './Pages/HowItWorks.vue'
import Contact from './Pages/Contact.vue'
import HelpCenter from './Pages/HelpCenter.vue'
import Privacy from './Pages/Privacy.vue'
import Terms from './Pages/Terms.vue'
import Settings from './Pages/Settings.vue'
import Bookings from './Pages/Bookings.vue'
import ForgotPassword from './Pages/ForgotPassword.vue'

// Routes
const routes = [
    {
        path: '/',
        component: PhotographerSearch,
        name: 'home',
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
        path: '/judge/dashboard',
        component: JudgeDashboard,
        name: 'judge-dashboard',
        meta: { requiresAuth: true },
    },
    {
        path: '/auth',
        component: Auth,
        name: 'auth',
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
        path: '/admin/dashboard',
        component: AdminDashboard,
        name: 'admin-dashboard',
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
    // Placeholder routes for admin navigation (to be implemented)
    {
        path: '/admin/users',
        component: AdminUsersIndex,
        name: 'admin-users',
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
        path: '/admin/audit-logs',
        component: AdminAuditLogsIndex,
        name: 'admin-audit-logs',
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/sponsors',
        component: AdminSponsorManagement,
        name: 'admin-sponsors',
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
        path: '/settings',
        component: Settings,
        name: 'settings',
        meta: { requiresAuth: true },
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
            next('/admin/dashboard')
        } else if (user.role === 'photographer') {
            next('/dashboard')
        } else {
            next('/')
        }
        return
    }

    // Redirect root path for authenticated users to their dashboard
    if (to.path === '/' && token && user.role) {
        if (['admin', 'super_admin'].includes(user.role)) {
            next('/admin/dashboard')
        } else if (user.role === 'photographer') {
            next('/dashboard')
        } else {
            next()
        }
        return
    }

    if (to.meta.requiresAuth && !token) {
        next('/auth')
    } else if (to.meta.requiresAdmin && !['admin', 'super_admin'].includes(user.role)) {
        next('/')
    } else {
        next()
    }
})

const app = createApp(App)

// Register lazy loading directive
app.directive('lazy', lazyload)

app.use(router)

app.mount('#app')
