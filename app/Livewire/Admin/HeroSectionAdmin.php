<?php



namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\HeroSection;
use App\Models\HeroStat;
use App\Models\HeroRating;

class HeroSectionAdmin extends Component
{
    use WithFileUploads;

    // ── Core fields ──────────────────────────────────────────────
    public ?int   $heroId            = null;
    public        $logo              = null;   // new upload
    public string $logo_path         = '';
    public string $heading_prefix    = '';
    public string $heading_highlight = '';
    public string $heading_bold      = '';
    public string $heading_suffix    = '';
    public string $lede              = '';
    public string $cta_primary_label = '';
    public string $cta_primary_url   = '';
    public string $cta_secondary_label = '';
    public string $cta_secondary_url   = '';
    public        $mascot            = null;   // new upload
    public string $mascot_image      = '';
    public string $guide_tag         = '';
    public string $guide_name        = '';
    public string $guide_text        = '';
    public bool   $is_active         = true;

    // ── Hand images (multiple) ────────────────────────────────────
    public array $hand_images        = [];
    public       $new_hand_images    = [];

    // ── Stats ─────────────────────────────────────────────────────
    public array $stats = [
        ['number' => '', 'label' => '', 'sort_order' => 0],
    ];

    // ── Ratings ───────────────────────────────────────────────────
    public array $ratings = [
        ['score' => '', 'label' => '', 'sort_order' => 0],
    ];

    // ── Validation rules ─────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'heading_prefix'        => 'required|string|max:255',
            'heading_highlight'     => 'nullable|string|max:255',
            'heading_bold'          => 'nullable|string|max:255',
            'heading_suffix'        => 'nullable|string|max:255',
            'lede'                  => 'required|string|max:1000',
            'cta_primary_label'     => 'required|string|max:100',
            'cta_primary_url'       => 'required|url|max:500',
            'cta_secondary_label'   => 'nullable|string|max:100',
            'cta_secondary_url'     => 'nullable|url|max:500',
            'guide_tag'             => 'nullable|string|max:100',
            'guide_name'            => 'nullable|string|max:150',
            'guide_text'            => 'nullable|string|max:500',
            'is_active'             => 'boolean',
            'logo'                  => 'nullable|image|mimes:png,jpg,webp,svg|max:2048',
            'mascot'                => 'nullable|image|mimes:png,jpg,webp,svg|max:4096',
            'new_hand_images.*'     => 'nullable|image|mimes:png,jpg,webp|max:2048',

            // Stats
            'stats'                   => 'array|min:1',
            'stats.*.number'          => 'required|string|max:50',
            'stats.*.label'           => 'required|string|max:100',
            'stats.*.sort_order'      => 'integer|min:0',

