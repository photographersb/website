/**
 * ADMIN HQ MENU CONFIGURATION
 * Central single source of truth for admin navigation
 * Last Updated: February 4, 2026
 */

export const adminMenuConfig = {
  dashboard: {
    section: 'Dashboard & Analytics',
    icon: 'LayoutDashboard',
    color: 'from-blue-600 to-blue-400',
    items: [
      {
        id: 'dashboard',
        label: 'Dashboard',
        route: '/admin/dashboard',
        icon: 'BarChart3',
        description: 'Main admin dashboard with KPIs',
        permission: 'view_dashboard',
      },
      {
        id: 'analytics',
        label: 'Analytics',
        route: '/admin/analytics',
        icon: 'TrendingUp',
        description: 'Platform analytics and metrics',
        permission: 'view_analytics',
      },
      {
        id: 'system-health',
        label: 'System Health',
        route: '/admin/system-health',
        icon: 'Activity',
        description: 'Platform health monitoring',
        permission: 'view_health',
      },
      {
        id: 'sitemap',
        label: 'Sitemap',
        route: '/admin/system-health/sitemap',
        icon: 'Map',
        description: 'SEO sitemap management',
        permission: 'view_sitemap',
      },
    ]
  },

  users: {
    section: 'Users & Access',
    icon: 'Users',
    color: 'from-purple-600 to-purple-400',
    items: [
      {
        id: 'users',
        label: 'Users',
        route: '/admin/users',
        icon: 'User',
        description: 'Manage platform users',
        permission: 'manage_users',
      },
      {
        id: 'roles',
        label: 'Roles & Permissions',
        route: '/admin/roles',
        icon: 'Shield',
        description: 'Role-based access control',
        permission: 'manage_roles',
      },
      {
        id: 'approvals',
        label: 'Approvals',
        route: '/admin/approvals',
        icon: 'CheckCircle2',
        description: 'User account approvals',
        permission: 'approve_users',
        badge: 'pending',
      },
    ]
  },

  photographers: {
    section: 'Photographers & Verification',
    icon: 'Camera',
    color: 'from-pink-600 to-pink-400',
    items: [
      {
        id: 'photographers',
        label: 'Photographers',
        route: '/admin/photographers',
        icon: 'User',
        description: 'Manage photographer profiles',
        permission: 'manage_photographers',
      },
      {
        id: 'verifications',
        label: 'Verifications',
        route: '/admin/verifications',
        icon: 'CheckCircle',
        description: 'Pending photographer verifications',
        permission: 'verify_photographers',
        badge: 'pending',
      },
      {
        id: 'verification-docs',
        label: 'Verification Documents',
        route: '/admin/verification-documents',
        icon: 'FileCheck',
        description: 'Review verification documents',
        permission: 'review_documents',
      },
      {
        id: 'featured-photographers',
        label: 'Featured Photographers',
        route: '/admin/featured-photographers',
        icon: 'Star',
        description: 'Manage featured photographer listings',
        permission: 'manage_featured',
      },
    ]
  },

  events: {
    section: 'Events & Attendance',
    icon: 'Calendar',
    color: 'from-green-600 to-green-400',
    items: [
      {
        id: 'events',
        label: 'Events',
        route: '/admin/events',
        icon: 'Calendar',
        description: 'Manage platform events',
        permission: 'manage_events',
      },
      {
        id: 'events-create',
        label: 'Events Create',
        route: '/admin/events/create',
        icon: 'PlusCircle',
        description: 'Create a new event',
        permission: 'manage_events',
      },
      {
        id: 'attendance',
        label: 'Attendance',
        route: '/admin/events/attendance',
        icon: 'Users',
        description: 'Event attendance tracking',
        permission: 'manage_attendance',
      },
      {
        id: 'event-albums',
        label: 'Event Albums',
        route: '/admin/event-albums',
        icon: 'Images',
        description: 'Manage event photo albums',
        permission: 'manage_albums',
      },
    ]
  },

  competitions: {
    section: 'Competitions & Submissions',
    icon: 'Trophy',
    color: 'from-orange-600 to-orange-400',
    items: [
      {
        id: 'competitions',
        label: 'Competitions',
        route: '/admin/competitions',
        icon: 'Trophy',
        description: 'Create and manage competitions',
        permission: 'manage_competitions',
      },
      {
        id: 'competitions-create',
        label: 'Competitions Create',
        route: '/admin/competitions/create',
        icon: 'PlusCircle',
        description: 'Create a new competition',
        permission: 'manage_competitions',
      },
      {
        id: 'submissions',
        label: 'Submissions',
        route: '/admin/submissions',
        icon: 'Upload',
        description: 'Review competition submissions',
        permission: 'review_submissions',
        badge: 'pending',
      },
      {
        id: 'competitions-submissions',
        label: 'Competitions Submissions (All)',
        route: '/admin/competitions/submissions',
        icon: 'Inbox',
        description: 'All competition submissions',
        permission: 'review_submissions',
      },
      {
        id: 'categories',
        label: 'Categories',
        route: '/admin/categories',
        icon: 'Tag',
        description: 'Competition categories',
        permission: 'manage_categories',
      },
      {
        id: 'judges',
        label: 'Judges',
        route: '/admin/judges',
        icon: 'Award',
        description: 'Manage competition judges',
        permission: 'manage_judges',
      },
      {
        id: 'judges-create',
        label: 'Judges Create',
        route: '/admin/judges/create',
        icon: 'PlusCircle',
        description: 'Add a new judge',
        permission: 'manage_judges',
      },
      {
        id: 'mentors',
        label: 'Mentors',
        route: '/admin/mentors',
        icon: 'Users',
        description: 'Manage platform mentors',
        permission: 'manage_mentors',
      },
      {
        id: 'mentors-create',
        label: 'Mentors Create',
        route: '/admin/mentors/create',
        icon: 'PlusCircle',
        description: 'Add a new mentor',
        permission: 'manage_mentors',
      },
      {
        id: 'scoring',
        label: 'Scoring System',
        route: '/admin/scoring',
        icon: 'Star',
        description: 'Configure scoring rules',
        permission: 'manage_scoring',
      },
    ]
  },

  sponsors: {
    section: 'Sponsors & Partnerships',
    icon: 'Gift',
    color: 'from-red-600 to-red-400',
    items: [
      {
        id: 'sponsors',
        label: 'Sponsors',
        route: '/admin/sponsors',
        icon: 'Gift',
        description: 'Manage event sponsors',
        permission: 'manage_sponsors',
      },
      {
        id: 'sponsorships',
        label: 'Sponsorships',
        route: '/admin/sponsorships',
        icon: 'Briefcase',
        description: 'Sponsorship agreements',
        permission: 'manage_sponsorships',
      },
    ]
  },

  reviews: {
    section: 'Reviews & Feedback',
    icon: 'MessageSquare',
    color: 'from-indigo-600 to-indigo-400',
    items: [
      {
        id: 'reviews',
        label: 'Reviews',
        route: '/admin/reviews',
        icon: 'Star',
        description: 'Manage platform reviews',
        permission: 'manage_reviews',
      },
      {
        id: 'feedback',
        label: 'Feedback',
        route: '/admin/feedback',
        icon: 'MessageSquare',
        description: 'User feedback and suggestions',
        permission: 'view_feedback',
        badge: 'new',
      },
      {
        id: 'complaints',
        label: 'Complaints',
        route: '/admin/complaints',
        icon: 'AlertCircle',
        description: 'User complaints and disputes',
        permission: 'manage_complaints',
        badge: 'urgent',
      },
    ]
  },

  bookings: {
    section: 'Bookings & Finance',
    icon: 'ShoppingCart',
    color: 'from-cyan-600 to-cyan-400',
    items: [
      {
        id: 'bookings',
        label: 'Bookings',
        route: '/admin/bookings',
        icon: 'Calendar',
        description: 'Photography service bookings',
        permission: 'manage_bookings',
      },
      {
        id: 'transactions',
        label: 'Transactions',
        route: '/admin/transactions',
        icon: 'CreditCard',
        description: 'Payment transactions',
        permission: 'view_transactions',
      },
      {
        id: 'payouts',
        label: 'Payouts',
        route: '/admin/payouts',
        icon: 'Send',
        description: 'Photographer payouts',
        permission: 'manage_payouts',
      },
      {
        id: 'payments',
        label: 'Payments',
        route: '/admin/payments',
        icon: 'Wallet',
        description: 'Payment approvals and tracking',
        permission: 'manage_payments',
      },
    ]
  },

  content: {
    section: 'Content & Discovery',
    icon: 'Search',
    color: 'from-emerald-600 to-emerald-400',
    items: [
      {
        id: 'cities',
        label: 'Cities',
        route: '/admin/locations',
        icon: 'MapPin',
        description: 'Manage cities and locations',
        permission: 'manage_locations',
      },
      {
        id: 'hashtags',
        label: 'Hashtags',
        route: '/admin/hashtags',
        icon: 'Hash',
        description: 'Trending hashtags',
        permission: 'manage_hashtags',
      },
      {
        id: 'hashtags-featured',
        label: 'Featured Hashtags',
        route: '/admin/hashtags/featured',
        icon: 'Sparkles',
        description: 'Featured hashtag sets',
        permission: 'manage_hashtags',
      },
      {
        id: 'photo-categories',
        label: 'Photo Categories',
        route: '/admin/photo-categories',
        icon: 'Image',
        description: 'Manage photo categories',
        permission: 'manage_categories',
      },
    ]
  },

  certificates: {
    section: 'Certificates & Assets',
    icon: 'Award',
    color: 'from-amber-600 to-amber-400',
    items: [
      {
        id: 'certificates',
        label: 'Certificates',
        route: '/admin/certificates',
        icon: 'Award',
        description: 'Certificate management',
        permission: 'manage_certificates',
      },
      {
        id: 'certificates-manual',
        label: 'Certificates - Manual Issuance',
        route: '/admin/certificates/manual-issuance',
        icon: 'FilePlus',
        description: 'Manually issue certificates',
        permission: 'manage_certificates',
      },
      {
        id: 'certificates-templates',
        label: 'Certificates - Templates',
        route: '/admin/certificates/templates',
        icon: 'FileText',
        description: 'Manage certificate templates',
        permission: 'manage_certificates',
      },
      {
        id: 'share-frames',
        label: 'Share Frames',
        route: '/admin/share-frames',
        icon: 'Frame',
        description: 'Share frame generator',
        permission: 'manage_assets',
      },
    ]
  },

  communications: {
    section: 'Communications',
    icon: 'Bell',
    color: 'from-violet-600 to-violet-400',
    items: [
      {
        id: 'notifications',
        label: 'Notifications',
        route: '/admin/notifications',
        icon: 'Bell',
        description: 'Notification center',
        permission: 'manage_notifications',
      },
      {
        id: 'contact-messages',
        label: 'Contact Messages',
        route: '/admin/contact-messages',
        icon: 'Mail',
        description: 'Inbound contact messages',
        permission: 'view_messages',
      },
      {
        id: 'notices',
        label: 'Notices',
        route: '/admin/notices',
        icon: 'Megaphone',
        description: 'Admin notices and announcements',
        permission: 'manage_notices',
      },
    ]
  },

  logs: {
    section: 'Monitoring & Logs',
    icon: 'Activity',
    color: 'from-lime-600 to-lime-400',
    items: [
      {
        id: 'audit-logs',
        label: 'Audit Logs',
        route: '/admin/audit-logs',
        icon: 'ClipboardList',
        description: 'Audit trail and history',
        permission: 'view_logs',
      },
      {
        id: 'activity-logs',
        label: 'Activity Logs',
        route: '/admin/activity-logs',
        icon: 'Activity',
        description: 'System activity tracking',
        permission: 'view_logs',
      },
      {
        id: 'error-center',
        label: 'Error Center',
        route: '/admin/error-center',
        icon: 'AlertTriangle',
        description: 'Error tracking and logs',
        permission: 'view_errors',
        badge: 'errors',
      },
      {
        id: 'backups',
        label: 'Backups',
        route: '/admin/backups',
        icon: 'Archive',
        description: 'Database backups',
        permission: 'manage_backups',
      },
      {
        id: 'data-hub',
        label: 'Data Hub',
        route: '/admin/data-hub',
        icon: 'Database',
        description: 'Admin data hub',
        permission: 'view_data',
      },
    ]
  },

  settings: {
    section: 'Settings & Configuration',
    icon: 'Settings',
    color: 'from-gray-600 to-gray-400',
    items: [
      {
        id: 'seo-settings',
        label: 'SEO Settings',
        route: '/admin/settings/seo',
        icon: 'Search',
        description: 'SEO configuration',
        permission: 'manage_seo',
      },
      {
        id: 'general-settings',
        label: 'General Settings',
        route: '/admin/settings/general',
        icon: 'Settings',
        description: 'Platform general settings',
        permission: 'manage_settings',
      },
      {
        id: 'site-links',
        label: 'Site Links',
        route: '/admin/settings/site-links',
        icon: 'Link',
        description: 'External site links',
        permission: 'manage_links',
      },
      {
        id: 'email-templates',
        label: 'Email Templates',
        route: '/admin/settings/email-templates',
        icon: 'Mail',
        description: 'Email notification templates',
        permission: 'manage_templates',
      },
      {
        id: 'settings-changes',
        label: 'Settings Change Tracking',
        route: '/admin/settings/changes',
        icon: 'History',
        description: 'Configuration change history',
        permission: 'manage_settings',
      },
    ]
  },
};

