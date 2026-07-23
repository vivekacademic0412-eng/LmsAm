<div class="uop">
<style>
.uop *{ box-sizing:border-box; }
.uop{ display:flex; flex-direction:column; gap:20px; color:var(--text); }
.uop-header h1{ font-size:20px; font-weight:700; color:var(--text-main,var(--text)); }
.uop-header p{ font-size:13px; color:var(--text-muted); margin-top:4px; }

.uop-card{ background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-card); overflow:hidden; }
.uop-card-head{ display:flex; align-items:center; justify-content:space-between; gap:10px; padding:16px 20px; border-bottom:1px solid var(--border); background:var(--bg2); }
.uop-card-head h3{ font-size:14.5px; font-weight:700; display:flex; align-items:center; gap:8px; }
.uop-card-head h3 i{ color:var(--brand-primary,var(--primary)); }
.uop-lock-badge{ font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; display:inline-flex; align-items:center; gap:5px; }
.uop-lock-badge.open{ background:rgba(22,163,74,.12); color:var(--success); }
.uop-lock-badge.locked{ background:rgba(220,38,38,.1); color:var(--danger); }

.uop-card-body{ padding:20px; }
.uop-grid{ display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.uop-grid .span-2{ grid-column:1/-1; }
@media (max-width:640px){ .uop-grid{ grid-template-columns:1fr; } }

.uop-field label{ display:block; font-size:.82rem; font-weight:600; margin-bottom:6px; color:var(--text); }
.uop-input, .uop-select, .uop-textarea{
    width:100%; background:var(--input-bg); border:1px solid var(--input-border); color:var(--text);
    border-radius:var(--radius-sm); padding:10px 13px; font-size:.87rem; font-family:inherit;
}
.uop-input:focus, .uop-select:focus, .uop-textarea:focus{ outline:none; border-color:var(--input-focus); box-shadow:0 0 0 3px var(--primary-glow); }
.uop-input:disabled, .uop-select:disabled, .uop-textarea:disabled{ opacity:.6; cursor:not-allowed; }
.uop-error{ color:var(--danger); font-size:.76rem; margin-top:5px; display:block; }

.uop-card-footer{ padding:14px 20px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; }
.uop-btn{ border:none; border-radius:var(--radius-sm); font-weight:600; font-size:.84rem; padding:10px 20px; cursor:pointer; background:linear-gradient(135deg,var(--brand-primary,var(--primary)),var(--brand-secondary,var(--accent2))); color:#fff; display:inline-flex; align-items:center; gap:8px; }
.uop-btn:hover{ opacity:.92; }
.uop-btn:disabled{ opacity:.5; cursor:not-allowed; }

.uop-locked-note{ font-size:.78rem; color:var(--text-muted); font-style:italic; padding:0 20px 16px; }
</style>

<div class="uop-header">
    <h1>My Onboarding Details</h1>
    <p>Review and update the information you submitted during onboarding.</p>
</div>

{{-- Personal --}}
<div class="uop-card">
    <div class="uop-card-head">
        <h3><i class="fa-solid fa-id-card"></i> Personal Details</h3>
        <span class="uop-lock-badge {{ $editable['personal'] ? 'open' : 'locked' }}">
            <i class="fa-solid {{ $editable['personal'] ? 'fa-lock-open' : 'fa-lock' }}"></i>
            {{ $editable['personal'] ? 'Editable' : 'Locked by admin' }}
        </span>
    </div>
    <div class="uop-card-body">
        <div class="uop-grid">
            <div class="uop-field">
                <label>First name</label>
                <input type="text" wire:model="first_name" class="uop-input" @disabled(!$editable['personal'])>
                @error('first_name') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Last name</label>
                <input type="text" wire:model="last_name" class="uop-input" @disabled(!$editable['personal'])>
                @error('last_name') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Date of birth</label>
                <input type="date" wire:model="dob" class="uop-input" @disabled(!$editable['personal'])>
                @error('dob') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Gender</label>
                <select wire:model="gender" class="uop-select" @disabled(!$editable['personal'])>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                    <option value="prefer_not_to_say">Prefer not to say</option>
                </select>
            </div>
            <div class="uop-field">
                <label>City / District</label>
                <select wire:model="city_id" class="uop-select" @disabled(!$editable['personal'])>
                    <option value="">Select city</option>
                    @foreach($cities as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}{{ $c->state ? ', '.$c->state : '' }}</option>
                    @endforeach
                </select>
                @error('city_id') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Mobile number</label>
                <input type="text" wire:model="mobile_number" maxlength="10" class="uop-input" @disabled(!$editable['personal'])>
                @error('mobile_number') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>WhatsApp number</label>
                <input type="text" wire:model="whatsapp_number" maxlength="10" class="uop-input" @disabled(!$editable['personal'])>
                @error('whatsapp_number') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>ID proof type</label>
                <select wire:model="id_proof_type" class="uop-select" @disabled(!$editable['personal'])>
                    <option value="aadhaar">Aadhaar</option>
                    <option value="passport">Passport</option>
                    <option value="pan">PAN</option>
                    <option value="voter_id">Voter ID</option>
                    <option value="driving_licence">Driving licence</option>
                </select>
            </div>
            <div class="uop-field">
                <label>ID number</label>
                <input type="text" wire:model="id_number" class="uop-input" @disabled(!$editable['personal'])>
                @error('id_number') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field span-2">
                <label>Residential address</label>
                <textarea wire:model="residential_address" rows="2" class="uop-textarea" @disabled(!$editable['personal'])></textarea>
                @error('residential_address') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    @if($editable['personal'])
    <div class="uop-card-footer">
        <button type="button" wire:click="updatePersonal" class="uop-btn" wire:loading.attr="disabled" wire:target="updatePersonal">
            <i class="fa-solid fa-check"></i> Save Personal Details
        </button>
    </div>
    @else
    <p class="uop-locked-note">Your role isn't permitted to edit this section. Contact an administrator if this needs to change.</p>
    @endif
</div>

{{-- Academic --}}
<div class="uop-card">
    <div class="uop-card-head">
        <h3><i class="fa-solid fa-graduation-cap"></i> Academic Background</h3>
        <span class="uop-lock-badge {{ $editable['academic'] ? 'open' : 'locked' }}">
            <i class="fa-solid {{ $editable['academic'] ? 'fa-lock-open' : 'fa-lock' }}"></i>
            {{ $editable['academic'] ? 'Editable' : 'Locked by admin' }}
        </span>
    </div>
    <div class="uop-card-body">
        <div class="uop-grid">
            <div class="uop-field">
                <label>Highest qualification</label>
                <input type="text" wire:model="highest_qualification" class="uop-input" @disabled(!$editable['academic'])>
                @error('highest_qualification') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Percentage / CGPA</label>
                <input type="text" wire:model="percentage_cgpa" class="uop-input" @disabled(!$editable['academic'])>
                @error('percentage_cgpa') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Institution name</label>
                <input type="text" wire:model="institution_name" class="uop-input" @disabled(!$editable['academic'])>
                @error('institution_name') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Year of passing</label>
                <input type="text" wire:model="year_of_passing" maxlength="4" class="uop-input" @disabled(!$editable['academic'])>
                @error('year_of_passing') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Experience level</label>
                <select wire:model="experience_level" class="uop-select" @disabled(!$editable['academic'])>
                    <option value="fresher">Fresher</option>
                    <option value="0-1">0–1 year</option>
                    <option value="1-2">1–2 years</option>
                    <option value="2+">2+ years</option>
                </select>
            </div>
            <div></div>
            <div class="uop-field">
                <label>Guardian name</label>
                <input type="text" wire:model="guardian_name" class="uop-input" @disabled(!$editable['academic'])>
                @error('guardian_name') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Guardian mobile</label>
                <input type="text" wire:model="guardian_mobile" maxlength="10" class="uop-input" @disabled(!$editable['academic'])>
                @error('guardian_mobile') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    @if($editable['academic'])
    <div class="uop-card-footer">
        <button type="button" wire:click="updateAcademic" class="uop-btn" wire:loading.attr="disabled" wire:target="updateAcademic">
            <i class="fa-solid fa-check"></i> Save Academic Background
        </button>
    </div>
    @else
    <p class="uop-locked-note">Your role isn't permitted to edit this section. Contact an administrator if this needs to change.</p>
    @endif
</div>

{{-- Program --}}
<div class="uop-card">
    <div class="uop-card-head">
        <h3><i class="fa-solid fa-layer-group"></i> Program Selection</h3>
        <span class="uop-lock-badge {{ $editable['program'] ? 'open' : 'locked' }}">
            <i class="fa-solid {{ $editable['program'] ? 'fa-lock-open' : 'fa-lock' }}"></i>
            {{ $editable['program'] ? 'Editable' : 'Locked by admin' }}
        </span>
    </div>
    <div class="uop-card-body">
        <div class="uop-grid">
            <div class="uop-field">
                <label>Program category</label>
                <select wire:model.live="program_category_id" class="uop-select" @disabled(!$editable['program'])>
                    <option value="">Select</option>
                    @foreach($programCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('program_category_id') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Program</label>
                <select wire:model.live="program_id" class="uop-select" @disabled(!$editable['program'])>
                    <option value="">Select</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}">{{ $prog->title }}</option>
                    @endforeach
                </select>
                @error('program_id') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field">
                <label>Batch</label>
                <select wire:model="batch_id" class="uop-select" @disabled(!$editable['program'])>
                    <option value="">Select</option>
                    @foreach($batches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="uop-field">
                <label>Mode of learning</label>
                <select wire:model="mode_of_learning" class="uop-select" @disabled(!$editable['program'])>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                    <option value="hybrid">Hybrid</option>
                </select>
            </div>
            <div class="uop-field">
                <label>Preferred start date</label>
                <input type="date" wire:model="preferred_start_date" class="uop-input" @disabled(!$editable['program'])>
                @error('preferred_start_date') <span class="uop-error">{{ $message }}</span> @enderror
            </div>
            <div class="uop-field span-2">
                <label>Career goal</label>
                <textarea wire:model="career_goal" rows="2" class="uop-textarea" @disabled(!$editable['program'])></textarea>
            </div>
        </div>
    </div>
    @if($editable['program'])
    <div class="uop-card-footer">
        <button type="button" wire:click="updateProgram" class="uop-btn" wire:loading.attr="disabled" wire:target="updateProgram">
            <i class="fa-solid fa-check"></i> Save Program Selection
        </button>
    </div>
    @else
    <p class="uop-locked-note">Your role isn't permitted to edit this section. Contact an administrator if this needs to change.</p>
    @endif
</div>

</div>

@script
<script>
    const _uopSwalBase = { background: '#111827', color: '#fff', confirmButtonColor: '#6366f1' };
    Livewire.on('swal', (payload) => {
        const e = Array.isArray(payload) ? payload[0] : payload;
        const isSuccess = e?.type === 'success';
        Swal.fire({
            ..._uopSwalBase,
            icon: e?.type ?? 'info',
            title: e?.title ?? 'Notice',
            text: e?.message ?? '',
            iconColor: isSuccess ? '#22c55e' : (e?.type === 'error' ? '#ef4444' : '#6366f1'),
            timer: isSuccess ? 2200 : undefined,
            timerProgressBar: isSuccess,
            showConfirmButton: !isSuccess,
        });
    });
</script>
@endscript