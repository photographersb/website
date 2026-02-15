import { describe, it, expect, beforeEach, afterEach } from 'vitest';

describe('Admin Dashboard', () => {
    let token;
    const baseUrl = 'http://localhost:8000/api/v1';

    beforeEach(async () => {
        // Login as admin
        const loginResponse = await fetch(`${baseUrl}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: 'admin@photographar.com',
                password: 'password'
            })
        });
        const loginData = await loginResponse.json();
        token = loginData.data.token;
    });

    it('should load admin dashboard', async () => {
        const response = await fetch(`${baseUrl}/admin/dashboard`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        expect(response.status).toBe(200);
        expect(data.status).toBe('success');
        expect(data.data).toBeDefined();
        expect(data.data.stats).toBeDefined();
    });

    it('should have required dashboard stats', async () => {
        const response = await fetch(`${baseUrl}/admin/dashboard`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        const stats = data.data.stats;

        expect(stats.total_users).toBeGreaterThanOrEqual(0);
        expect(stats.total_photographers).toBeGreaterThanOrEqual(0);
        expect(stats.total_competitions).toBeGreaterThanOrEqual(0);
        expect(stats.total_bookings).toBeGreaterThanOrEqual(0);
    });

    it('should return competitions data', async () => {
        const response = await fetch(`${baseUrl}/admin/dashboard`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        expect(data.data.recent_competitions).toBeDefined();
        expect(Array.isArray(data.data.recent_competitions)).toBe(true);
    });
});

describe('Notices API', () => {
    let adminToken;
    let userToken;
    const baseUrl = 'http://localhost:8000/api/v1';

    beforeEach(async () => {
        // Login as admin
        const adminLogin = await fetch(`${baseUrl}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: 'admin@photographar.com',
                password: 'password'
            })
        });
        const adminData = await adminLogin.json();
        adminToken = adminData.data.token;

        // Login as regular user (photographer)
        const userLogin = await fetch(`${baseUrl}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: 'photographer@example.com',
                password: 'password'
            })
        });
        const userData = await userLogin.json();
        userToken = userData.data.token;
    });

    it('should create a notice', async () => {
        const response = await fetch(`${baseUrl}/admin/notices`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                title: 'Test Notice',
                message: 'This is a test notice',
                priority: 'normal',
                status: 'published',
                show_to_all_roles: true
            })
        });

        const data = await response.json();
        expect(response.status).toBe(201);
        expect(data.status).toBe('success');
        expect(data.data.id).toBeDefined();
    });

    it('should get user notices (role-based)', async () => {
        const response = await fetch(`${baseUrl}/notices/my-notices`, {
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        expect(response.status).toBe(200);
        expect(data.status).toBe('success');
        expect(Array.isArray(data.data)).toBe(true);
    });

    it('should mark notice as read', async () => {
        // Get user's notices first
        const noticesResponse = await fetch(`${baseUrl}/notices/my-notices`, {
            headers: {
                'Authorization': `Bearer ${userToken}`,
                'Accept': 'application/json'
            }
        });

        const noticesData = await noticesResponse.json();
        if (noticesData.data.length > 0) {
            const noticeId = noticesData.data[0].id;

            // Mark as read
            const readResponse = await fetch(`${baseUrl}/notices/${noticeId}/read`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${userToken}`,
                    'Accept': 'application/json'
                }
            });

            const readData = await readResponse.json();
            expect(readData.status).toBe('success');
        }
    });
});

describe('SEO Meta API', () => {
    let adminToken;
    const baseUrl = 'http://localhost:8000/api/v1';

    beforeEach(async () => {
        const response = await fetch(`${baseUrl}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: 'admin@photographar.com',
                password: 'password'
            })
        });
        const data = await response.json();
        adminToken = data.data.token;
    });

    it('should get SEO meta for an entity', async () => {
        const response = await fetch(`${baseUrl}/admin/seo?model_type=Photographer&model_id=1`, {
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        expect(response.status).toBe(200);
        expect(data.status).toBe('success');
    });

    it('should auto-generate SEO meta', async () => {
        const response = await fetch(`${baseUrl}/admin/seo/generate`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                model_type: 'Photographer',
                model_id: 1
            })
        });

        const data = await response.json();
        expect(response.status).toBe(200);
        expect(data.status).toBe('success');
        expect(data.data).toBeDefined();
    });

    it('should update SEO meta', async () => {
        const response = await fetch(`${baseUrl}/admin/seo`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                model_type: 'Photographer',
                model_id: 1,
                meta_title: 'Professional Photographer',
                meta_description: 'Hire a professional photographer',
                meta_keywords: 'photography, photographer, professional'
            })
        });

        const data = await response.json();
        expect(response.status).toBe(200);
        expect(data.status).toBe('success');
    });

    it('should preview SEO meta', async () => {
        const response = await fetch(`${baseUrl}/admin/seo/preview`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${adminToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                model_type: 'Photographer',
                model_id: 1
            })
        });

        const data = await response.json();
        expect(response.status).toBe(200);
        expect(data.status).toBe('success');
        expect(data.data.title).toBeDefined();
        expect(data.data.description).toBeDefined();
    });
});
