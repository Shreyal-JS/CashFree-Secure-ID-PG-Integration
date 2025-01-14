/* DOB format fixing script */

// yob = 14/05/2004 (php response from sdk)

let dob = document.querySelector('#dob'); // input tag

async function fixDob() {

    if (yobRes) { // dd/mm/yyyy
        // console.log(yobRes);
        let [dd, mm, yyyy] = yobRes.split("/");
        if (dd && mm && yyyy) {
            dob.value = `${yyyy}-${mm}-${dd}`; // yyyy/mm/dd
            // console.log(yob.value); 
            dob.setAttribute('readonly', '');
            // calculateAge();

        } else {
            alert('Unable to fetch DoB from Aadhaar Card!\nManually fill DOB field.');
            dob.value = "";
            dob.removeAttribute('readonly');
        }

    } else {
        alert("Unable to get DOB from Aadhaar card!\nManually fill DOB field.");
        dob.value = "";
        dob.removeAttribute("readonly");
    }
    if (dob.value) {
        calculateAge();
    } else {
        alert("Couldn't fetch DOB");
        dob.value = "";
        dob.removeAttribute('readonly');
        dob.addEventListener('change', calculateAge);
    }
}

document.addEventListener('DOMContentLoaded', fixDob);
/* End of DOB format fixing script */

/* Age Calculation Script */

async function calculateAge() {
    const showAge = document.getElementById("age");
    if (dob.value) {
        const birthDate = new Date(dob.value);
        const today = new Date();

        let years = today.getFullYear() - birthDate.getFullYear();
        let months = today.getMonth() - birthDate.getMonth();
        let days = today.getDate() - birthDate.getDate();

        if (days < 0) {
            months -= 1;
            days += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
        }

        if (months < 0) {
            years -= 1;
            months += 12;
        }

        // Display age in format "x years y months z days old"
        showAge.value = `${years}y, ${months}m, ${days}d old`;
    } else {
        showAge.value = ""; // Clear display if no date
        alert("Unable to get age");
        dob.value = "";
        dob.removeAttribute("readonly");
    }
}

/* End of Age Calculation Script */

/* Fetch District Script */
function fetchDistrict() {
    const pincode = document.getElementById("pincode").value;
    if (pincode.length === 6) { 
        try {
            // Fetch district information from the API using the pincode
            const response = fetch(`https://api.postalpincode.in/pincode/${pincode}`);
            const data = response.json();

            if (data[0].Status === "Success") {
                const district = data[0].PostOffice[0].District;
                document.getElementById("district").value = district;
            } else {
                alert("Invalid Pincode or no data available");
                document.getElementById("district").value = "";
            }
        } catch (error) {
            console.error("Error fetching district:", error);
            alert("Could not retrieve district information.");
        }
    } else {
        document.getElementById("district").value = ""; // Clear district if pincode is invalid
        alert('Unable to fetch district');
        document.querySelector('#district').removeAttribute('readonly');
    }
}
document.addEventListener("DOMContentLoaded", fetchDistrict);

/*End of Fetch District Script */

/* File Checker script */
function validateFile(input) {
    const maxSize = 2 * 1024 * 1024; // 2 MB
    const allowedExtensions = ["image/jpeg", "image/png", "image/jpg"];
    const file = input.files[0];

    if (file) {
        // Check file size
        if (file.size > maxSize) {
            alert("File size exceeds 2 MB. Please upload a smaller file.");
            input.value = ""; // Clear the input
            return false;
        }

        // Check file type
        if (!allowedExtensions.includes(file.type)) {
            alert("Invalid file type. Only .jpg, .jpeg, and .png files are allowed.");
            input.value = ""; // Clear the input
            return false;
        }
    }
    return true;
}

/* End of File Checker script */
