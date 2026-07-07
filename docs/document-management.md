# Document & File Management System

## Overview

The Document Management System (DMS) provides centralized file management for the entire ERP. Every module can upload, organize, preview, download, and manage documents through this reusable infrastructure.

---

## Architecture

```
ERP Modules (any module)
    ↓
DocumentService / FolderService / StorageService / FilePreviewService
    ↓
Laravel Storage (local / public / future S3)
    ↓
Documents Table (polymorphic) + Folders Table
```

---

## Core Entities

| Entity | Purpose |
|--------|---------|
| Document | File records with polymorphic attachment |
| Folder | Hierarchical folder organization |

---

## Polymorphic Relationships

Documents can be attached to any entity:

- Company
- Employee
- Customer
- Supplier
- Product
- PurchaseOrder
- SalesOrder
- Invoice
- JournalEntry
- Opportunity
- Any future entity

---

## Services

| Service | Purpose |
|---------|---------|
| DocumentService | Upload, download, rename, move, delete, search, duplicate detection |
| FolderService | CRUD folders, tree structure |
| StorageService | Storage path resolution, disk usage |
| FilePreviewService | Preview capability detection, content retrieval |

---

## File Features

| Feature | Implementation |
|---------|---------------|
| Upload | Single and multi-file upload with validation |
| Download | Streamed download with original filename |
| Preview | Image (base64), PDF, text content |
| Rename | Update original_name |
| Move | Change folder_id |
| Soft Delete | Move to trash |
| Restore | Recover from trash |
| Permanent Delete | Remove file + DB record |
| Duplicate Detection | SHA-256 checksum comparison |
| Drag & Drop Ready | Architecture supports future implementation |

---

## Storage

| Disk | Purpose |
|------|---------|
| local | Default storage location |
| public | Publicly accessible files |

Architecture allows future S3, MinIO, Azure Blob Storage support.

---

## Supported File Types

| Type | Extensions |
|------|-----------|
| Documents | pdf, docx, xlsx, csv, txt |
| Images | jpg, jpeg, png, webp |

Max file size: 10 MB

---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/documents` | File browser |
| POST | `/documents` | Upload files |
| GET | `/documents/{document}` | Document detail + preview |
| GET | `/documents/{document}/download` | Download file |
| PATCH | `/documents/{document}/rename` | Rename file |
| PATCH | `/documents/{document}/move` | Move to folder |
| PATCH | `/documents/{document}/restore` | Restore from trash |
| DELETE | `/documents/{document}` | Soft delete |
| DELETE | `/documents/{document}/force-delete` | Permanent delete |
| GET | `/documents/trash` | View trash |
| GET | `/folders` | Folder tree |
| POST | `/folders` | Create folder |
| GET | `/folders/{folder}` | Folder contents |
| PATCH | `/folders/{folder}` | Update folder |
| DELETE | `/folders/{folder}` | Delete folder |

---

## Tests

| Test Suite | Tests |
|------------|-------|
| DocumentTest | 10 |
| FolderTest | 5 |
| DocumentServiceTest | 7 |
| FolderServiceTest | 4 |
| **Total** | **26** |
