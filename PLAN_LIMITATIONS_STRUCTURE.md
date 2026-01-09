# Plan Limitations Display Structure - Complete Guide

## 📋 Overview
The plan limitations system is a modular architecture that defines what features and capabilities are available in each subscription plan. The system uses a mapping table (`planlimitation_mapping`) to link plans with their specific limitations and configurable values.

---

## 🏗️ Data Models & Database

### 1. **PlanLimitation Model**
- **Location**: [Modules/Subscriptions/Models/PlanLimitation.php](Modules/Subscriptions/Models/PlanLimitation.php)
- **Purpose**: Defines the available limitation types (e.g., device-limit, video-cast, ads, download-status, profile-limit, supported-device-type)
- **Fields**:
  - `id`: Primary key
  - `title`: Display name (e.g., "Device Limit", "Video Cast")
  - `slug`: Identifier (e.g., "device-limit")
  - `status`: Active/Inactive flag
  - `description`: Additional info

### 2. **PlanLimitationMapping Model**
- **Location**: [Modules/Subscriptions/Models/PlanLimitationMapping.php](Modules/Subscriptions/Models/PlanLimitationMapping.php)
- **Purpose**: Maps specific limitations to plans with their configured values
- **Table**: `planlimitation_mapping`
- **Fields**:
  - `id`: Primary key
  - `plan_id`: Foreign key to plans
  - `planlimitation_id`: Foreign key to plan_limitations
  - `limitation_slug`: Slug identifier (video-cast, ads, device-limit, download-status, profile-limit, supported-device-type)
  - `limitation_value`: Boolean flag (1/0) for enable/disable
  - `limit`: JSON or scalar value containing the specific limit (e.g., device count, quality options, device types)
  - `created_by`, `updated_by`, `deleted_by`: Audit fields
  - `timestamps`: Created/Updated at

**Relationship**:
```php
public function limitation_data()
{
    return $this->belongsTo(PlanLimitation::class, 'planlimitation_id', 'id')->withTrashed();
}
```

### 3. **Plan Model**
- **Location**: [Modules/Subscriptions/Models/Plan.php](Modules/Subscriptions/Models/Plan.php)
- **Relationship**:
```php
public function planLimitation()
{
    return $this->hasMany(PlanLimitationMapping::class, 'plan_id', 'id')->with('limitation_data');
}
```

---

## 🗄️ Database Migration

**File**: [Modules/Subscriptions/database/migrations/2023_05_02_111622_create_planlimitation_mapping_table.php](Modules/Subscriptions/database/migrations/2023_05_02_111622_create_planlimitation_mapping_table.php)

```php
Schema::create('planlimitation_mapping', function (Blueprint $table) {
    $table->id();
    $table->Integer('plan_id')->nullable();
    $table->Integer('planlimitation_id')->nullable();
    $table->string('limitation_slug')->nullable();
    $table->Integer('limitation_value')->nullable();  // 1 = enabled, 0 = disabled
    $table->longtext('limit')->nullable();            // JSON for complex data, scalar for simple values
    $table->integer('created_by')->unsigned()->nullable();
    $table->integer('updated_by')->unsigned()->nullable();
    $table->integer('deleted_by')->unsigned()->nullable();
    $table->softDeletes();
    $table->timestamps();
});
```

---

## 🎯 Limitation Types

The system supports the following limitation types:

| Slug | Title | Value Type | Example |
|------|-------|-----------|---------|
| `video-cast` | Video Cast | Boolean | `limitation_value`: 1 (enabled) |
| `ads` | Ads | Boolean | `limitation_value`: 0 (ad-free) |
| `device-limit` | Device Limit | Scalar | `limit`: "2" (2 devices allowed) |
| `profile-limit` | Profile Limit | Scalar | `limit`: "4" (4 profiles allowed) |
| `download-status` | Download Status | JSON | `limit`: `{"480p":1,"720p":1,"1080p":0,...}` |
| `supported-device-type` | Supported Device Type | JSON | `limit`: `{"tablet":"0","laptop":"0","mobile":"1"}` |

---

## 🛣️ Controller Methods

### **Backend Plan Controller**
**Location**: [Modules/Subscriptions/Http/Controllers/Backend/PlanController.php](Modules/Subscriptions/Http/Controllers/Backend/PlanController.php)

