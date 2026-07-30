<div x-data="{ open: @entangle('showModal') }">

    {{-- Instant open — no server round-trip, Alpine flips it immediately --}}
    <button type="button" x-on:click="open = true" class="cc-btn cc-btn-primary">+ Add Student</button>

    <div x-show="open" x-cloak
         style="position:fixed; inset:0; background:rgba(14,31,54,.55); display:flex; align-items:center; justify-content:center; z-index:50; padding:70px;">
        <div class="cc-wrap" style="max-width:640px; width:100%; max-height:90vh; overflow-y:auto;">

            <div class="cc-header">
                <div class="cc-header-left">
                    <div class="cc-icon">👤</div>
                    <div>
                        <div class="cc-title">Add New Student</div>
                        <div class="cc-subtitle">Create account & generate payment link</div>
                    </div>
                </div>
                <button type="button" x-on:click="open = false" class="cc-btn cc-btn-outline">✕</button>
            </div>

            <div class="cc-grid-2">
                <div class="cc-field">
                    <label class="cc-label">Full Name</label>
                    <input type="text" wire:model="name" class="cc-input" placeholder="Student full name">
                    @error('name') <span class="cc-error">{{ $message }}</span> @enderror
                </div>
                <div class="cc-field">
                    <label class="cc-label">Email</label>
                    <input type="email" wire:model="email" class="cc-input" placeholder="student@email.com">
                    @error('email') <span class="cc-error">{{ $message }}</span> @enderror
                </div>
                <div class="cc-field">
                    <label class="cc-label">Contact Number</label>
                    <input type="text" wire:model="contact_no" class="cc-input" placeholder="+91 98xxxxxxx0">
                    @error('contact_no') <span class="cc-error">{{ $message }}</span> @enderror
                </div>
                <div class="cc-field">
                    <label class="cc-label">Enroll For</label>
                    <select wire:model.live="enrollment_type" class="cc-select">
                        <option value="demo">Demo</option>
                        <option value="course">Course</option>
                    </select>
                </div>

                <div class="cc-field">
                    <label class="cc-label">Subject</label>
                    <select wire:model.live="category_id" class="cc-select">
                        <option value="">-- Select Subject --</option>
                        @foreach ($this->categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="cc-error">{{ $message }}</span> @enderror
                </div>

                @if ($enrollment_type === 'course')
                    <div class="cc-field">
                        <label class="cc-label">Course</label>
                        <select wire:model.live="course_id" class="cc-select" @disabled(!$category_id)>
                            <option value="">-- Select Course --</option>
                            @foreach ($this->courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id') <span class="cc-error">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div class="cc-field">
                    <label class="cc-label">
                        Price (₹)
                        @if ($enrollment_type === 'course' && $original_price)
                            <span style="font-weight:400; color:var(--text-muted); text-transform:none;">— default ₹{{ number_format($original_price) }}</span>
                        @endif
                    </label>
                    <input type="number" min="0" wire:model="price" class="cc-input">
                    <span class="cc-hint">Auto-filled from course price — you can edit this per student.</span>
                    @error('price') <span class="cc-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="cc-actions">
                {{-- SweetAlert confirmation BEFORE hitting the server --}}
                <button type="button"
    onclick="confirmCreateStudent()"
    class="cc-btn cc-btn-primary">
    Create Student & Generate Link
</button>
                <button type="button" x-on:click="open = false" class="cc-btn cc-btn-outline">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmCreateStudent() {
    const studentName =
        document.querySelector('[wire\\:model="name"]')?.value || 'this student';

    Swal.fire({
        title: 'Create this student?',
        html: `A username, password and payment link will be generated for <strong>${studentName}</strong>.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Create',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0947a8',
        cancelButtonColor: '#5a718a',
    }).then((result) => {
       if (result.isConfirmed) {
            @this.call('save');
        }
    });
}
</script>
<script>
    
    document.addEventListener('livewire:init', () => {
        Livewire.on('student-created', (payload) => {
            const data = Array.isArray(payload) ? payload[0] : payload;

            Swal.fire({
                title: '✓ Student account has been created successfully. A payment link and login credentials have been sent to the student email address.',
                icon: 'success',
                html: `
                    <div style="text-align:left; font-size:13.5px; line-height:1.9;">
                        <div><strong>Username:</strong> ${data.username}</div>
                        <div><strong>Password:</strong> ${data.password}</div>
                        <div style="margin-top:8px;"><strong>Payment Link:</strong></div>
                        <div style="display:flex; gap:6px; margin-top:4px;">
                            <input id="swal-payment-link" readonly value="${data.link}"
                                   style="flex:1; font-size:11.5px; padding:6px 8px; border:1px solid #d6e4f5; border-radius:6px;">
                            <button id="swal-copy-btn" type="button"
                                    style="background:#0947a8; color:#fff; border:none; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                                Copy
                            </button>
                        </div>
                        <p style="margin-top:10px; font-size:11.5px; color:#5a718a;">
                            Copy these now — the password won't be shown again. Share the link with the student to collect payment.
                        </p>
                    </div>
                `,
                confirmButtonText: 'Done',
                confirmButtonColor: '#0947a8',
                didOpen: () => {
                    document.getElementById('swal-copy-btn').addEventListener('click', () => {
                        const input = document.getElementById('swal-payment-link');
                        input.select();
                        navigator.clipboard.writeText(input.value);

                        const btn = document.getElementById('swal-copy-btn');
                        const original = btn.textContent;
                        btn.textContent = 'Copied!';
                        setTimeout(() => { btn.textContent = original; }, 1500);
                    });
                }
            }).then(() => {
                // Reset the form and close the modal after the admin dismisses the alert
                Livewire.dispatch('close-student-modal');
            });
        });
    });
</script>