/**
 * HELPER: Get all menu items flattened
 */
export const getAllMenuItems = () => {
  const items = [];
  Object.values(adminMenuConfig).forEach(section => {
    items.push(...section.items);
  });
  return items;
};

/**
 * HELPER: Get menu item by ID
 */
export const getMenuItemById = (id) => {
  for (const section of Object.values(adminMenuConfig)) {
    const item = section.items.find(i => i.id === id);
    if (item) return item;
  }
  return null;
};

/**
 * HELPER: Get section by key
 */
export const getMenuSection = (key) => {
  return adminMenuConfig[key] || null;
};

/**
 * HELPER: Check if user has permission for menu item
 */
export const canAccessMenuItem = (item, userPermissions = []) => {
  if (!item.permission) return true; // No permission required
  return userPermissions.includes(item.permission);
};

/**
 * HELPER: Filter menu by user permissions
 */
export const filterMenuByPermissions = (userPermissions = []) => {
  const filtered = {};
  
  Object.entries(adminMenuConfig).forEach(([key, section]) => {
    const filteredItems = section.items.filter(item => 
      canAccessMenuItem(item, userPermissions)
    );
    
    if (filteredItems.length > 0) {
      filtered[key] = {
        ...section,
        items: filteredItems
      };
    }
  });
  
  return filtered;
};

/**
 * BADGE CONFIGURATIONS
 * Used for dynamic badge counts on menu items
 */
export const badgeConfig = {
  pending: {
    label: 'Pending',
    color: 'bg-yellow-500',
    endpoint: '/api/v1/admin/dashboard',
    field: 'pending_count'
  },
  new: {
    label: 'New',
    color: 'bg-green-500',
    endpoint: '/api/v1/admin/dashboard',
    field: 'new_count'
  },
  errors: {
    label: 'Errors',
    color: 'bg-red-500',
    endpoint: '/api/v1/admin/dashboard',
    field: 'error_count'
  },
  urgent: {
    label: 'Urgent',
    color: 'bg-red-600',
    endpoint: '/api/v1/admin/dashboard',
    field: 'urgent_count'
  }
};

export default adminMenuConfig;