#### 1. **create() - Line ~345**
- Fetches all active plan limitations: `PlanLimitation::where('status', 1)->get()`
- Fetches video quality options: `Constant::where('type', 'video_quality')->get()`
- Returns `view('subscriptions::backend.plan.form', compact('planLimits', 'downloadoptions', ...))`
- **Purpose**: Display the create plan form with available limitations and options

#### 2. **store() - Line ~367**
- Validates plan data via `PlanRequest`
- Creates the plan record
- **Limitation Handling** (Lines 397-426):
  - Loops through `$request->input('limits')`
  - For each limitation:
    - **device-limit**: Takes `device_limit_value` input
    - **profile-limit**: Takes `profile_limit_value` input
    - **download-status**: Converts selected quality options to JSON: `json_encode($downloadOptions)`
    - **supported-device-type**: Converts device type selections to JSON: `json_encode($deviceTypes)`
    - Creates `PlanLimitationMapping` records with the plan_id, limitation data, and value

#### 3. **edit($id) - Line ~469**
- Fetches existing plan: `Plan::findOrFail($id)`
- Fetches active plan limitations: `PlanLimitation::where('status', 1)->get()`
- Fetches plan's limitation mappings: `PlanLimitationMapping::where('plan_id', $id)->get()`
- Decodes limits into an array for display: `json_decode($mapping->limit, true)`
- Passes to view: `view('subscriptions::backend.plan.edit_form', compact(..., 'limits', ...))`
- **Purpose**: Pre-populate the edit form with current limitation values

#### 4. **update($request, $id) - Line ~518**
- Similar to store() but uses `PlanLimitationMapping::updateOrCreate()`
- Updates existing limitation mappings or creates new ones
- Handles reordering of plan levels if level changed
- Processes Order Bumps (if any)

---

## 🎨 Blade Templates

### **Create Plan Form**
**File**: [Modules/Subscriptions/Resources/views/backend/plan/form.blade.php](Modules/Subscriptions/Resources/views/backend/plan/form.blade.php)

**Lines 250-350 (Plan Limitations Section)**:
- **Structure**:
  ```blade
  @foreach($planLimits as $planLimit)
      <!-- Limitation Toggle Switch -->
      <div class="col-md-6">
          <label>{{ $planLimit->title }}</label>
          <div class="form-control">
              <input type="checkbox" name="limits[{{ $planLimit->id }}][value]" 
                     id="{{ $planLimit->slug }}" 
                     value="1" 
                     onchange="toggleQualitySection()">
          </div>
      </div>
      
      <!-- Conditional Input Fields -->
      @if($planLimit->slug == 'device-limit')
          <input type="number" name="device_limit_value" id="device_limit_value" class="d-none">
      @endif
      
      @if($planLimit->slug == 'download-status')
          <!-- Quality Checkboxes (480p, 720p, 1080p, etc.) -->
      @endif
      
      @if($planLimit->slug == 'supported-device-type')
          <!-- Device Type Checkboxes (tablet, laptop, mobile) -->
      @endif
  @endforeach
  ```

- **Hidden Fields**: Store limitation metadata
  ```blade
  <input type="hidden" name="limits[{{ $planLimit->id }}][planlimitation_id]" value="{{ $planLimit->id }}">
  <input type="hidden" name="limits[{{ $planLimit->id }}][limitation_slug]" value="{{ $planLimit->slug }}">
  <input type="hidden" name="limits[{{ $planLimit->id }}][value]" value="0">
  ```

### **Edit Plan Form**
**File**: [Modules/Subscriptions/Resources/views/backend/plan/edit_form.blade.php](Modules/Subscriptions/Resources/views/backend/plan/edit_form.blade.php)

**Lines 430-600 (Plan Limitations Section)**:
- **Difference from create form**: Pre-populates values from `$limits` array
- **Device Limit Input** (Lines 477-487):
  ```blade
  @if($planLimit->slug == 'device-limit')
  <input type="number" name="device_limit_value" 
         value="{{ isset($limits[$planLimit->slug]) ? $limits[$planLimit->slug]['limit'] ?? 0 : 0 }}">
  @endif
  ```

- **Download Status Options** (Lines 491-510):
  ```blade
  @if($planLimit->slug == 'download-status')
  @foreach($downloadoptions as $option)
      <input type="checkbox" name="download_options[{{ $option->value }}]" 
             {{ (isset($downloadOptions[$option->value]) && $downloadOptions[$option->value] == "1") ? 'checked' : '' }}>
  @endforeach
  @endif
  ```

