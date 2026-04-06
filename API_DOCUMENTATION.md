# Edrak API Documentation

**Base URL:** `http://localhost:8000/api`

## Overview

- All admin endpoints require `Authorization: Bearer {token}` header
- Bilingual content follows `Accept-Language` header (`en` or `ar`, defaults to `en`)
- All request bodies are JSON (`Content-Type: application/json`)
- All IDs exposed in the API are UUIDs (internal auto-increment IDs are hidden)
- Timestamps are ISO 8601 format

## Response Envelope

All JSON responses follow this structure:

```json
{
  "success": true,
  "message": "Success",
  "data": { ... }
}
```

Error responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": { "field": ["Validation error"] }
}
```

Paginated responses add `links` and `meta`:

```json
{
  "success": true,
  "message": "Success",
  "data": [ ... ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "from": 1, "to": 20, "total": 100, "per_page": 20, "last_page": 5 }
}
```

## HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200  | Success |
| 201  | Created |
| 401  | Unauthenticated / Invalid credentials |
| 403  | Forbidden (no permission) |
| 404  | Not found |
| 409  | Conflict (e.g. test already completed) |
| 410  | Gone (e.g. time expired) |
| 422  | Validation error |

---

# Authentication

## POST /api/auth/login

Login and receive an API token.

**Auth:** None

**Request:**

```json
{
  "email": "superadmin@edrak.com",
  "password": "password"
}
```

| Field    | Type   | Rules              |
|----------|--------|--------------------|
| email    | string | required, email    |
| password | string | required           |

**Response (200):**

```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "user": {
      "id": "uuid",
      "name": "Super Admin",
      "email": "superadmin@edrak.com",
      "role": "super_admin",
      "is_active": true,
      "preferred_locale": "en",
      "created_at": "2026-03-01T20:07:32.000000Z"
    },
    "token": "1|abc123..."
  }
}
```

**Errors:**
- `401` — Invalid credentials
- `403` — Account deactivated

---

## POST /api/auth/logout

Revoke the current token.

**Auth:** Bearer token

**Request:** None

**Response (200):**

```json
{
  "success": true,
  "message": "Logged out successfully.",
  "data": null
}
```

---

## GET /api/auth/me

Get the authenticated user's profile.

**Auth:** Bearer token

**Response (200):**

```json
{
  "success": true,
  "message": "Success",
  "data": {
    "id": "uuid",
    "name": "Super Admin",
    "email": "superadmin@edrak.com",
    "role": "super_admin",
    "is_active": true,
    "preferred_locale": "en",
    "created_at": "2026-03-01T20:07:32.000000Z"
  }
}
```

---

# Categories

All category endpoints require `Bearer` token. Only `super_admin` can create, update, or delete categories. All admins can read.

## GET /api/admin/categories

List all categories.

**Pagination:** No (returns all, ordered by `sort_order`)

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "title": "Occupational Burnout",
      "description": "Assessments measuring workplace burnout...",
      "sort_order": 1,
      "tests_count": 3,
      "created_at": "2026-03-01T20:07:32.000000Z"
    }
  ]
}
```

---

## POST /api/admin/categories

Create a new category.

**Request:**

```json
{
  "title": { "en": "Work Stress", "ar": "ضغوط العمل" },
  "description": { "en": "Stress assessments", "ar": "تقييمات الضغط" },
  "sort_order": 4
}
```

| Field          | Type    | Rules                        |
|----------------|---------|------------------------------|
| title          | object  | required                     |
| title.en       | string  | required, max:255            |
| title.ar       | string  | required, max:255            |
| description    | object  | nullable                     |
| description.en | string  | nullable                     |
| description.ar | string  | nullable                     |
| sort_order     | integer | nullable, min:0              |

**Response (201):**

```json
{
  "success": true,
  "message": "Category created.",
  "data": { "id": "uuid", "title": "Work Stress", ... }
}
```

---

## GET /api/admin/categories/{id}

Get a single category.

**Response (200):** Same shape as list item, with `tests_count`.

---

## PUT /api/admin/categories/{id}

Update a category. All fields optional.

**Request:**

```json
{
  "title": { "en": "Updated Title", "ar": "عنوان محدث" }
}
```

| Field          | Type    | Rules                              |
|----------------|---------|-------------------------------------|
| title          | object  | sometimes                           |
| title.en       | string  | required_with:title, max:255        |
| title.ar       | string  | required_with:title, max:255        |
| description    | object  | nullable                            |
| description.en | string  | nullable                            |
| description.ar | string  | nullable                            |
| sort_order     | integer | nullable, min:0                     |

