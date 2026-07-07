# CRM Module

## Overview

The CRM module manages leads, contacts, opportunities, sales pipelines, activities, notes, and tasks. Integrates with Sales, Purchasing, and Inventory modules.

---

## Architecture

```
Lead ──has many──> Contact
Lead ──has many──> Opportunity
Lead ──has many──> Activities
Lead ──has many──> Notes

Opportunity ──belongs to──> Pipeline
Opportunity ──belongs to──> PipelineStage
Opportunity ──has many──> Activities
Opportunity ──has many──> Tasks
Opportunity ──has many──> Notes

Pipeline ──has many──> PipelineStage
Pipeline ──has many──> Opportunity
```

---

## Lead Lifecycle

```
New → Qualified → Contacted → Proposal Sent → Negotiation → Won
                                                           ↓
                                                          Lost
```

### Conversion

When a lead is converted:
1. A new Customer is created from lead data
2. Lead history is preserved
3. Activities and notes remain linked
4. Lead is marked as converted with timestamp

---

## Pipeline

Customizable sales pipelines with multiple stages.

| Stage | Purpose |
|-------|---------|
| Qualification | Initial screening |
| Needs Analysis | Understanding requirements |
| Proposal | Delivering proposal |
| Negotiation | Terms discussion |
| Closed Won | Successful deal |
| Closed Lost | Lost deal |

---

## Services

| Service | Purpose |
|---------|---------|
| `LeadService` | CRUD, conversion, lost marking |
| `OpportunityService` | CRUD, stage movement, win/loss |
| `PipelineService` | CRUD, stage management |
| `ActivityService` | CRUD, completion tracking |
| `TaskService` | CRUD, completion tracking |
| `CustomerConversionService` | Lead → Customer conversion |

---

## Business Rules

| Rule | Enforcement |
|------|-------------|
| Lead email unique per company | Service validation |
| Won opportunities are immutable | Service validation |
| Lost opportunities require reason | Service validation |
| Converted leads cannot be edited except notes | Service validation |
| Posted journal entries are immutable | Accounting service |

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/leads` | List leads |
| POST | `/leads` | Store lead |
| GET | `/leads/{lead}` | Show lead |
| PUT | `/leads/{lead}` | Update lead |
| DELETE | `/leads/{lead}` | Delete lead |
| PATCH | `/leads/{lead}/convert` | Convert to customer |
| GET | `/opportunities` | List opportunities |
| POST | `/opportunities` | Store opportunity |
| PATCH | `/opportunities/{opp}/won` | Mark won |
| PATCH | `/opportunities/{opp}/lost` | Mark lost |
| GET | `/pipelines` | List pipelines |
| POST | `/pipelines` | Store pipeline |
| GET | `/pipelines/{pipeline}` | Show pipeline with stages |
| POST | `/pipelines/{pipeline}/stages` | Add stage |
| DELETE | `/pipeline-stages/{stage}` | Remove stage |
| GET | `/activities` | List activities |
| POST | `/activities` | Store activity |
| PATCH | `/activities/{activity}/complete` | Complete activity |
| GET | `/tasks` | List tasks |
| POST | `/tasks` | Store task |
| PATCH | `/tasks/{task}/complete` | Complete task |

---

## Tests

| Test Suite | Tests |
|------------|-------|
| `LeadTest` | 7 |
| `OpportunityTest` | 6 |
| `PipelineTest` | 4 |
| `LeadServiceTest` | 4 |
| `OpportunityServiceTest` | 7 |
| **Total** | **28** |

---

## Future Expansion

- Marketing Automation
- Email Campaigns
- SMS Notifications
- Customer Portal
- Support Ticket System
- AI Lead Scoring
- AI Sales Assistant
- Predictive Sales Analytics
