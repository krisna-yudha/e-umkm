# 🏗️ System Architecture - Rating & User Role System

## High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        FRONTEND (Vue 3)                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────┐          ┌──────────────────────────────┐ │
│  │   UserLogin.vue  │          │   RatingSection.vue          │ │
│  │  UmkmLogin.vue   │          │  (Display & Submit Ratings)  │ │
│  └──────────────────┘          └──────────────────────────────┘ │
│         ▲                              ▲                          │
│         │                              │                          │
│         └──────────────┬───────────────┘                          │
│                        │ HTTP Requests                            │
│                        │ (axios)                                  │
└────────────────────────┼──────────────────────────────────────────┘
                         │
        ┌────────────────┴────────────────┐
        │                                 │
┌───────▼────────────────────────────┐   │
│  API Routes (routes/api.php)       │   │
├────────────────────────────────────┤   │
│                                    │   │
│  PUBLIC ROUTES:                    │   │
│  GET /api/v1/umkms/{id}/ratings   │   │
│                                    │   │
│  PROTECTED ROUTES:                 │   │
│  POST /api/v1/umkms/{id}/ratings  │   │
│  DELETE /api/v1/ratings/{id}      │   │
│  POST /api/v1/ratings/{id}/helpful│   │
│                                    │   │
│  AUTH ROUTES:                      │   │
│  POST /api/v1/auth/register       │   │
│  POST /api/v1/auth/login          │   │
│                                    │   │
└────────────────┬───────────────────┘   │
                 │                       │
        ┌────────▼───────────┐           │
        │                    │           │
┌───────▼──────────────┐  ┌──┴──────────────────────┐
│   RatingController   │  │  AuthApiController      │
│   ────────────────   │  │  ─────────────────      │
│ store()              │  │ register() + user_type  │
│ index()              │  │ login() + user_type     │
│ destroy()            │  │ Check role authorization│
│ markHelpful()        │  │                         │
└──────────┬───────────┘  └──────────┬──────────────┘
           │                        │
           │                        │
           └────────────┬───────────┘
                        │
        ┌───────────────┴──────────────────┐
        │                                  │
┌───────▼──────────────────┐   ┌──────────▼──────────────┐
│    Database (MySQL)      │   │  Eloquent Models        │
├──────────────────────────┤   ├─────────────────────────┤
│                          │   │                         │
│  USERS TABLE:            │   │ User.php:               │
│  ├─ id                   │   │ ├─ user_type (enum)    │
│  ├─ name                 │   │ ├─ ratings() relation  │
│  ├─ email                │   │ ├─ isUser()            │
│  ├─ password             │   │ └─ isUmkm()            │
│  └─ user_type (NEW)◄─────┼───┤                        │
│     • 'user'             │   │ Umkm.php:              │
│     • 'umkm'             │   │ ├─ ratings() relation  │
│                          │   │ ├─ average_rating()    │
│  RATINGS TABLE (NEW):    │   │ └─ rating_count()      │
│  ├─ id                   │   │                        │
│  ├─ umkm_id (FK)         │   │ Rating.php (NEW):      │
│  ├─ user_id (FK)         │   │ ├─ umkm relation      │
│  ├─ rating (1-5)         │   │ ├─ user relation      │
│  ├─ review (text)        │   │ ├─ helpful_count      │
│  ├─ helpful_count        │   │ └─ fillable[]         │
│  ├─ timestamps           │   │                        │
│  └─ unique(umkm,user)    │   │                        │
│                          │   │                        │
│  UMKMS TABLE:            │   │                        │
│  ├─ id                   │   │                        │
│  ├─ nama_umkm            │   │                        │
│  └─ ... (existing)       │   │                        │
│                          │   │                        │
└──────────────────────────┘   └─────────────────────────┘
```

---

## Data Flow: User Registration

```
┌──────────────┐
│ Register Form│
│  (UserRegister.vue)
└──────┬───────┘
       │ 1. Submit name, email, password
       │    user_type: 'user'
       ▼
┌──────────────────────────┐
│ AuthApiController        │
│ register()               │
└──────┬───────────────────┘
       │ 2. Validate input
       │    Hash password
       │    Check user_type
       ▼
┌──────────────────────────┐
│ Database: INSERT         │
│ users table              │
│ with user_type = 'user'  │
└──────┬───────────────────┘
       │ 3. User created
       │    Return token
       ▼
┌──────────────────────────┐
│ Frontend                 │
│ Store token              │
│ Redirect to dashboard    │
└──────────────────────────┘
```

---

## Data Flow: Submit Rating

```
┌──────────────────┐
│ RatingSection.vue│
│ Submit 5 stars   │
│ + "Great UMKM!"  │
└────────┬─────────┘
         │ 1. POST /api/v1/umkms/1/ratings
         │    {rating: 5, review: "Great UMKM!"}
         │    Authorization: Bearer {token}
         ▼