**Response (200):** Updated category object.

---

## DELETE /api/admin/categories/{id}

Delete a category.

**Response (200):**

```json
{ "success": true, "message": "Category deleted.", "data": null }
```

---

# Tests

Admins see only their own tests. `super_admin` sees all.

## GET /api/admin/tests

List tests (paginated).

**Pagination:** Yes (20 per page)

**Response (200):** Paginated array of test summaries:

```json
{
  "data": [
    {
      "id": "uuid",
      "title": "Maslach Burnout Inventory",
      "status": "published",
      "category": { "id": "uuid", "title": "...", ... },
      "questions_count": 22,
      "created_at": "2026-03-01T20:07:32.000000Z"
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

## POST /api/admin/tests

Create a new test.

**Request:**

```json
{
  "category_id": "category-uuid",
  "title": { "en": "Burnout Scale", "ar": "مقياس الإحتراق" },
  "description": { "en": "...", "ar": "..." },
  "instructions": { "en": "...", "ar": "..." },
  "status": "draft",
  "scale_config": { "min": 1, "max": 5, "labels": { "1": "Never", "5": "Always" } },
  "time_limit_minutes": 30,
  "randomize_questions": false
}
```

| Field               | Type    | Rules                           |
|---------------------|---------|---------------------------------|
| category_id         | string  | nullable, exists:categories,uuid|
| title               | object  | required                        |
| title.en            | string  | required, max:255               |
| title.ar            | string  | required, max:255               |
| description         | object  | nullable                        |
| description.en      | string  | nullable                        |
| description.ar      | string  | nullable                        |
| instructions        | object  | nullable                        |
| instructions.en     | string  | nullable                        |
| instructions.ar     | string  | nullable                        |
| status              | string  | sometimes, in:draft,published,archived |
| scale_config        | object  | required                        |
| scale_config.min    | integer | required, min:0                 |
| scale_config.max    | integer | required, min:1                 |
| scale_config.labels | object  | nullable                        |
| time_limit_minutes  | integer | nullable, min:1                 |
| randomize_questions | boolean | sometimes                       |

**Response (201):** Full test object with category and empty questions array.

---

## GET /api/admin/tests/{id}

Get test with all questions.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "title": "Maslach Burnout Inventory",
    "description": "...",
    "instructions": "...",
    "status": "published",
    "scale_config": { "min": 0, "max": 6 },
    "time_limit_minutes": null,
    "randomize_questions": false,
    "category": { "id": "uuid", "title": "...", ... },
    "questions": [
      {
        "id": 1,
        "text": "I feel emotionally drained from my work.",
        "sort_order": 1,
        "is_reverse_scored": false,
        "is_required": true,
        "scale_override": null
      }
    ],
    "questions_count": 22,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

## PUT /api/admin/tests/{id}

Update a test. All fields optional (same rules as create, but `sometimes`).

**Response (200):** Updated test object.

---

## DELETE /api/admin/tests/{id}

Soft-delete a test.

**Response (200):**

```json
{ "success": true, "message": "Test deleted.", "data": null }
```

---

# Questions

Nested under tests. Authorization checks test ownership.

## GET /api/admin/tests/{test_id}/questions

List all questions for a test, ordered by `sort_order`.

**Pagination:** No

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "text": "I feel emotionally drained from my work.",
      "sort_order": 1,
      "is_reverse_scored": false,
      "is_required": true,
      "scale_override": null
    }
  ]
}
```

> Note: Question `id` is an integer (internal ID), not UUID, since questions are always accessed nested under a test.

---

## POST /api/admin/tests/{test_id}/questions

Create a question.

**Request:**

```json
{
  "text": { "en": "I feel burned out.", "ar": "أشعر بالإحتراق." },
  "sort_order": 5,
  "is_reverse_scored": false,
  "is_required": true,
  "scale_override": null
}
```

| Field            | Type    | Rules              |
|------------------|---------|--------------------|
| text             | object  | required           |
| text.en          | string  | required           |
| text.ar          | string  | required           |
| sort_order       | integer | nullable, min:0    |
| is_reverse_scored| boolean | sometimes          |
| scale_override   | object  | nullable           |
| is_required      | boolean | sometimes          |

If `sort_order` is omitted, it auto-increments to `max(sort_order) + 1`.

**Response (201):** Question object.

