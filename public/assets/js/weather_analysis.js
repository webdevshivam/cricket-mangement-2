
document.addEventListener('DOMContentLoaded', function() {
    const analyzeBtn = document.getElementById('analyze-weather-btn');
    const citySelect = document.querySelector('select[name="city_name"]');
    const dateInput = document.getElementById('trial_date');
    const weatherCard = document.getElementById('weather-analysis-card');
    const loadingDiv = document.getElementById('weather-loading');
    const resultsDiv = document.getElementById('weather-results');
    
    if (analyzeBtn) {
        analyzeBtn.addEventListener('click', function() {
            const cityName = citySelect ? citySelect.value.trim() : '';
            const trialDate = dateInput.value;
            
            if (!cityName) {
                alert('Please select city name first');
                if (citySelect) citySelect.focus();
                return;
            }
            
            if (!trialDate) {
                alert('Please select trial date first');
                dateInput.focus();
                return;
            }
            
            // Show loading
            weatherCard.style.display = 'block';
            loadingDiv.style.display = 'block';
            resultsDiv.style.display = 'none';
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
            
            // Make AJAX request
            fetch('/index.php/admin/manage-trial-cities/weather-analysis', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `city_name=${encodeURIComponent(cityName)}&trial_date=${encodeURIComponent(trialDate)}`
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.style.display = 'none';
                
                if (data.success) {
                    displayWeatherResults(data.weather, data.analysis);
                    resultsDiv.style.display = 'block';
                } else {
                    alert('Error: ' + (data.error || 'Failed to get weather analysis'));
                    weatherCard.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingDiv.style.display = 'none';
                alert('Failed to connect to weather service');
                weatherCard.style.display = 'none';
            })
            .finally(() => {
                analyzeBtn.disabled = false;
                analyzeBtn.innerHTML = '<i class="fas fa-robot"></i> Get AI Weather Analysis';
            });
        });
    }
    
    function displayWeatherResults(weather, analysis) {
        // Weather summary
        document.getElementById('weather-summary').innerHTML = `
            <div class="bg-dark p-3 rounded">
                <p><strong>City:</strong> ${weather.city}</p>
                <p><strong>Condition:</strong> ${weather.description}</p>
                <p><strong>Temperature:</strong> ${weather.temperature}°C</p>
                <p><strong>Humidity:</strong> ${weather.humidity}%</p>
            </div>
        `;
        
        // Risk assessment
        const riskColor = analysis.risk_level === 'high' ? 'danger' : 
                         analysis.risk_level === 'medium' ? 'warning' : 'success';
        
        document.getElementById('risk-assessment').innerHTML = `
            <div class="bg-dark p-3 rounded">
                <div class="badge bg-${riskColor} fs-6 mb-2">
                    Risk Level: ${analysis.risk_level.toUpperCase()}
                </div>
                ${analysis.should_delay ? 
                    '<div class="badge bg-danger fs-6 ms-2">DELAY RECOMMENDED</div>' : 
                    '<div class="badge bg-success fs-6 ms-2">PROCEED OK</div>'
                }
            </div>
        `;
        
        // Recommendations
        let recommendationsHtml = '<div class="bg-dark p-3 rounded">';
        if (analysis.recommendations.length > 0) {
            analysis.recommendations.forEach(rec => {
                recommendationsHtml += `<div class="alert alert-info mb-2 py-2">${rec}</div>`;
            });
        } else {
            recommendationsHtml += '<p class="text-success">No specific concerns identified.</p>';
        }
        recommendationsHtml += '</div>';
        document.getElementById('ai-recommendations').innerHTML = recommendationsHtml;
        
        // Overall advice
        const adviceColor = analysis.should_delay ? 'danger' : 
                           analysis.risk_level === 'high' ? 'warning' : 'success';
        
        document.getElementById('overall-advice').innerHTML = `
            <div class="alert alert-${adviceColor} mb-0">
                <strong>${analysis.overall_advice}</strong>
            </div>
        `;
    }
});