- **Supported Device Types** (Lines 519-526):
  ```blade
  @if($planLimit->slug == 'supported-device-type')
  @foreach(['tablet', 'laptop', 'mobile'] as $option)
      <input type="checkbox" name="supported_device_types[{{ $option }}]" 
             {{ isset($limits['supported-device-type'][$option]) && $limits['supported-device-type'][$option] ? 'checked' : '' }}>
  @endforeach
  @endif
  ```

---

## 📜 Plan Limitations CRUD Pages

### **Index/List Page**
**File**: [Modules/Subscriptions/Resources/views/backend/planlimitation/index.blade.php](Modules/Subscriptions/Resources/views/backend/planlimitation/index.blade.php)

- Displays all plan limitations with pagination/datatable
- Search and filter by status
- Shows: ID, Title, Status, Created Date
- Actions: Edit, Delete, Restore, Force Delete

### **Create/Edit Forms**
**File**: [Modules/Subscriptions/Resources/views/backend/planlimitation/form.blade.php](Modules/Subscriptions/Resources/views/backend/planlimitation/form.blade.php)
**File**: [Modules/Subscriptions/Resources/views/backend/planlimitation/edit_form.blade.php](Modules/Subscriptions/Resources/views/backend/planlimitation/edit_form.blade.php)

- Fields: Title, Status, Description
- Simple forms for managing the limitation types themselves (not the mappings)

---

## 🔌 PlanLimitationMapping Controller (API)
**File**: [Modules/Subscriptions/Http/Controllers/Backend/API/PlanLimitationController.php](Modules/Subscriptions/Http/Controllers/Backend/API/PlanLimitationController.php)

- **index()**: List all plan limitations
- **show(PlanLimitation $planlimitation)**: Get single limitation
- **store(PlanLimitationRequest $request)**: Create limitation
- **update(PlanLimitationRequest $request, PlanLimitation $planlimitation)**: Update limitation
- **destroy(PlanLimitation $planlimitation)**: Delete limitation

---

## 🔄 Data Transformers (API Resources)

### **PlanlimitationMappingResource**
**File**: [Modules/Subscriptions/Transformers/PlanlimitationMappingResource.php](Modules/Subscriptions/Transformers/PlanlimitationMappingResource.php)

**Purpose**: Transform plan limitation mapping data for API responses

**Output Structure**:
```php
return [
    'id' => $this->id,
    'planlimitation_id' => $this->planlimitation_id,
    'limitation_title' => optional($this->limitation_data)->title,
    'limitation_value' => $this->limitation_value,  // 1 or 0
    'limit' => $limit,  // Decoded JSON or scalar value
    'slug' => optional($this->limitation_data)->slug,
    'status' => optional($this->limitation_data)->status,
    'message' => $message,  // Human-readable description of the limitation
];
```

**Example Messages**:
- Video Cast (enabled): `"Cast videos to your TV with ease."`
- Video Cast (disabled): `"Video casting is not available with this plan."`
- Device Limit: `"Stream on up to 2 devices simultaneously."`
- Download Status (disabled): `"Download feature is not available with this plan."`

---

## 🔄 JavaScript Handling

### **Form Toggle Functions** (in edit_form.blade.php)

**toggleQualitySection()** - Handles visibility of conditional limitation inputs:
- Checks if "device-limit" checkbox is enabled
- Shows/hides the device limit input field
- Sets `required` attribute when shown, removes when hidden
- Similar logic for:
  - `#deviceLimitInput`
  - `#profileLimitInput`
  - `#DownloadStatus`
  - `#supportedDeviceTypeInput`

### **Bump Management** (Lines 412-441 in edit_form.blade.php)
- Separate from limitations but in the same form
- Handles add/remove of "Order Bumps" (upsell products)

---

## 🌐 Seeder Data

**File**: [Modules/Subscriptions/database/seeders/PlanlimitationMappingTableSeeder.php](Modules/Subscriptions/database/seeders/PlanlimitationMappingTableSeeder.php)

