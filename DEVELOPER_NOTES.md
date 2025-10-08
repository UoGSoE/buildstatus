# Developer Notes

## Project Overview
This is a proof-of-concept Laravel/Livewire application for monitoring lab machine build statuses across multiple university labs. The approach is to spike out functionality first to understand the domain, then backfill with comprehensive tests once the design is more confident.

## Session Summary (2025-10-08 - Planning)

### Planning: User API Key Management

**Goal**: Allow users to manage their own Sanctum API tokens through a profile interface.

**Research Completed**:

1. **Reviewed Existing Admin Components**
   - `resources/views/livewire/admin/manage-labs.blade.php`
   - `resources/views/livewire/admin/manage-users.blade.php`

2. **Established UI Patterns to Follow**:
   - Header: `flex items-center justify-between` with `flux:heading size="lg"` + action button
   - Filter input: `flux:input` with `wire:model.live` and magnifying-glass icon
   - `flux:separator class="my-4"` for visual separation
   - `flux:table :paginate="$collection"` for data tables
   - Modals: `variant="flyout"` for forms, `space-y-6` for spacing
   - Action buttons: `size="sm"` with icons (pencil, trash)
   - Button groups: Primary left, Cancel right with `flex items-center justify-between`
   - Cancel buttons: `x-on:click="$flux.modals().close()"`
   - Destructive actions: `wire:confirm` for confirmation prompts
   - Warning modals: `flux:callout variant="warning"` for scary actions

3. **Sanctum API Token Features** (via Laravel Boost MCP docs search):
   - ✅ `last_used_at` is tracked **automatically** by Sanctum in the `personal_access_tokens` table
   - ✅ Automatically updated whenever the token authenticates a request
   - Token creation: `$user->createToken($name)->plainTextToken`
   - Token revocation: `$user->tokens()->where('id', $tokenId)->delete()`
   - **IMPORTANT**: Plaintext token only available via `$token->plainTextToken` immediately after creation
   - After creation, tokens are hashed in database (SHA-256) and cannot be retrieved

**Planned Implementation**:

1. **Profile Page Structure**:
   - Create `/profile` route
   - Create `Profile` Livewire component (container)
   - Include `ApiKeys` Livewire component within Profile
   - Leaves room for future profile-related features

2. **ApiKeys Component Features**:
   - Table columns: Name, Created, Last Used, Actions
   - "Create New Token" button → flyout modal
   - Modal collects token name (e.g., "Production Server", "Testing")
   - After creation: Show plaintext token ONCE with copy button
   - Warning: "Copy this token now. It won't be shown again."
   - "Revoke" button for each token → confirmation
   - Display last_used_at as human-readable (e.g., "2 hours ago", "Never")
   - Users can only see/manage their own tokens (scoped query)

**Key Database Fields Available**:
- `name` - Token name/description
- `token` - SHA-256 hash (not user-visible)
- `last_used_at` - Automatically updated by Sanctum
- `created_at` - When token was created
- `expires_at` - Optional (not currently used)

**Next Steps**:
- Create Profile Livewire component
- Create ApiKeys Livewire component
- Add routes for `/profile`
- Implement token creation with one-time plaintext display
- Implement token revocation
- Write comprehensive tests

---

## Session Summary (2025-10-07 - Part 2)

### What We Accomplished

1. **Admin Components - Lab Management**
   - Created `ManageLabs` Livewire component with full CRUD operations
   - FluxUI table with flyout modals for create/edit forms
   - Complex deletion workflow with two options:
     - **Option 1**: Reassign all machines to another lab before deletion
     - **Option 2**: Delete lab and all associated machines
   - Prevents orphaning machines by showing confirmation modal when lab has machines
   - Authorization checks: admin-only for all CRUD operations
   - Tests: 28 passing (ManageLabsTest)

2. **Admin Components - User Management**
   - Created `ManageUsers` Livewire component with full CRUD operations
   - FluxUI table with flyout modals for create/edit forms
   - Clickable admin badge toggle (sky color for admins)
   - Self-edit protection: users cannot demote themselves
   - Authorization checks: admin-only for all operations
   - Hidden admin checkbox when editing own account
   - Tests: 41 passing (ManageUsersTest)

