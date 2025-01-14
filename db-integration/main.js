document.addEventListener('DOMContentLoaded', () => {
    const dobInput = document.querySelector('#dob'); // Date of Birth input
    const ageInput = document.querySelector('#age'); // Age input field  
    
    function calculateAge(dob) {
        const birthDate = new Date(dob);
        const today = new Date();

        let years = today.getFullYear() - birthDate.getFullYear();
        let months = today.getMonth() - birthDate.getMonth();
        let days = today.getDate() - birthDate.getDate();

        if (days < 0) {
            months--;
            days += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
        }
        if (months < 0) {
            years--;
            months += 12;
        }

        return `${years}y, ${months}m, ${days}d old`;
    }

    // Event Listener for DOB change
    dobInput.addEventListener('change', () => {
        const dobValue = dobInput.value;
        if (dobValue) {
            const ageText = calculateAge(dobValue);
            ageInput.value = ageText; 
        }
    });
});

