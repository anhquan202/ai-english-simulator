/**
 * Scenario Catalog Component (Screen 1: Home / Catalog)
 * Manages Category & Level Filtering, Favorites Toggle, and Hover Peek Flyout
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('scenarioCatalog', (config = {}) => ({
        selectedLevel: config.level || 'all',
        selectedCategory: config.category || 'all',
        searchQuery: '',
        sortBy: 'pinned',
        activePeek: null,
        peekData: null,
        favorites: config.favorites || {},
        scenarios: config.scenarios || {},

        async toggleFavorite(scenarioId) {
            const current = !!this.favorites[scenarioId];
            this.favorites[scenarioId] = !current;
            const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
            try {
                const res = await fetch(`/scenarios/${scenarioId}/favorite`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.favorites[scenarioId] = data.is_favorited;
                }
            } catch (err) {
                console.error('Error toggling favorite:', err);
            }
        },

        _peekTimer: null,

        openPeek(id) {
            clearTimeout(this._peekTimer);
            this._peekTimer = setTimeout(() => {
                this.activePeek = id;
                this.peekData = this.scenarios[id] || null;
            }, 150);
        },

        closePeek() {
            clearTimeout(this._peekTimer);
            this.activePeek = null;
        },

        filterMatch(id) {
            const s = this.scenarios[id];
            if (!s) return true;

            const levels = Array.isArray(s.supported_levels) ? s.supported_levels : [];
            const matchLevel = this.selectedLevel === 'all'
                || levels.includes(this.selectedLevel)
                || (this.selectedLevel === 'A1' && (levels.includes('A1') || levels.includes('A2')));

            let matchCategory = this.selectedCategory === 'all' || s.category === this.selectedCategory;
            if (!matchCategory && this.selectedCategory === 'dining' && (s.category === 'daily_life' || s.category === 'dining')) {
                matchCategory = true;
            }

            const query = (this.searchQuery || '').trim().toLowerCase();
            const matchSearch = !query 
                || (s.title && s.title.toLowerCase().includes(query)) 
                || (s.context_note && s.context_note.toLowerCase().includes(query))
                || (s.location_name && s.location_name.toLowerCase().includes(query));

            return matchLevel && matchCategory && matchSearch;
        }
    }));
});