3. **Bulk Delete Feature**
   - Added "Clear Filtered Machines" button to MachineList component
   - Respects current filters (text search AND lab selection)
   - Shows scary confirmation modal with danger callout
   - Displays count of machines to be deleted
   - Perfect for summer rebuild process (clearing out old machines)
   - Admin-only with proper authorization checks
   - Tests: 9 new tests added to MachineListTest (24 total)

4. **UI Polish & Fixes**
   - Fixed pluralization throughout using `Str::plural('machine', $count)`
   - Fixed layout jank caused by loading spinner in filter input
   - Rearranged controls: Lab dropdown + Filter on left, Delete button + Auto-refresh on right
   - Added routes for admin components: `admin.labs` and `admin.users`
   - Updated sidebar navigation with admin links

### Key Design Decisions

#### Lab Deletion Modal with Two Options
**Problem**: Deleting labs could orphan machines. Simply blocking deletion isn't practical (labs do get repurposed).

**Solution**: Confirmation modal offering two clear options:
1. Reassign machines to another lab (validates target lab selected)
2. Delete everything (requires double confirmation)

**Rationale**: University context means labs genuinely go away (rooms repurposed), and machines get scattered to different labs or reused. User should make informed decision based on context.

#### Self-Edit Protection
**Issue**: Admin could demote themselves and lock themselves out.

**Solution**:
- Backend guard in `save()` to skip `is_admin` update when `$userId === auth()->user()->id`
- Hidden checkbox in edit modal when editing own account
- Toast warning in `toggleAdmin()` preventing self-demotion

#### Bulk Delete Authorization
All destructive operations require:
- Backend authorization checks with early return and danger toast
- Frontend conditional rendering to hide admin-only buttons
- No machine count shown to non-admins (security through obscurity)

### Niggling Details (Don't Bug Me About These Again!)

#### Pluralization Pet Peeve
**Always use `Str::plural()` for countable nouns!**

```blade
<!-- BAD -->
{{ $count }} machine(s)

<!-- GOOD -->
{{ $count }} {{ Str::plural('machine', $count) }}
```

Applied everywhere:
- Bulk delete modal: "Delete 1 machine" vs "Delete 5 machines"
- Lab deletion modal: "This lab has 1 machine" vs "5 machines"
- Success toasts: proper pluralization
- Confirmation dialogs: proper pluralization

#### UI Jank from Loading Spinners
**Problem**: Flux/Livewire add loading spinners to inputs with `wire:model.live`, causing size changes that jank adjacent elements.

**Solution**: Rearrange layout so growing element doesn't affect others:
- Filter input on far left (can grow freely)
- Fixed elements (lab dropdown, buttons) positioned safely away
- Use `justify-between` to separate left and right groups

**Avoided**: Complex CSS hacks, absolute positioning, or fighting with Flux internals.

### Test Coverage Summary

```
tests/Feature/Livewire/Admin/
├── ManageLabsTest.php (28 tests)
│   ├── Basic CRUD operations
│   ├── Filtering and pagination
│   ├── Complex deletion workflows (reassign vs delete all)
│   ├── Authorization checks (admin vs non-admin)
│   └── Self-edit protection
├── ManageUsersTest.php (41 tests)
│   ├── Basic CRUD operations
│   ├── Admin badge toggle
│   ├── Authorization checks
│   ├── Self-demotion prevention
│   └── Password handling (required for create, optional for update)
└── (previously) MachineListTest.php (24 tests - updated)
    ├── Bulk delete with filters
    ├── Bulk delete by lab
    ├── Authorization for bulk delete
    └── Count reflects current filters
```

**Total for this session**: 93 tests passing

### Commands to Remember

```bash
# Access admin components
/admin/labs
/admin/users

# Run admin tests
lando artisan test --filter=ManageLabsTest
lando artisan test --filter=ManageUsersTest

# Format code (always run before committing)
vendor/bin/pint --dirty
```

### Patterns Established

1. **Admin Authorization Pattern**
   ```php
   if (! auth()->user()->isAdmin()) {
       Flux::toast('Unauthorized action', variant: 'danger');
       return;
   }
   ```

2. **Self-Edit Protection Pattern**
   ```php
   if (auth()->user()->id === $userId) {
       Flux::toast('You cannot change your own admin status', variant: 'danger');
       return;
   }
   ```

3. **Modal Confirmation for Destructive Actions**
   - Use `variant="flyout"` for form modals
   - Use danger callout with `icon="x-circle"` for scary warnings
   - Include `wire:confirm` for double confirmation on danger buttons