**Example Data**:
```php
[
    'id' => 1,
    'plan_id' => 1,
    'planlimitation_id' => 1,
    'limitation_slug' => 'video-cast',
    'limitation_value' => 1,      // Enabled
    'limit' => NULL,              // Boolean type, no additional limit
],
[
    'id' => 3,
    'plan_id' => 1,
    'planlimitation_id' => 3,
    'limitation_slug' => 'device-limit',
    'limitation_value' => 1,      // Enabled
    'limit' => '1',               // Limited to 1 device
],
[
    'id' => 4,
    'plan_id' => 1,
    'planlimitation_id' => 4,
    'limitation_slug' => 'download-status',
    'limitation_value' => 1,      // Downloads enabled
    'limit' => '{"480p":1,"720p":1,"1080p":1,"1440p":0,"2K":0,"4K":0,"8K":0}',
],
```

---

## 🔗 Integration Points

### **Where PlanLimitationMapping is Used**

1. **PaymentController** (Frontend)
   - [Modules/Frontend/Http/Controllers/PaymentController.php#L403](Modules/Frontend/Http/Controllers/PaymentController.php#L403)
   - Fetches and transforms limitations: `PlanlimitationMappingResource::collection($plan->planLimitation)`
   - Used to display plan features during checkout

2. **SubscriptionController** (API)
   - [Modules/Subscriptions/Http/Controllers/Backend/API/SubscriptionController.php#L61](Modules/Subscriptions/Http/Controllers/Backend/API/SubscriptionController.php#L61)
   - Returns plan limitations in subscription details

3. **WebHookController** (Payment Processing)
   - [app/Http/Controllers/WebHookController.php#L78](app/Http/Controllers/WebHookController.php#L78)
   - Uses limitations when processing payment webhooks

4. **PlanResource** (API Response)
   - [Modules/Subscriptions/Transformers/PlanResource.php#L30](Modules/Subscriptions/Transformers/PlanResource.php#L30)
   - Includes `PlanlimitationMappingResource::collection($this->planLimitation)` in plan data

---

## 🏁 Data Flow Summary

### **Create Plan with Limitations**
1. User accesses `/admin/plans/create`
2. `PlanController@create()` fetches active limitations and passes to form
3. Form renders limitation toggles and conditional input fields
4. User selects limitations and sets values (device count, quality options, etc.)
5. Form submits with nested array: `limits[id][planlimitation_id]`, `limits[id][limitation_slug]`, `limits[id][value]`, etc.
6. `PlanController@store()` processes each limitation:
   - Creates `PlanLimitationMapping` record per limitation
   - Encodes JSON data for complex limitations
7. Plan created with all limitation mappings

### **Edit Plan Limitations**
1. User accesses `/admin/plans/{id}/edit`
2. `PlanController@edit()` fetches plan and existing mappings
3. Decodes `limit` values and passes to form as `$limits` array
4. Form pre-populates checkboxes and input fields
5. User modifies limitations
6. `PlanController@update()` uses `updateOrCreate()` to sync mappings
7. New mappings created, existing ones updated, deleted ones removed

### **Display to Users (Frontend)**
1. PaymentController fetches plan with: `$plan->planLimitation`
2. Transforms via `PlanlimitationMappingResource`
3. Resource decodes JSON, generates human-readable messages
4. Frontend displays features/limitations in plan card or checkout page

---

## 📝 Configuration & Language Files

**File**: [lang/en/plan_limitation.php](lang/en/plan_limitation.php)

```php
return [
    'title' => 'Plan Limits',
    'lbl_title' => 'Title',
    'lbl_status' => 'Status',
    'lbl_description' => 'Description',
    'add_planlimit_title' => 'Add New Plan Limitation',
    'edit_planlimit_title' => 'Edit Plan Limitation'
];
```

---

## 🎯 Key Takeaways

1. **PlanLimitationMapping** links plans with limitations and their specific values
2. **Limitations** can be:
   - **Boolean-only**: video-cast, ads (no additional value in `limit` field)
   - **Scalar**: device-limit, profile-limit (numeric value in `limit` field)
   - **JSON Complex**: download-status, supported-device-type (JSON object in `limit` field)
3. **UI Flow**: Toggle enables/disables, conditional fields appear for scalar/complex limitations
4. **Form Handling**: Nested arrays in POST data, processed in loops in controller
5. **API Responses**: PlanlimitationMappingResource transforms data with human-readable messages
6. **Integration**: PaymentController and API endpoints use the resources for displaying limitations to users