┌───────────────────────────────────────┐
│ RatingController::store()             │
├───────────────────────────────────────┤
│ 2a. Verify user is authenticated      │
│ 2b. Check user_type == 'user'         │
│ 2c. Validate rating (1-5)             │
│ 2d. Validate review (max 1000 chars)  │
│ 2e. Check no duplicate rating         │
│     (unique constraint)               │
└────────┬────────────────────────────┘
         │
         ▼ 3a. UPDATE (if exists) 3b. INSERT (if new)
┌──────────────────────────────────────┐
│ Database                             │
│ UPDATE/INSERT ratings table          │
│                                      │
│ OLD: rating_id = 5 ⟶ UPDATE         │
│      DELETE old, INSERT new          │
│                                      │
│ NEW: rating_id = null ⟶ INSERT      │
│      Create new entry                │
└────────┬─────────────────────────────┘
         │
         ▼ 4. Return saved rating
┌──────────────────────────────────────┐
│ API Response                         │
│ {                                    │
│   "message": "Rating saved",         │
│   "rating": {                        │
│     "id": 123,                       │
│     "rating": 5,                     │
│     "review": "Great UMKM!",         │
│     ...                              │
│   }                                  │
│ }                                    │
└────────┬─────────────────────────────┘
         │
         ▼ 5. Update UI
┌──────────────────────────────────────┐
│ RatingSection.vue                    │
│ 1. Reload ratings from API           │
│ 2. Update display:                   │
│    - Average rating                  │
│    - Distribution bars               │
│    - Show new review in list         │
│ 3. Show "Rating saved!" toast        │
└──────────────────────────────────────┘
```

---

## Data Flow: View Ratings (Public)

```
┌─────────────────────────┐
│ PublicUmkmShow.vue      │
│ (UMKM Detail Page)      │
└────────┬────────────────┘
         │ 1. GET /api/v1/umkms/1/ratings
         │    (NO auth required)
         ▼
┌─────────────────────────┐
│ RatingController::index()|
│ (No authorization check)│
└────────┬────────────────┘
         │ 2. Fetch ratings for UMKM
         │    Query all ratings with user info
         │    Calculate average
         │    Calculate distribution
         ▼
┌─────────────────────────┐
│ Database Query          │
│ SELECT ratings          │
│ WHERE umkm_id = 1       │
│ WITH user relations     │
└────────┬────────────────┘
         │
         ▼ 3. Calculate stats
┌─────────────────────────┐
│ Statistics Calculation  │
│ Average = SUM(rating)/  │
│           COUNT(rating) │
│ Distribution = count    │
│           by rating (1-5)
└────────┬────────────────┘
         │
         ▼ 4. Return JSON
┌─────────────────────────┐
│ API Response:           │
│ {                       │
│   "ratings": [...],     │
│   "avg_rating": 4.5,    │
│   "total": 10,          │
│   "distribution": {     │
│     "5": {count: 5, %:50}
│     "4": {count: 3, %:30}
│     ...                 │
│   }                     │
│ }                       │
└────────┬────────────────┘
         │
         ▼ 5. Render UI
┌─────────────────────────┐
│ RatingSection.vue       │
│ Display:                │
│ ⭐⭐⭐⭐ 4.5 (10 ratings)
│ ████████░ 50%  (5⭐)    │
│ ██████░░░ 30%  (4⭐)    │
│                         │
│ [User reviews list]     │
│                         │
│ [Login form if not auth]│
└─────────────────────────┘
```

---

## Authorization Matrix

```
┌────────────────────┬────────┬──────────────┬──────────────┐
│ Action             │ Public │ User (Type)  │ UMKM (Type)  │
├────────────────────┼────────┼──────────────┼──────────────┤
│ GET /ratings       │ ✅     │ ✅           │ ✅           │
│ POST /ratings      │ ❌     │ ✅ (owner)   │ ❌           │
│ DELETE /rating/:id │ ❌     │ ✅ (owner)   │ ❌           │
│ POST /helpful      │ ❌     │ ✅           │ ✅           │
│ /auth/register     │ ✅     │ ✅ (type)    │ ✅ (type)    │
│ /auth/login        │ ✅     │ ✅ (type)    │ ✅ (type)    │
└────────────────────┴────────┴──────────────┴──────────────┘