4. **Conditional Rendering for Admin Features**
   ```blade
   @if(auth()->user()->isAdmin())
       <!-- admin-only UI -->
   @endif
   ```

### Known Issues & Quirks

#### Flux Loading Spinner Size Changes
Flux UI adds loading spinners to inputs with `wire:model.live`, and there's no documented way to prevent the size change. Work around by:
- Positioning growing elements where they won't affect others
- Using layout groups with `justify-between`
- Avoiding rigid width constraints on adjacent elements

See GitHub issues:
- `livewire/flux#1829` - Permanent loading spinner
- `livewire/flux#1756` - Loading indicator always visible

### Next Steps (Future Sessions)

1. ~~Implement `SimulateApiUpdates` command~~ ✅ Done
2. ~~Add admin features for managing labs and users~~ ✅ Done
3. Consider adding user roles beyond simple is_admin flag
4. Implement audit logging for admin actions
5. Add email notifications for critical status changes
6. Consider soft deletes for machines (currently hard delete)

### Notes for Future Developers

- **Pluralization**: Always use `Str::plural()` - it's a pet peeve!
- **Admin Features**: Use consistent authorization pattern with toast + early return
- **Self-Edit**: Always protect users from locking themselves out
- **Destructive Actions**: Always show scary modals with clear consequences
- **UI Jank**: Position growing elements strategically, don't fight the framework
- **Test Authorization**: Test both positive cases (admin can) and negative cases (non-admin cannot)

---

## Session Summary (2025-10-07 - Part 1)

### What We Accomplished

1. **SimulateApiUpdates Command**
   - Implemented `app:simulate-api-updates` artisan command for local development and demos
   - Accepts `--number` option (defaults to 5) to specify how many machines to update
   - Uses `Machine::inRandomOrder()->take($number)->get()` to select random machines
   - Dispatches `MachineUpdate` jobs synchronously via `dispatchSync()` (no queue worker required)
   - Includes 14 plausible status updates: Building, Installing Updates, Configuring, Ready, Pending Restart, In Maintenance, Provisioning, Failed, Imaging, Testing, Deploying Applications, Awaiting Approval, Offline, Online
   - Provides clear console feedback showing each machine update
   - Handles edge cases: validates number >= 1, checks for empty machine list

### Key Design Decisions

#### Why dispatchSync()?
For demos and local testing, requiring a queue worker running adds unnecessary friction. Using `dispatchSync()` executes jobs immediately inline, making it trivial to see live dashboard updates without additional setup.

#### Status Variety
The 14 statuses cover common states across different OS build systems (Windows, Linux, macOS, FreeBSD) to make demos more realistic and showcase the flexible status field design.

### Commands to Remember

```bash
# Simulate 5 random machine updates (default)
lando artisan app:simulate-api-updates

# Simulate 10 random machine updates
lando artisan app:simulate-api-updates --number=10

# Useful for quickly demonstrating live dashboard updates without API calls or queue workers
```

---

## Session Summary (2025-10-06)

### What We Accomplished

1. **Code Review & Refactoring**
   - Added missing return types to model relationships
   - Added missing `$fillable` properties to Lab and Log models
   - Refactored MachineUpdate job to use `array_filter()` with null coalescing operator
   - Created Form Request for API validation (moved from inline controller validation)
   - Added database indexes on `machines.name` and `machines.status` (lab_id already indexed via foreign key)

2. **Feature Enhancements**
   - Updated MachineList to load latest 10 logs for modal display
   - Added pagination to MachineDetails (50 logs per page)
   - Implemented Horizon authorization using `isAdmin()` gate

3. **Test Suite**
   - Created comprehensive Pest v4 test suite (39 tests, 89 assertions)
   - API endpoint tests with Sanctum authentication
   - Job tests covering all edge cases
   - Livewire component tests for both MachineList and MachineDetails

### Key Lessons & Best Practices

#### 1. Test Thoroughness Matters
**Issue**: Initial tests checked that updates worked but didn't verify *all* fields remained intact or that validation failures prevented job dispatch.

**Solution**:
- Added comprehensive field checks in `updates existing machine` test
- Added `Queue::assertNotPushed()` to all validation failure tests
- Explicitly test that fields *don't* change when they shouldn't

**Takeaway**: Tests should tell the complete story - both what happens AND what doesn't happen. This catches unintended side effects and documents expected behavior for new developers.