---

## PUT /api/admin/tests/{test_id}/questions/{question_id}

Update a question. Returns `404` if question doesn't belong to the test.

**Request:** Same fields as create, all optional.

**Response (200):** Updated question object.

---

## DELETE /api/admin/tests/{test_id}/questions/{question_id}

Delete a question. Returns `404` if question doesn't belong to the test.

**Response (200):**

```json
{ "success": true, "message": "Question deleted.", "data": null }
```

---

## POST /api/admin/tests/{test_id}/questions/reorder

Bulk update question sort order.

**Request:**

```json
{
  "questions": [
    { "id": 3, "sort_order": 1 },
    { "id": 1, "sort_order": 2 },
    { "id": 2, "sort_order": 3 }
  ]
}
```

| Field                   | Type    | Rules                        |
|-------------------------|---------|------------------------------|
| questions               | array   | required, min:1              |
| questions.*.id          | integer | required, exists:questions,id|
| questions.*.sort_order  | integer | required, min:0              |

**Response (200):**

```json
{ "success": true, "message": "Questions reordered.", "data": null }
```

---

# Assessments

Assessments are bundles of tests. Admins see only their own. `super_admin` sees all.

## GET /api/admin/assessments

List assessments (paginated).

**Pagination:** Yes (20 per page)

**Response (200):**

```json
{
  "data": [
    {
      "id": "uuid",
      "title": "Employee Well-being Assessment",
      "description": "...",
      "instructions": "...",
      "status": "published",
      "show_results_to_participant": true,
      "tests_count": 2,
      "links_count": 4,
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

## POST /api/admin/assessments

Create an assessment.

**Request:**

```json
{
  "title": { "en": "Well-being Check", "ar": "فحص الرفاهية" },
  "description": { "en": "...", "ar": "..." },
  "instructions": { "en": "...", "ar": "..." },
  "status": "draft",
  "show_results_to_participant": true
}
```

| Field                        | Type    | Rules                                  |
|------------------------------|---------|----------------------------------------|
| title                        | object  | required                               |
| title.en                     | string  | required, max:255                      |
| title.ar                     | string  | required, max:255                      |
| description                  | object  | nullable                               |
| description.en               | string  | nullable                               |
| description.ar               | string  | nullable                               |
| instructions                 | object  | nullable                               |
| instructions.en              | string  | nullable                               |
| instructions.ar              | string  | nullable                               |
| status                       | string  | sometimes, in:draft,published,archived |
| show_results_to_participant  | boolean | sometimes                              |

**Response (201):** Assessment object.

---

## GET /api/admin/assessments/{id}

Get assessment with attached tests.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "title": "Employee Well-being Assessment",
    "description": "...",
    "instructions": "...",
    "status": "published",
    "show_results_to_participant": true,
    "tests": [
      {
        "id": "uuid",
        "title": "Maslach Burnout Inventory",
        "status": "published",
        "category": { ... },
        "questions_count": 22,
        "created_at": "..."
      }
    ],
    "tests_count": 2,
    "links_count": 4,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

## PUT /api/admin/assessments/{id}

Update an assessment.

**Response (200):** Updated assessment object.

---

## DELETE /api/admin/assessments/{id}

Soft-delete an assessment.

**Response (200):**

```json
{ "success": true, "message": "Assessment deleted.", "data": null }
```

---

## POST /api/admin/assessments/{id}/tests

Attach tests to an assessment.

**Request:**

```json
{
  "tests": [
    { "uuid": "test-uuid-1", "sort_order": 1 },
    { "uuid": "test-uuid-2", "sort_order": 2 }
  ]
}
```

| Field              | Type    | Rules                     |
|--------------------|---------|---------------------------|
| tests              | array   | required, min:1           |
| tests.*.uuid       | string  | required, exists:tests,uuid |
| tests.*.sort_order | integer | nullable, min:0           |

Uses `syncWithoutDetaching` — won't remove existing test attachments.

**Response (200):** Updated assessment with tests loaded.

---

## DELETE /api/admin/assessments/{id}/tests/{test_id}

Detach a test from an assessment.

**Response (200):**

```json
{ "success": true, "message": "Test detached.", "data": null }
```

---

# Assessment Links

Links are shareable URLs for participants to access an assessment. Nested under assessments.

## GET /api/admin/assessments/{assessment_id}/links

List all links for an assessment.

**Pagination:** No

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "token": "Cprailia1ZIyKNjy...",
      "title": "HR Department - Q1 2026",
      "url": "http://frontend.com/assess/Cprailia1ZIy...",
      "starts_at": "2026-01-01T00:00:00.000000Z",
      "expires_at": "2026-06-30T23:59:59.000000Z",
      "max_participants": 50,
      "is_active": true,
      "has_password": false,
      "collect_name": true,
      "collect_email": true,
      "collect_phone": false,
      "collect_department": true,
      "collect_age": true,
      "collect_gender": true,
      "custom_fields": null,
      "welcome_message": "Welcome to the assessment...",
      "completion_message": "Thank you for completing...",
      "participants_count": 10,
      "is_accessible": true,
      "created_at": "..."
    }
  ]
}
```