Legend:
✅ = Allowed
❌ = Forbidden
(owner) = Must be the one who created the resource
(type) = Must match user_type in request
```

---

## Component Relationship Diagram

```
App
│
├── Pages/
│   ├── Auth/
│   │   ├── UserLogin.vue ◄─────┐
│   │   ├── UserRegister.vue ◄──┤
│   │   ├── UmkmLogin.vue ◄──────┼─ Separate forms per role
│   │   └── UmkmRegister.vue ◄───┤
│   │                            │
│   └── Public/
│       └── PublicUmkmShow.vue ──┤
│           │                     │
│           └─► Components/       │
│               └── RatingSection.vue ◄─ Uses props:
│                                        • umkm
│                                        • currentUser
│
└── Models/ (Eloquent)
    ├── User
    │   └─► ratings() [HasMany]
    │
    ├── Umkm
    │   └─► ratings() [HasMany]
    │
    └── Rating (NEW)
        ├─► user [BelongsTo]
        └─► umkm [BelongsTo]
```

---

## Database Relationships

```
users (1) ◄──────────────────────► (M) ratings
│                                      │
│                                      ▼
│                            ratings.user_id (FK)
│
│
UMKMS (1) ◄──────────────────────► (M) ratings
                                        │
                                        ▼
                            ratings.umkm_id (FK)


Constraints:
• ratings.user_id → users.id (CASCADE DELETE)
• ratings.umkm_id → umkms.id (CASCADE DELETE)
• UNIQUE(ratings.umkm_id, ratings.user_id) → One rating per user per UMKM
```

---

## Form & Validation Flow

```
FORM SUBMISSION
┌──────────────────────────────────┐
│ Frontend Validation              │
│ ✓ Rating: required (1-5)        │
│ ✓ Review: optional (max 1000)   │
└────────────┬─────────────────────┘
             ▼
┌──────────────────────────────────┐
│ Backend Validation               │
│ ✓ Rating: integer 1-5           │
│ ✓ Review: string|null, max 1000 │
│ ✓ User authenticated            │
│ ✓ user_type == 'user'           │
│ ✓ No duplicate rating           │
└────────────┬─────────────────────┘
             ▼
        SUCCESS ✅
             │
             ▼
    Database INSERT/UPDATE
```

---

## State Management Flow (RatingSection)

```
Component Mount
      │
      ▼
┌──────────────────┐
│ loadRatings()    │ (API GET)
│ isLoading=true   │
└────────┬─────────┘
         │
         ▼
   API Response
         │
         ▼
┌──────────────────────────────────┐
│ Update State:                    │
│ • ratings = [...]               │
│ • stats = {...}                 │
│ • existingRating = {...}? (user)│
│ • userRating = rating? : 0      │
│ • isLoading = false             │
└────────┬─────────────────────────┘
         │
         ▼
   Render Component
         │
         ├─► Display Stats
         ├─► Display Rating Form (if user)
         ├─► Display Reviews List
         └─► Display Login CTA (if not auth)
```

---

## Performance Considerations

```
API Optimization:
┌─────────────────────────────────┐
│ GET /api/v1/umkms/{id}/ratings  │
│ • Eager load user relations     │
│ • Cache stats for 5 minutes     │
│ • Paginate if > 50 reviews      │
└─────────────────────────────────┘

Database Optimization:
┌─────────────────────────────────┐
│ Indexes on:                     │
│ • ratings(umkm_id)              │
│ • ratings(user_id)              │
│ • ratings.created_at (for sort) │
│ • unique(umkm_id, user_id)      │
└─────────────────────────────────┘

Frontend Optimization:
┌─────────────────────────────────┐
│ • Lazy load rating images       │
│ • Memoize computed properties   │
│ • Debounce search/filter        │
│ • Virtual scrolling for large   │
│   review lists                  │
└─────────────────────────────────┘
```

---

## Error Handling Flow

```
Error Type                  API Response         Frontend Action
─────────────────────────────────────────────────────────────────
Validation Error           422                  Show field errors
Auth Required              401                  Redirect to login
Unauthorized               403                  Show "Access denied"
Duplicate Rating           409                  Show update form
Server Error              500                  Show generic error
Network Error             -                    Retry or offline msg
```

---

## Deployment Checklist

```
Development → Production

Database:
□ Backup current database
□ Run migrations
□ Verify tables created
□ Check indexes present

Backend:
□ Update AuthApiController
□ Test API endpoints
□ Check rate limiting
□ Enable HTTPS (if not)

Frontend:
□ Build Vue components
□ Run npm build
□ Test in production mode
□ Check responsive design
□ Verify all links work

Monitoring:
□ Setup error logging
□ Monitor API performance
□ Track user signups
□ Alert on high errors
```

---

This document visualizes the complete system architecture and data flows.

Generated: 2025-04-18