            // Ratings
            'ratings'                 => 'array|min:1',
            'ratings.*.score'         => 'required|numeric|min:0|max:10',
            'ratings.*.label'         => 'required|string|max:100',
            'ratings.*.sort_order'    => 'integer|min:0',
        ];
    }

    protected array $messages = [
        'heading_prefix.required'      => 'Heading prefix is required.',
        'lede.required'                => 'The lede (sub-heading) is required.',
        'cta_primary_label.required'   => 'Primary CTA label is required.',
        'cta_primary_url.required'     => 'Primary CTA URL is required.',
        'cta_primary_url.url'          => 'Primary CTA must be a valid URL.',
        'cta_secondary_url.url'        => 'Secondary CTA must be a valid URL.',
        'stats.*.number.required'      => 'Each stat needs a number/value.',
        'stats.*.label.required'       => 'Each stat needs a label.',
        'ratings.*.score.required'     => 'Each rating needs a score.',
        'ratings.*.score.numeric'      => 'Rating score must be numeric.',
        'ratings.*.label.required'     => 'Each rating needs a label.',
    ];

    // ── Boot ─────────────────────────────────────────────────────
    public function mount(): void
    {
        $hero = HeroSection::with(['stats', 'ratings'])->first();

        if ($hero) {
            $this->heroId              = $hero->id;
            $this->logo_path           = $hero->logo_path         ?? '';
            $this->heading_prefix      = $hero->heading_prefix    ?? '';
            $this->heading_highlight   = $hero->heading_highlight ?? '';
            $this->heading_bold        = $hero->heading_bold      ?? '';
            $this->heading_suffix      = $hero->heading_suffix    ?? '';
            $this->lede                = $hero->lede              ?? '';
            $this->cta_primary_label   = $hero->cta_primary_label ?? '';
            $this->cta_primary_url     = $hero->cta_primary_url   ?? '';
            $this->cta_secondary_label = $hero->cta_secondary_label ?? '';
            $this->cta_secondary_url   = $hero->cta_secondary_url   ?? '';
            $this->mascot_image        = $hero->mascot_image      ?? '';
            $this->guide_tag           = $hero->guide_tag         ?? '';
            $this->guide_name          = $hero->guide_name        ?? '';
            $this->guide_text          = $hero->guide_text        ?? '';
            $this->hand_images         = $hero->hand_images       ?? [];
            $this->is_active           = $hero->is_active;

            $this->stats   = $hero->stats->toArray()   ?: [['number' => '', 'label' => '', 'sort_order' => 0]];
            $this->ratings = $hero->ratings->toArray() ?: [['score'  => '', 'label' => '', 'sort_order' => 0]];
        }
    }

    // ── Repeater helpers ─────────────────────────────────────────
    public function addStat(): void
    {
        $this->stats[] = ['number' => '', 'label' => '', 'sort_order' => count($this->stats)];
    }

    public function removeStat(int $index): void
    {
        unset($this->stats[$index]);
        $this->stats = array_values($this->stats);
    }

    public function addRating(): void
    {
        $this->ratings[] = ['score' => '', 'label' => '', 'sort_order' => count($this->ratings)];
    }

    public function removeRating(int $index): void
    {
        unset($this->ratings[$index]);
        $this->ratings = array_values($this->ratings);
    }

    public function removeHandImage(int $index): void
    {
        unset($this->hand_images[$index]);
        $this->hand_images = array_values($this->hand_images);
    }

    // ── Save ─────────────────────────────────────────────────────
    public function save(): void
    {
        $validated = $this->validate();

        // Handle logo upload
        if ($this->logo) {
            $validated['logo_path'] = $this->logo->store('hero/logos', 'public');
        } else {
            $validated['logo_path'] = $this->logo_path;
        }

        // Handle mascot upload
        if ($this->mascot) {
            $validated['mascot_image'] = $this->mascot->store('hero/mascots', 'public');
        } else {
            $validated['mascot_image'] = $this->mascot_image;
        }

        // Handle hand images
        $handPaths = $this->hand_images;
        if (!empty($this->new_hand_images)) {
            foreach ($this->new_hand_images as $img) {
                if ($img) {
                    $handPaths[] = $img->store('hero/hands', 'public');
                }
            }
        }
        $validated['hand_images'] = $handPaths;

        // Persist hero section
        $hero = HeroSection::updateOrCreate(
            ['id' => $this->heroId],
            collect($validated)->except(['stats', 'ratings', 'logo', 'mascot', 'new_hand_images'])->toArray()
        );

        $this->heroId = $hero->id;

        // Sync stats
        $hero->stats()->delete();
        foreach ($this->stats as $i => $stat) {
            $hero->stats()->create([
                'number'     => $stat['number'],
                'label'      => $stat['label'],
                'sort_order' => $i,
            ]);
        }

        // Sync ratings
        $hero->ratings()->delete();
        foreach ($this->ratings as $i => $rating) {
            $hero->ratings()->create([
                'score'      => $rating['score'],
                'label'      => $rating['label'],
                'sort_order' => $i,
            ]);
        }

        // Reset file inputs
        $this->logo           = null;
        $this->mascot         = null;
        $this->new_hand_images = [];

        $this->dispatch('saved');   // picked up by SweetAlert JS
    }

    public function render()
    {
        return view('livewire.admin.hero-section-admin');
    }
}