Non-super_admin users see only links they created.

---

## POST /api/admin/assessments/{assessment_id}/links

Create a link.

**Request:**

```json
{
  "title": "Engineering Team Q1",
  "starts_at": "2026-04-01",
  "expires_at": "2026-04-30",
  "max_participants": 30,
  "is_active": true,
  "password": "secret123",
  "collect_name": true,
  "collect_email": true,
  "collect_phone": false,
  "collect_department": true,
  "collect_age": true,
  "collect_gender": true,
  "custom_fields": null,
  "welcome_message": { "en": "Welcome!", "ar": "!مرحبا" },
  "completion_message": { "en": "Thank you!", "ar": "!شكرا" }
}
```

| Field              | Type    | Rules                          |
|--------------------|---------|--------------------------------|
| title              | string  | nullable, max:255              |
| starts_at          | date    | nullable                       |
| expires_at         | date    | nullable, after:starts_at      |
| max_participants   | integer | nullable, min:1                |
| is_active          | boolean | sometimes                      |
| password           | string  | nullable, min:4                |
| collect_name       | boolean | sometimes                      |
| collect_email      | boolean | sometimes                      |
| collect_phone      | boolean | sometimes                      |
| collect_department | boolean | sometimes                      |
| collect_age        | boolean | sometimes                      |
| collect_gender     | boolean | sometimes                      |
| custom_fields      | array   | nullable                       |
| welcome_message    | object  | nullable                       |
| welcome_message.en | string  | nullable                       |
| welcome_message.ar | string  | nullable                       |
| completion_message | object  | nullable                       |
| completion_message.en | string | nullable                    |
| completion_message.ar | string | nullable                    |

Token is auto-generated (64 chars). Password is hashed.

**Response (201):** Link object.

---

## GET /api/admin/assessments/{assessment_id}/links/{link_id}

Get a single link.

**Response (200):** Link object (same shape as list item).

---

## PUT /api/admin/assessments/{assessment_id}/links/{link_id}

Update a link. Same fields as create, all optional.

**Response (200):** Updated link object.

---

## DELETE /api/admin/assessments/{assessment_id}/links/{link_id}

Soft-delete a link.

**Response (200):**

```json
{ "success": true, "message": "Assessment link deleted.", "data": null }
```

---

# Results

View participant results and responses.

## GET /api/admin/assessments/{id}/results

Get all participants and their attempts for an assessment (across all links).

**Pagination:** No

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "name": "Ahmed Al-Rashid",
      "email": "ahmed.r@company.com",
      "phone": null,
      "department": "Engineering",
      "age": 32,
      "gender": "male",
      "locale": "ar",
      "attempts": [
        {
          "id": "uuid",
          "test": { "id": "uuid", "title": "Maslach Burnout Inventory", ... },
          "status": "completed",
          "started_at": "...",
          "completed_at": "...",
          "score_raw": "86.00",
          "score_max": "132.00",
          "score_percentage": "65.15",
          "score_average": "3.91",
          "time_spent_seconds": 1500
        }
      ],
      "created_at": "..."
    }
  ]
}
```

Score fields (`score_raw`, `score_max`, `score_percentage`, `score_average`) are only included when the attempt status is `completed`.

---

## GET /api/admin/assessments/{assessment_id}/links/{link_id}/results

Get participants for a specific link (paginated).

**Pagination:** Yes (20 per page)

**Response (200):** Same shape as assessment results, but scoped to one link and paginated.

---

## GET /api/admin/attempts/{attempt_id}

Get detailed info for a single attempt.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "attempt": {
      "id": "uuid",
      "test": { "id": "uuid", "title": "..." },
      "status": "completed",
      "started_at": "...",
      "completed_at": "...",
      "score_raw": "86.00",
      "score_max": "132.00",
      "score_percentage": "65.15",
      "score_average": "3.91",
      "time_spent_seconds": 1500
    },
    "participant": {
      "id": "uuid",
      "name": "Ahmed Al-Rashid",
      "email": "ahmed.r@company.com",
      ...
    }
  }
}
```

