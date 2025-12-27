# Phase C: Transaction UI - Task Checklist

## Backend
- [ ] Create `TransactionController.php` (Web)
- [ ] Add transaction routes to `web.php`

## Frontend Pages
- [ ] Create `Finance/Transactions/Index.tsx`
  - [ ] List view dengan table (desktop)
  - [ ] Card view (mobile)
  - [ ] Filter: status, tanggal, tipe
  - [ ] Pagination
  - [ ] Premium dark mode styling

- [ ] Create `Finance/Transactions/Create.tsx`
  - [ ] Form header (tanggal, tipe, deskripsi)
  - [ ] Dynamic line items (add/remove)
  - [ ] Balance indicator (realtime)
  - [ ] Submit validation

- [ ] Create `Finance/Transactions/Show.tsx`
  - [ ] Detail view
  - [ ] Post action
  - [ ] Void action dengan modal

## Components
- [ ] Create `TransactionStatusBadge.tsx`

## Documentation
- [ ] Update CHANGELOG.md

## Testing
- [ ] Test create transaksi
- [ ] Test post transaksi
- [ ] Test void transaksi
- [ ] Test dark mode
- [ ] Test mobile view
