<!-- Basic Information -->
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h5 class="text-lg font-semibold">Basic Information</h5>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Event Title <span class="text-red-600">*</span></label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500 focus:border-burgundy-500 @error('title') border-red-500 @enderror" id="title" 
                    name="title" value="{{ old('title', $event->title ?? '') }}" required>
                @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">Event Type <span class="text-red-600">*</span></label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500 @error('event_type') border-red-500 @enderror" id="event_type" 
                    name="event_type" required onchange="togglePriceField()">
                    <option value="">Select Type</option>
                    <option value="free" {{ old('event_type', $event->event_type ?? '') === 'free' ? 'selected' : '' }}>Free Event</option>
                    <option value="paid" {{ old('event_type', $event->event_type ?? '') === 'paid' ? 'selected' : '' }}>Paid Event</option>
                </select>
                @error('event_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-600">*</span></label>
            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500 @error('description') border-red-500 @enderror" id="description" 
                name="description" rows="4" required>{{ old('description', $event->description ?? '') }}</textarea>
            <p class="text-gray-500 text-xs mt-1">Detailed description of your event</p>
            @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<!-- Location & Venue -->
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h5 class="text-lg font-semibold">Location & Venue</h5>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label for="city_id" class="block text-sm font-medium text-gray-700 mb-2">City <span class="text-red-600">*</span></label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500 @error('city_id') border-red-500 @enderror" id="city_id" 
                    name="city_id" required>
                    <option value="">Select City</option>
                    @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $event->city_id ?? '') == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                    @endforeach
                </select>
                @error('city_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="category_id" name="category_id">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $event->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="organizer_id" class="block text-sm font-medium text-gray-700 mb-2">Organizer</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="organizer_id" name="organizer_id">
                    <option value="{{ auth()->id() }}" {{ old('organizer_id', $event->organizer_id ?? auth()->id()) == auth()->id() ? 'selected' : '' }}>
                        {{ auth()->user()?->name ?? 'Me' }}
                    </option>
                </select>
                <p class="text-gray-500 text-xs mt-1">Your profile</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="venue_name" class="block text-sm font-medium text-gray-700 mb-2">Venue Name <span class="text-red-600">*</span></label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500 @error('venue_name') border-red-500 @enderror" 
                    id="venue_name" name="venue_name" value="{{ old('venue_name', $event->venue_name ?? '') }}" 
                    placeholder="e.g., Grand Hall, Hotel Aurora" required>
                @error('venue_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="venue_address" class="block text-sm font-medium text-gray-700 mb-2">Venue Address <span class="text-red-600">*</span></label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500 @error('venue_address') border-red-500 @enderror" 
                    id="venue_address" name="venue_address" value="{{ old('venue_address', $event->venue_address ?? '') }}" 
                    placeholder="Full address of the venue" required>
                @error('venue_address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                <input type="number" step="0.000001" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="latitude" 
                    name="latitude" value="{{ old('latitude', $event->latitude ?? '') }}" 
                    placeholder="e.g., 23.8103">
            </div>
            <div>
                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                <input type="number" step="0.000001" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="longitude" 
                    name="longitude" value="{{ old('longitude', $event->longitude ?? '') }}" 
                    placeholder="e.g., 90.3563">
            </div>
        </div>
    </div>
</div>

<!-- Date & Time -->
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h5 class="text-lg font-semibold">Date & Time</h5>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time <span class="text-red-600">*</span></label>
                <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500 @error('start_datetime') border-red-500 @enderror" 
                    id="start_datetime" name="start_datetime" 
                    value="{{ old('start_datetime', $event->start_datetime?->format('Y-m-d\TH:i') ?? '') }}" required>
                @error('start_datetime')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-2">End Date & Time <span class="text-red-600">*</span></label>
                <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500 @error('end_datetime') border-red-500 @enderror" 
                    id="end_datetime" name="end_datetime" 
                    value="{{ old('end_datetime', $event->end_datetime?->format('Y-m-d\TH:i') ?? '') }}" required>
                @error('end_datetime')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="registration_deadline" class="block text-sm font-medium text-gray-700 mb-2">Registration Deadline</label>
                <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="registration_deadline" 
                    name="registration_deadline" 
                    value="{{ old('registration_deadline', $event->registration_deadline?->format('Y-m-d\TH:i') ?? '') }}">
                <p class="text-gray-500 text-xs mt-1">When to close registration</p>
            </div>

            <div>
                <label for="booking_close_datetime" class="block text-sm font-medium text-gray-700 mb-2">Booking Close</label>
                <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="booking_close_datetime" 
                    name="booking_close_datetime" 
                    value="{{ old('booking_close_datetime', $event->booking_close_datetime?->format('Y-m-d\TH:i') ?? '') }}">
                <p class="text-gray-500 text-xs mt-1">When to stop accepting bookings</p>
            </div>
        </div>
    </div>
</div>

<!-- Pricing & Capacity -->
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h5 class="text-lg font-semibold">Pricing & Capacity</h5>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div id="priceField" style="display: none;">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-md">৳</span>
                    <input type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-r-md focus:outline-none focus:ring-burgundy-500" id="price" 
                        name="price" value="{{ old('price', $event->price ?? '') }}">
                </div>
                <p class="text-gray-500 text-xs mt-1">Price in BDT</p>
            </div>

            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity <span class="text-red-600">*</span></label>
                <input type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500 @error('capacity') border-red-500 @enderror" 
                    id="capacity" name="capacity" value="{{ old('capacity', $event->capacity ?? '') }}" required>
                @error('capacity')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                <p class="text-gray-500 text-xs mt-1">Maximum attendees</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Certificates</label>
                <div class="flex items-center h-10">
                    <input type="checkbox" id="certificates_enabled" 
                        name="certificates_enabled" value="1" 
                        {{ old('certificates_enabled', $event->certificates_enabled ?? 0) ? 'checked' : '' }}
                        class="rounded">
                    <label for="certificates_enabled" class="ml-2 text-sm">
                        Auto-issue certificates
                    </label>
                </div>
                <p class="text-gray-500 text-xs mt-1">To attendees</p>
            </div>

            <div>
                <label for="certificate_template_id" class="block text-sm font-medium text-gray-700 mb-2">Certificate Template</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="certificate_template_id" name="certificate_template_id">
                    <option value="">Select Template</option>
                    @foreach($certificateTemplates as $template)
                    <option value="{{ $template->id }}" {{ old('certificate_template_id', $event->certificate_template_id ?? '') == $template->id ? 'selected' : '' }}>
                        {{ $template->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Mentors -->
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h5 class="text-lg font-semibold">Mentors</h5>
    </div>
    <div class="p-6">
        <label for="mentors" class="block text-sm font-medium text-gray-700 mb-2">Assign Mentors</label>
        <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="mentors" name="mentors[]" multiple size="6">
            @foreach($mentors as $mentor)
            <option value="{{ $mentor->id }}" 
                {{ in_array($mentor->id, old('mentors', $event->mentors->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                {{ $mentor->name }}
            </option>
            @endforeach
        </select>
        <p class="text-gray-500 text-xs mt-1">Hold Ctrl/Cmd to select multiple mentors</p>
    </div>
</div>

<!-- Media -->
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h5 class="text-lg font-semibold">Banner Image</h5>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <label for="banner_image" class="block text-sm font-medium text-gray-700 mb-2">Upload Banner</label>
                <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-md" id="banner_image" name="banner_image" 
                    accept="image/*" onchange="previewImage(this)">
                <p class="text-gray-500 text-xs mt-1">Recommended: 1200x400px. Max 5MB</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                <div id="imagePreview">
                    @if($event->banner_image ?? false)
                    <picture>
                        <source srcset="{{ preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', asset('storage/' . $event->banner_image)) }}" type="image/webp">
                        <img src="{{ asset('storage/' . $event->banner_image) }}" class="rounded" style="max-height: 150px; width: 100%; object-fit: cover;" loading="lazy">
                    </picture>
                    @else
                    <div class="bg-gray-100 rounded text-center py-8">
                        <p class="text-gray-500 text-sm">No image</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Information -->
<div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b">
        <h5 class="text-lg font-semibold">Additional Information</h5>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="refund_policy" class="block text-sm font-medium text-gray-700 mb-2">Refund Policy</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="refund_policy" name="refund_policy" rows="3">{{ old('refund_policy', $event->refund_policy ?? '') }}</textarea>
            </div>

            <div>
                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="requirements" name="requirements" rows="3">{{ old('requirements', $event->requirements ?? '') }}</textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" name="status">
                    <option value="draft" {{ old('status', $event->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $event->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="cancelled" {{ old('status', $event->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Featured</label>
                <div class="flex items-center h-10">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" 
                        {{ old('is_featured', $event->is_featured ?? 0) ? 'checked' : '' }}
                        class="rounded">
                    <label for="is_featured" class="ml-2 text-sm">
                        Feature this event
                    </label>
                </div>
            </div>

            <div>
                <label for="featured_until" class="block text-sm font-medium text-gray-700 mb-2">Featured Until</label>
                <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-burgundy-500" id="featured_until" name="featured_until" 
                    value="{{ old('featured_until', $event->featured_until?->format('Y-m-d\TH:i') ?? '') }}">
            </div>
        </div>
    </div>
</div>

<!-- Form Actions -->
<div class="flex justify-between gap-2 mb-4">
    <a href="{{ route('admin.events.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
        Back
    </a>
    <div class="flex gap-2">
        <button type="reset" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
            Reset
        </button>
        <button type="submit" class="px-4 py-2 bg-burgundy-600 text-white rounded-md hover:bg-burgundy-700">
            {{ isset($event) ? 'Update Event' : 'Create Event' }}
        </button>
    </div>
</div>

<script>
function togglePriceField() {
    const eventType = document.getElementById('event_type').value;
    const priceField = document.getElementById('priceField');
    priceField.style.display = eventType === 'paid' ? 'block' : 'none';
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = `<img src="${e.target.result}" class="rounded" style="max-height: 150px; width: 100%; object-fit: cover;" loading="lazy">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePriceField();
});
</script>
