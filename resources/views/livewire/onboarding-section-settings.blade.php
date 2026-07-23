<div class="oss">
<style>
.oss *{ box-sizing:border-box; }
.oss{ display:flex; flex-direction:column; gap:18px; color:var(--text); }
.oss-header h1{ font-size:20px; font-weight:700; color:var(--text-main,var(--text)); }
.oss-header p{ font-size:13px; color:var(--text-muted); margin-top:4px; }

.oss-card{ background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-card); overflow:hidden; }
.oss-table-wrap{ overflow-x:auto; }
table.oss-table{ width:100%; border-collapse:collapse; min-width:560px; }
table.oss-table thead th{
    text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
    color:var(--text-muted); padding:12px 16px; border-bottom:1px solid var(--border); background:var(--bg2);
}
table.oss-table tbody td{ padding:13px 16px; border-bottom:1px solid var(--line,var(--border)); font-size:13.5px; }
table.oss-table tbody tr:last-child td{ border-bottom:none; }
table.oss-table tbody td.role-col{ font-weight:600; color:var(--text); }

.oss-switch{ position:relative; display:inline-block; width:42px; height:23px; cursor:pointer; }
.oss-switch input{ display:none; }
.oss-switch .track{
    position:absolute; inset:0; background:var(--border); border-radius:999px; transition:.2s ease;
}
.oss-switch .thumb{
    position:absolute; top:2px; left:2px; width:19px; height:19px; background:#fff; border-radius:50%;
    transition:.2s ease; box-shadow:0 1px 3px rgba(0,0,0,.25);
}
.oss-switch input:checked + .track{ background:var(--success); }
.oss-switch input:checked + .track .thumb{ transform:translateX(19px); }

.oss-footer{ padding:14px 20px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; }
.oss-btn{ border:none; border-radius:var(--radius-sm); font-weight:600; font-size:.85rem; padding:10px 22px; cursor:pointer; background:linear-gradient(135deg,var(--brand-primary,var(--primary)),var(--brand-secondary,var(--accent2))); color:#fff; display:inline-flex; align-items:center; gap:8px; }
.oss-btn:hover{ opacity:.92; }

.oss-note{ font-size:12px; color:var(--text-muted); padding:14px 20px 0; }
</style>

<div class="oss-header">
    <h1>Onboarding Edit Permissions</h1>
    <p>Choose which onboarding sections each role is allowed to edit after their onboarding is completed.</p>
</div>

<div class="oss-card">
    <div class="oss-table-wrap">
        <table class="oss-table">
            <thead>
                <tr>
                    <th>Role</th>
                    @foreach($sections as $key => $label)
                        <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $roleKey => $roleLabel)
                <tr wire:key="oss-role-{{ $roleKey }}">
                    <td class="role-col">{{ $roleLabel }}</td>
                    @foreach($sections as $secKey => $secLabel)
                    <td>
                        <label class="oss-switch">
                            <input type="checkbox"
                                   @checked($matrix[$roleKey][$secKey] ?? true)
                                   wire:click="toggle('{{ $roleKey }}', '{{ $secKey }}')">
                            <span class="track"><span class="thumb"></span></span>
                        </label>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="oss-note">Toggle on = that role can edit the section from "My Onboarding Details" after submission. Toggle off = locked, view-only.</p>

    <div class="oss-footer">
        <button type="button" wire:click="save" class="oss-btn" wire:loading.attr="disabled" wire:target="save">
            <i class="fa-solid fa-check"></i> Save Permissions
        </button>
    </div>
</div>

</div>

@script
<script>
    const _ossSwalBase = { background: '#111827', color: '#fff', confirmButtonColor: '#6366f1' };
    Livewire.on('swal', (payload) => {
        const e = Array.isArray(payload) ? payload[0] : payload;
        Swal.fire({
            ..._ossSwalBase,
            icon: e?.type ?? 'success',
            title: e?.title ?? 'Saved',
            text: e?.message ?? '',
            iconColor: '#22c55e',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
        });
    });
</script>
@endscript