#### 2. Encapsulate Authorization Logic
**Issue**: Direct column checks (`$user->is_admin`) become painful to refactor when requirements change.

**Solution**: Created `User::isAdmin(): bool` helper method.

**Rationale**: When authorization logic evolves (e.g., adding email verification checks, role relationships, etc.), you only update one method instead of hunting down dozens of column checks throughout the codebase.

#### 3. Sanctum Testing Helpers
**Discovery**: `Sanctum::actingAs(User::factory()->create(), ['*'])` is much cleaner than creating tokens and passing Authorization headers.

**Benefit**: Reduces test boilerplate significantly and makes intent clearer.

#### 4. Don't Test Framework Code
**Principle**: We shouldn't test that Livewire pagination works (Caleb Porzio already did that). We should test:
- That pagination UI appears when expected (50+ logs)
- Our business logic (ordering, filtering, data display)
- Our configuration (50 per page)

#### 5. Explicit UI Elements for Testing
**Note**: For thorough testing of null/optional fields, consider adding explicit UI elements (like badges with IDs) that show "N/A" or specific states. Makes tests more reliable than just checking absence of text.

#### 6. MySQL Foreign Key Indexes
**Discovery**: MySQL/InnoDB automatically creates indexes on foreign key columns, so we don't need to manually add them.

### Tools That Helped

#### Laravel Boost MCP Server
The Laravel Boost MCP tool was invaluable for:
- Finding version-specific documentation (Pest 4, Sanctum testing, Horizon authorization)
- Searching across multiple Laravel ecosystem packages simultaneously
- Getting exact code examples for our installed package versions

Particularly helpful for:
- Sanctum `actingAs()` pattern for testing
- Horizon `viewHorizon` gate authorization
- Livewire 3 testing patterns

### Architecture Decisions

#### Status Field
Currently a free-form string to accommodate different OS build systems (Windows, Linux, macOS, FreeBSD). May evolve to enum later once we understand all possible values.

#### Lab Auto-Creation
Labs are auto-created via `firstOrCreate()` because:
- Multiple labs with varying naming conventions
- Labs split/merge over time
- Local teams know ground truth
- No central lab registry

Alternative considered: Pre-defined labs with validation. Rejected for PoC due to organizational complexity.

#### Logs Storage
Currently storing all machine update logs as JSON. Future considerations:
- Add retention policy (time-based or count-based)
- Consider storing only deltas rather than full state
- May need log pagination/archiving at scale

### Test Organization

```
tests/
├── Feature/
│   ├── Api/
│   │   └── MachineUpdateControllerTest.php (8 tests - auth, validation, queuing)
│   ├── Jobs/
│   │   └── MachineUpdateTest.php (8 tests - CRUD, lab handling, logs)
│   └── Livewire/
│       ├── MachineListTest.php (15 tests - display, filtering, pagination)
│       └── MachineDetailsTest.php (7 tests - display, logs, routing)
└── Unit/
```

**Total**: 39 tests, 89 assertions

### Commands to Remember

```bash
# Run all tests
lando php artisan test

# Run specific test file
lando php artisan test --filter=MachineUpdateTest

# Refresh database with new indexes
lando php artisan migrate:fresh
```

### Next Steps (Future Sessions)

1. Implement `SimulateApiUpdates` command for local testing
2. Add more feature tests as new functionality is added
3. Consider adding browser tests (Dusk/Playwright) for critical user flows
4. Implement log retention policy
5. Add more admin features using `isAdmin()` gate

### Notes for Future Developers

- **Don't rush to enum**: Status field is intentionally flexible until we know all possible values across different OS build systems
- **Test negative cases**: Always verify that failures/validation errors have the expected side effects (or lack thereof)
- **Use factories**: All test data should come from factories, even in simple scenarios
- **Queue::fake() vs Bus::fake()**: We prefer `Queue::fake()` for testing queued jobs as it's more semantically correct

### Patterns to Follow

1. **Form Requests for API validation** - Keeps controllers thin
2. **Return types on relationships** - Makes IDE autocomplete work better
3. **Explicit `$fillable` arrays** - Even if "obvious", be explicit
4. **Helper methods for authorization** - `isAdmin()` instead of `$user->is_admin`
5. **Test thoroughness** - Check all fields, not just the ones that changed
6. **Negative assertions** - Test that things DON'T happen when they shouldn't

---

*Remember: "Picky" developers write better code. Thoroughness today prevents bugs tomorrow.*