---

## GET /api/admin/attempts/{attempt_id}/responses

Get all individual responses for an attempt.

**Pagination:** No

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "question_id": 1,
      "question_text": "I feel emotionally drained from my work.",
      "value": 4,
      "scored_value": 4,
      "answered_at": "2026-02-23T19:24:13.000000Z"
    }
  ]
}
```

`value` is the raw answer. `scored_value` is adjusted for reverse-scoring.

---

# CSV Export

Download assessment results as CSV files. All export endpoints return a streamed CSV file with UTF-8 BOM (for Arabic/Excel compatibility). Column headers for test names and question text follow the `Accept-Language` header.

## GET /api/admin/assessments/{id}/export/summary

Export summary CSV — one row per participant with scores per test.

**Response:** CSV file download

**Columns:** `participant_name`, `participant_email`, `participant_phone`, `participant_department`, `participant_age`, `participant_gender`, `participant_locale`, `completed_at`, then per test: `{Test Name} - Score Raw`, `{Test Name} - Score Max`, `{Test Name} - Score %`, `{Test Name} - Score Avg`, `{Test Name} - Time (s)`

Only completed attempts are included. Empty cells for tests not attempted.

---

## GET /api/admin/assessments/{id}/export/detailed

Export detailed CSV — one row per question response.

**Response:** CSV file download

**Columns:** `participant_name`, `participant_email`, `participant_department`, `participant_age`, `participant_gender`, `test_name`, `question_number`, `question_text`, `is_reverse_scored`, `raw_value`, `scored_value`, `answered_at`

---

## GET /api/admin/assessments/{assessment_id}/links/{link_id}/export/summary

Same as assessment summary, scoped to one link.

---

## GET /api/admin/assessments/{assessment_id}/links/{link_id}/export/detailed

Same as assessment detailed, scoped to one link.

---

# Participant Flow (Public)

These endpoints do NOT require authentication. Participants access assessments via a 64-character token.

## GET /api/participate/{token}

Get public info about an assessment link.

**Auth:** None

**Response (200):**

```json
{
  "success": true,
  "data": {
    "assessment_title": "Employee Well-being Assessment",
    "assessment_description": "A comprehensive assessment...",
    "assessment_instructions": "Please complete both tests honestly...",
    "welcome_message": "Welcome to the assessment...",
    "has_password": false,
    "required_fields": ["name", "email", "department", "age", "gender"],
    "custom_fields": null,
    "tests_count": 2
  }
}
```

**Errors:**
- `404` — Token not found
- `403` — Link not accessible (expired, deactivated, or full)

---

## POST /api/participate/{token}/verify-password

Verify the link's password (if one is set).

**Request:**

```json
{ "password": "secret123" }
```

**Response (200):**

```json
{
  "success": true,
  "data": { "verified": true },
  "message": "Password verified."
}
```

If no password is set, returns `"No password required."` with `verified: true`.

**Errors:**
- `401` — Invalid password

---

## POST /api/participate/{token}/register

Register as a participant.

**Request:** Fields are dynamically required based on the link's `collect_*` settings.

```json
{
  "name": "Ahmed Al-Rashid",
  "email": "ahmed@company.com",
  "department": "Engineering",
  "age": 32,
  "gender": "male",
  "custom_data": {}
}
```

| Field       | Type    | Rules (when collected)                          |
|-------------|---------|--------------------------------------------------|
| name        | string  | required, max:255                                |
| email       | string  | required, email, max:255                         |
| phone       | string  | nullable, max:50                                 |
| department  | string  | nullable, max:255                                |
| age         | integer | nullable, min:10, max:120                        |
| gender      | string  | nullable, in:male,female,other,prefer_not_to_say |
| custom_data | object  | nullable                                         |

IP address, user agent, and locale are captured automatically.

**Response (201):**

```json
{
  "success": true,
  "message": "Registered successfully.",
  "data": {
    "id": "participant-uuid",
    "name": "Ahmed Al-Rashid",
    "email": "ahmed@company.com",
    "phone": null,
    "department": "Engineering",
    "age": 32,
    "gender": "male",
    "locale": "en",
    "created_at": "..."
  }
}
```

**Errors:**
- `403` — Link not accessible

---

## GET /api/participate/session/{participant_id}

Get the participant's assessment progress.

**Response (200):**

```json
{
  "success": true,
  "data": {
    "participant": { "id": "uuid", "name": "Ahmed", ... },
    "assessment_title": "Employee Well-being Assessment",
    "tests": [
      {
        "test": { "id": "uuid", "title": "Maslach Burnout Inventory", ... },
        "status": "completed",
        "attempt_id": "attempt-uuid"
      },
      {
        "test": { "id": "uuid", "title": "Perceived Stress Scale", ... },
        "status": "not_started",
        "attempt_id": null
      }
    ],
    "all_completed": false
  }
}
```

`status` is one of: `not_started`, `in_progress`, `completed`.

---

## GET /api/participate/session/{participant_id}/test/{test_id}

Start or resume a test. Creates a new attempt if none exists, or returns the existing in-progress attempt.

**Response (201 new / 200 existing):**

```json
{
  "success": true,
  "message": "Test started.",
  "data": {
    "attempt": {
      "id": "attempt-uuid",
      "status": "in_progress",
      "started_at": "...",
      ...
    },
    "questions": [
      {
        "id": 1,
        "text": "I feel emotionally drained from my work.",
        "sort_order": 1,
        "is_reverse_scored": false,
        "is_required": true,
        "scale_override": null
      }
    ],
    "remaining_seconds": 1800
  }
}
```

`remaining_seconds` is `null` if no time limit. Questions are shuffled if `randomize_questions` is enabled.

**Errors:**
- `404` — Test not part of the assessment
- `409` — Test already completed
- `410` — Time expired (attempt auto-completed)

---

## POST /api/participate/session/{participant_id}/test/{test_id}/responses

Submit responses (can be called multiple times — uses upsert).

**Request:**

```json
{
  "responses": [
    { "question_id": 1, "value": 4 },
    { "question_id": 2, "value": 3 },
    { "question_id": 3, "value": 5 }
  ]
}
```

| Field                    | Type    | Rules                          |
|--------------------------|---------|--------------------------------|
| responses                | array   | required, min:1                |
| responses.*.question_id  | integer | required, exists:questions,id  |
| responses.*.value        | integer | required                       |

`scored_value` is auto-calculated based on reverse-scoring rules.

**Response (200):**

```json
{ "success": true, "message": "Responses saved.", "data": null }
```

**Errors:**
- `404` — No in-progress attempt found
- `410` — Time expired

---

## POST /api/participate/session/{participant_id}/test/{test_id}/complete

Mark the test as completed and calculate scores.

**Response (200):**

```json
{
  "success": true,
  "message": "Test completed.",
  "data": {
    "id": "attempt-uuid",
    "status": "completed",
    "started_at": "...",
    "completed_at": "...",
    "score_raw": "68.00",
    "score_max": "132.00",
    "score_percentage": "51.52",
    "score_average": "3.09",
    "time_spent_seconds": 1245
  }
}
```

---

## GET /api/participate/session/{participant_id}/results

Get the participant's final results (all completed tests).

**Response (200):**

```json
{
  "success": true,
  "data": {
    "assessment_title": "Employee Well-being Assessment",
    "completion_message": "Thank you for completing the assessment.",
    "results": [
      {
        "id": "uuid",
        "test": { "id": "uuid", "title": "Maslach Burnout Inventory", ... },
        "status": "completed",
        "score_raw": "68.00",
        "score_max": "132.00",
        "score_percentage": "51.52",
        "score_average": "3.09",
        "time_spent_seconds": 1245
      }
    ]
  }
}
```

**Errors:**
- `403` — Assessment has `show_results_to_participant` set to `false`

---

# Authorization Summary

| Resource        | View Any | View         | Create    | Update       | Delete       |
|-----------------|----------|--------------|-----------|--------------|--------------|
| Category        | All      | All          | super_admin | super_admin | super_admin |
| Test            | All      | Owner or SA  | All       | Owner or SA  | Owner or SA  |
| Assessment      | All      | Owner or SA  | All       | Owner or SA  | Owner or SA  |
| AssessmentLink  | All      | Creator or SA| All       | Creator or SA| Creator or SA|
| TestAttempt     | —        | Assessment owner or SA | — | —          | —            |

**SA** = super_admin. **Owner** = the user who created the resource.

Non-super_admin users see only their own resources in list endpoints.
