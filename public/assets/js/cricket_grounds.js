
// Cricket Grounds Management
document.addEventListener('DOMContentLoaded', function() {
    const cityInput = document.getElementById('city_name');
    const bestGroundsBtn = document.getElementById('bestCricketGroundsBtn');
    
    if (cityInput && bestGroundsBtn) {
        // Enable/disable button based on city input
        cityInput.addEventListener('input', function() {
            const cityName = this.value.trim();
            if (cityName.length >= 3) {
                bestGroundsBtn.disabled = false;
                bestGroundsBtn.classList.remove('disabled');
            } else {
                bestGroundsBtn.disabled = true;
                bestGroundsBtn.classList.add('disabled');
            }
        });
        
        // Check initial state for edit form
        if (cityInput.value.trim().length >= 3) {
            bestGroundsBtn.disabled = false;
            bestGroundsBtn.classList.remove('disabled');
        }
    }
});

function showBestCricketGrounds() {
    const cityInput = document.getElementById('city_name');
    const cityName = cityInput.value.trim();
    
    if (!cityName || cityName.length < 3) {
        alert('Please enter a valid city name (minimum 3 characters)');
        cityInput.focus();
        return;
    }
    
    // Update offcanvas title
    document.getElementById('selectedCityName').textContent = cityName;
    
    // Show offcanvas
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('cricketGroundsOffcanvas'));
    offcanvas.show();
    
    // Load cricket grounds data
    loadCricketGrounds(cityName);
}

function loadCricketGrounds(cityName) {
    const loadingDiv = document.getElementById('cricketGroundsLoading');
    const listDiv = document.getElementById('cricketGroundsList');
    
    // Show loading
    loadingDiv.style.display = 'block';
    listDiv.innerHTML = '';
    
    // Simulate API call with mock data (replace with real API integration)
    setTimeout(() => {
        const mockGrounds = getCricketGroundsData(cityName);
        displayCricketGrounds(mockGrounds);
        loadingDiv.style.display = 'none';
    }, 1500);
}

function getCricketGroundsData(cityName) {
    // Mock data - replace with real API integration
    const grounds = {
        'Mumbai': [
            {
                name: 'Oval Maidan Cricket Ground',
                fees: '₹500-800/hour',
                address: 'Churchgate, Mumbai, Maharashtra',
                phone: '+91 98765 43210',
                facilities: ['Floodlights', 'Parking', 'Changing Rooms'],
                rating: 4.5
            },
            {
                name: 'Cross Maidan',
                fees: '₹400-600/hour',
                address: 'Fort, Mumbai, Maharashtra',
                phone: '+91 98765 43211',
                facilities: ['Open Ground', 'Public Access'],
                rating: 4.2
            },
            {
                name: 'Shivaji Park Cricket Ground',
                fees: '₹600-900/hour',
                address: 'Dadar, Mumbai, Maharashtra',
                phone: '+91 98765 43212',
                facilities: ['Floodlights', 'Coaching Available', 'Equipment Rental'],
                rating: 4.7
            }
        ],
        'Delhi': [
            {
                name: 'Feroz Shah Kotla Ground',
                fees: '₹800-1200/hour',
                address: 'Bahadur Shah Zafar Marg, New Delhi',
                phone: '+91 98765 43213',
                facilities: ['Professional Ground', 'Floodlights', 'Stadium Facilities'],
                rating: 4.8
            },
            {
                name: 'Delhi University Cricket Ground',
                fees: '₹300-500/hour',
                address: 'Delhi University, North Campus',
                phone: '+91 98765 43214',
                facilities: ['Student Discount', 'Basic Facilities'],
                rating: 4.1
            }
        ],
        'Bangalore': [
            {
                name: 'M. Chinnaswamy Stadium Practice Ground',
                fees: '₹700-1000/hour',
                address: 'Cubbon Park, Bangalore',
                phone: '+91 98765 43215',
                facilities: ['Professional Nets', 'Coaching', 'Equipment'],
                rating: 4.6
            },
            {
                name: 'KSCA Cricket Ground',
                fees: '₹500-750/hour',
                address: 'Alur Village, Bangalore',
                phone: '+91 98765 43216',
                facilities: ['Multiple Pitches', 'Parking', 'Canteen'],
                rating: 4.4
            }
        ]
    };
    
    // Return grounds for the city or default grounds
    return grounds[cityName] || [
        {
            name: `${cityName} Cricket Club`,
            fees: '₹400-700/hour',
            address: `Central ${cityName}, India`,
            phone: '+91 98765 43217',
            facilities: ['Basic Facilities', 'Local Ground'],
            rating: 4.0
        },
        {
            name: `${cityName} Sports Complex`,
            fees: '₹300-600/hour',
            address: `Sports Complex Area, ${cityName}`,
            phone: '+91 98765 43218',
            facilities: ['Multiple Sports', 'Affordable Rates'],
            rating: 3.8
        }
    ];
}

function displayCricketGrounds(grounds) {
    const listDiv = document.getElementById('cricketGroundsList');
    
    if (!grounds || grounds.length === 0) {
        listDiv.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No cricket grounds found for this city.
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="mb-3">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Found ${grounds.length} cricket ground(s) with affordable rates
            </small>
        </div>
    `;
    
    grounds.forEach((ground, index) => {
        html += `
            <div class="card bg-secondary mb-3 border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title text-warning mb-0">
                            <i class="fas fa-map-marker-alt me-1"></i>${ground.name}
                        </h6>
                        <div class="text-end">
                            <small class="text-warning">
                                ${generateStarRating(ground.rating)} ${ground.rating}
                            </small>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-success">
                            <i class="fas fa-rupee-sign me-1"></i><strong>${ground.fees}</strong>
                        </small>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-light">
                            <i class="fas fa-location-dot me-1"></i>${ground.address}
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-info">
                            <i class="fas fa-phone me-1"></i>${ground.phone}
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted mb-1 d-block">Facilities:</small>
                        <div class="d-flex flex-wrap gap-1">
                            ${ground.facilities.map(facility => 
                                `<span class="badge bg-info text-dark">${facility}</span>`
                            ).join('')}
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="https://maps.google.com/search/${encodeURIComponent(ground.address)}" 
                           target="_blank" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-map me-1"></i>View on Map
                        </a>
                        <a href="tel:${ground.phone}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-phone me-1"></i>Call Now
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    listDiv.innerHTML = html;
}

function generateStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    let stars = '';
    
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star text-warning"></i>';
    }
    
    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt text-warning"></i>';
    }
    
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star text-muted"></i>';
    }
    
    return stars;
}
