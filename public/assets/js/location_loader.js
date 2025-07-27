
/**
 * Location Loader - Handles dynamic loading of Indian states and cities
 */
class LocationLoader {
    constructor() {
        this.states = [];
        this.cities = {};
        this.init();
    }

    init() {
        this.loadStates();
        this.bindEvents();
    }

    /**
     * Load all Indian states
     */
    async loadStates() {
        try {
            const response = await fetch('/index.php/api/states');
            const data = await response.json();
            
            if (data.success) {
                this.states = data.states;
                this.populateStateDropdowns();
            } else {
                console.error('Failed to load states:', data.error);
                this.loadFallbackStates();
            }
        } catch (error) {
            console.error('Error loading states:', error);
            this.loadFallbackStates();
        }
    }

    /**
     * Load cities for a specific state
     */
    async loadCities(stateCode) {
        try {
            // Check cache first
            if (this.cities[stateCode]) {
                return this.cities[stateCode];
            }

            const response = await fetch(`/index.php/api/cities/${stateCode}`);
            const data = await response.json();
            
            if (data.success) {
                this.cities[stateCode] = data.cities;
                return data.cities;
            } else {
                console.error('Failed to load cities:', data.error);
                return [];
            }
        } catch (error) {
            console.error('Error loading cities:', error);
            return [];
        }
    }

    /**
     * Populate all state dropdowns on the page
     */
    populateStateDropdowns() {
        const stateSelects = document.querySelectorAll('select[name="state"], select#state');
        
        stateSelects.forEach(select => {
            // Clear existing options except the first one
            const firstOption = select.querySelector('option[value=""]');
            select.innerHTML = '';
            if (firstOption) {
                select.appendChild(firstOption);
            }

            // Add state options
            this.states.forEach(state => {
                const option = document.createElement('option');
                option.value = state.code;
                option.textContent = state.name;
                select.appendChild(option);
            });
        });
    }

    /**
     * Populate city dropdown for a specific state
     */
    async populateCityDropdown(stateCode, citySelectElement) {
        if (!citySelectElement) return;

        // Show loading
        const loadingOption = document.createElement('option');
        loadingOption.value = '';
        loadingOption.textContent = 'Loading cities...';
        citySelectElement.innerHTML = '';
        citySelectElement.appendChild(loadingOption);

        const cities = await this.loadCities(stateCode);
        
        // Clear loading and add cities
        citySelectElement.innerHTML = '';
        
        // Add default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Select City';
        citySelectElement.appendChild(defaultOption);

        // Add city options
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelectElement.appendChild(option);
        });
    }

    /**
     * Bind events for state change
     */
    bindEvents() {
        document.addEventListener('change', (e) => {
            if (e.target.matches('select[name="state"], select#state')) {
                const stateCode = e.target.value;
                const form = e.target.closest('form');
                
                if (form && stateCode) {
                    // Find city dropdown in the same form
                    const citySelect = form.querySelector('select[name="city_name"], select#city_name, input[name="city_name"]');
                    
                    if (citySelect && citySelect.tagName === 'SELECT') {
                        this.populateCityDropdown(stateCode, citySelect);
                    }
                }
            }
        });
    }

    /**
     * Fallback states if API fails
     */
    loadFallbackStates() {
        this.states = [
            {code: 'AP', name: 'Andhra Pradesh'},
            {code: 'AR', name: 'Arunachal Pradesh'},
            {code: 'AS', name: 'Assam'},
            {code: 'BR', name: 'Bihar'},
            {code: 'CG', name: 'Chhattisgarh'},
            {code: 'DL', name: 'Delhi'},
            {code: 'GA', name: 'Goa'},
            {code: 'GJ', name: 'Gujarat'},
            {code: 'HR', name: 'Haryana'},
            {code: 'HP', name: 'Himachal Pradesh'},
            {code: 'JK', name: 'Jammu and Kashmir'},
            {code: 'JH', name: 'Jharkhand'},
            {code: 'KA', name: 'Karnataka'},
            {code: 'KL', name: 'Kerala'},
            {code: 'MP', name: 'Madhya Pradesh'},
            {code: 'MH', name: 'Maharashtra'},
            {code: 'MN', name: 'Manipur'},
            {code: 'ML', name: 'Meghalaya'},
            {code: 'MZ', name: 'Mizoram'},
            {code: 'NL', name: 'Nagaland'},
            {code: 'OR', name: 'Odisha'},
            {code: 'PB', name: 'Punjab'},
            {code: 'RJ', name: 'Rajasthan'},
            {code: 'SK', name: 'Sikkim'},
            {code: 'TN', name: 'Tamil Nadu'},
            {code: 'TS', name: 'Telangana'},
            {code: 'TR', name: 'Tripura'},
            {code: 'UP', name: 'Uttar Pradesh'},
            {code: 'UK', name: 'Uttarakhand'},
            {code: 'WB', name: 'West Bengal'}
        ];
        this.populateStateDropdowns();
    }

    /**
     * Get state name by code
     */
    getStateName(stateCode) {
        const state = this.states.find(s => s.code === stateCode);
        return state ? state.name : stateCode;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.locationLoader = new LocationLoader();
});
